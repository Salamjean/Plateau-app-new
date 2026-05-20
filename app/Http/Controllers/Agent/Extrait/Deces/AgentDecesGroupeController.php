<?php

namespace App\Http\Controllers\Agent\Extrait\Deces;

use App\Http\Controllers\Controller;
use App\Models\DecesGroupe;
use App\Models\UserNotification;
use App\Models\ActionHistory;
use App\Notifications\GeneralPushNotification;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\YellikaSmsService;

/**
 * Traitement par l'agent des demandes groupées de décès.
 * Logique "tout ou rien" : si UNE ligne rejetée, TOUT le groupe rejeté.
 */
class AgentDecesGroupeController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('agent')->user();

        $groupes = DecesGroupe::where('commune', $admin->communeM)
            ->paye()
            ->where(function ($q) {
                $q->whereNull('statut_livraison')->orWhere('statut_livraison', '!=', 'livré');
            })
            ->where('etat', '!=', 'rejetée')
            ->with(['user', 'lignes'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('agent.extraits.deces.groupes_index', compact('groupes'));
    }

    public function show($id)
    {
        $groupe = DecesGroupe::with(['lignes', 'user'])->findOrFail($id);
        return view('agent.extraits.deces.groupe_detail', compact('groupe'));
    }

    public function process(Request $request, $id)
    {
        $request->validate([
            'decisions'    => 'required|array|min:1',
            'decisions.*'  => 'required|in:valider,rejeter',
            'motifs'       => 'nullable|array',
            'motifs.*'     => 'nullable|string|max:1000',
            'motif_global' => 'nullable|string|max:1000',
        ]);

        $groupe = DecesGroupe::with('lignes')->findOrFail($id);
        $agent = Auth::guard('agent')->user();
        $decisions = $request->input('decisions');
        $motifs = $request->input('motifs', []);

        foreach ($groupe->lignes as $ligne) {
            if (!isset($decisions[$ligne->id])) {
                return back()->with('error', "Décision manquante pour la ligne #{$ligne->position_in_groupe}.");
            }
        }

        $auMoinsUnRejet = in_array('rejeter', $decisions, true);

        try {
            DB::beginTransaction();
            $ancienEtatGroupe = $groupe->etat;

            if ($auMoinsUnRejet) {
                $this->rejeterGroupe($groupe, $decisions, $motifs, $request->input('motif_global'), $agent);
                $messageSuccess = "Demande groupée décès rejetée. L'utilisateur a été notifié.";
            } else {
                $this->validerGroupe($groupe, $agent);
                $messageSuccess = "Demande groupée décès validée.";
                if ($groupe->livraison_code) {
                    $messageSuccess .= " Code de livraison : {$groupe->livraison_code}";
                }
            }

            $this->notifierUtilisateur($groupe, $auMoinsUnRejet);

            ActionHistory::logAction(
                'deces_groupe',
                $groupe->id,
                $groupe->reference,
                $auMoinsUnRejet ? 'rejet' : 'changement_etat',
                $ancienEtatGroupe,
                $groupe->etat,
                $auMoinsUnRejet ? 'Rejet global suite à motif sur au moins une ligne' : null,
                null,
                null
            );

            DB::commit();
            return redirect()->route('agent.demandes.deces.groupes.index')->with('success', $messageSuccess);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur traitement groupe deces ' . $groupe->reference . ' : ' . $e->getMessage());
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    private function rejeterGroupe(DecesGroupe $groupe, array $decisions, array $motifs, ?string $motifGlobal, $agent): void
    {
        foreach ($groupe->lignes as $ligne) {
            $ligne->etat = 'rejetée';
            $ligne->agent_id = $agent->id;
            $decision = $decisions[$ligne->id] ?? null;
            if ($decision === 'rejeter') {
                $motif = trim($motifs[$ligne->id] ?? '');
                if ($motifGlobal) $motif = $motif ? $motif . "\n\n" . $motifGlobal : $motifGlobal;
                $ligne->motif_de_rejet = $motif ?: 'Document rejeté par l\'agent.';
            } else {
                $ligne->motif_de_rejet = "Cette ligne a été validée par l'agent mais la demande globale est rejetée car au moins un autre document n'est pas conforme.";
            }
            $ligne->save();
        }
        $groupe->etat = 'rejetée';
        $groupe->agent_id = $agent->id;
        $groupe->save();

        // Envoi SMS
        $user = $groupe->user;
        if ($user) {
            $phoneNumber = $user->indicatif . $user->contact;
            $message = "Bonjour {$user->name}, votre demande groupée d'extraits de décès (Réf: {$groupe->reference}) a été rejetée. Veuillez consulter l'application pour plus de détails.";
            try {
                $yellikaSmsService = app(YellikaSmsService::class);
                $yellikaSmsService->sendSms($phoneNumber, $message);
            } catch (\Exception $e) {
                Log::error("Erreur lors de l'envoi du SMS de rejet (décès groupe) : " . $e->getMessage());
            }
        }
    }

    private function validerGroupe(DecesGroupe $groupe, $agent): void
    {
        foreach ($groupe->lignes as $ligne) {
            $ligne->etat = 'terminé';
            $ligne->agent_id = $agent->id;
            $ligne->motif_de_rejet = null;
            $ligne->save();
        }

        $groupe->etat = 'terminé';
        $groupe->agent_id = $agent->id;

        if ($groupe->choix_option === 'livraison' && is_null($groupe->livraison_code)) {
            $code = 'LIVDG' . str_pad((string) random_int(1, 9999999), 7, '0', STR_PAD_LEFT);
            while (DecesGroupe::where('livraison_code', $code)->exists()) {
                $code = 'LIVDG' . str_pad((string) random_int(1, 9999999), 7, '0', STR_PAD_LEFT);
            }
            $groupe->livraison_code = $code;
            $groupe->qr_code_path = $this->generateQrCode($code);
            $groupe->statut_livraison = 'en attente';
            $groupe->livraison_id = 1;
        }
        $groupe->save();
    }

    private function generateQrCode(string $code): string
    {
        $qr = new QrCode($code);
        $qr->setSize(300);
        $qr->setMargin(10);
        $qr->setErrorCorrectionLevel(ErrorCorrectionLevel::High);
        $writer = new PngWriter();
        $result = $writer->write($qr);
        $fileName = 'qrcodes/' . $code . '.png';
        Storage::disk('public')->put($fileName, $result->getString());
        return $fileName;
    }

    private function notifierUtilisateur(DecesGroupe $groupe, bool $rejete): void
    {
        $user = $groupe->user;
        if (!$user) return;

        UserNotification::notifyStatusChange($user->id, 'deces_groupe', $groupe->id, $groupe->reference, 'en attente', $groupe->etat);

        if ($rejete) {
            $title = 'Demande groupée décès rejetée';
            $body = "Votre demande groupée d'actes de décès ({$groupe->reference}) a été rejetée.";
        } else {
            $title = 'Demande groupée décès traitée ✔';
            $body = "Votre demande groupée d'actes de décès ({$groupe->reference}) a été validée.";
            if ($groupe->livraison_code) $body .= ' Code de livraison : ' . $groupe->livraison_code;
        }

        $user->notify(new GeneralPushNotification($title, $body, [
            'type' => 'tracking',
            'reference' => $groupe->reference,
            'url' => 'plateauapps://demande?reference=' . $groupe->reference,
        ]));
    }
}
