<?php

namespace App\Http\Controllers\Agent\Extrait\Mariage;

use App\Http\Controllers\Controller;
use App\Models\MariageGroupe;
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
use Illuminate\Support\Facades\Mail;
use App\Mail\DemandeRejeteeMail;

/**
 * Traitement par l'agent des demandes groupées de mariage.
 * Logique "tout ou rien" : si UNE ligne rejetée, TOUT le groupe rejeté.
 */
class AgentMariageGroupeController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('agent')->user();

        $groupes = MariageGroupe::where('commune', $admin->communeM)
            ->paye()
            ->where(function ($q) {
                $q->whereNull('statut_livraison')->orWhere('statut_livraison', '!=', 'livré');
            })
            ->where('etat', '!=', 'rejetée')
            ->with(['user', 'lignes'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('agent.extraits.mariages.groupes_index', compact('groupes'));
    }

    public function show($id)
    {
        $groupe = MariageGroupe::with(['lignes', 'user'])->findOrFail($id);
        return view('agent.extraits.mariages.groupe_detail', compact('groupe'));
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

        $groupe = MariageGroupe::with('lignes')->findOrFail($id);
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
                $messageSuccess = "Demande groupée mariage rejetée. L'utilisateur a été notifié.";
            } else {
                $this->validerGroupe($groupe, $agent);
                $messageSuccess = "Demande groupée mariage validée.";
                if ($groupe->livraison_code) {
                    $messageSuccess .= " Code de livraison : {$groupe->livraison_code}";
                }
            }

            $this->notifierUtilisateur($groupe, $auMoinsUnRejet);

            ActionHistory::logAction(
                'mariage_groupe',
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
            return redirect()->route('agent.demandes.mariage.groupes.index')->with('success', $messageSuccess);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur traitement groupe mariage ' . $groupe->reference . ' : ' . $e->getMessage());
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    private function rejeterGroupe(MariageGroupe $groupe, array $decisions, array $motifs, ?string $motifGlobal, $agent): void
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
            if ($groupe->choix_option === 'livraison') {
                $phoneNumber = $groupe->contact_destinataire;
                $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
                if (!str_starts_with($phoneNumber, '225') && strlen($phoneNumber) == 10) {
                    $phoneNumber = '225' . $phoneNumber;
                }
                $destName = trim($groupe->prenom_destinataire . ' ' . $groupe->nom_destinataire);

                // Récupérer les motifs de rejet de chaque ligne
                $motifsRejet = [];
                foreach ($groupe->lignes as $ligne) {
                    if (($decisions[$ligne->id] ?? null) === 'rejeter') {
                        $motifsRejet[] = "• Acte #" . $ligne->position_in_groupe . " (" . $ligne->nomEpoux . " & " . $ligne->nomEpouse . ") : " . ($motifs[$ligne->id] ?? 'sans précision');
                    }
                }
                if ($motifGlobal) {
                    $motifsRejet[] = "Commentaire général : " . $motifGlobal;
                }
                $emailMotif = implode("\n", $motifsRejet);

                // Envoi d'un e-mail de notification de rejet au demandeur
                try {
                    Mail::to($user->email)->send(new DemandeRejeteeMail($user, $groupe, 'mariage_groupe', $emailMotif));
                } catch (\Exception $e) {
                    Log::error("Erreur lors de l'envoi du mail de rejet (mariage groupe) : " . $e->getMessage());
                }
            } else {
                $phoneNumber = $user->indicatif . $user->contact;
                $destName = $user->name;
            }
            $message = "Bonjour {$destName}, votre demande groupée d'extraits de mariage (Réf: {$groupe->reference}) a été rejetée. Veuillez consulter l'application pour plus de détails.";
            try {
                $yellikaSmsService = app(YellikaSmsService::class);
                $yellikaSmsService->sendSms($phoneNumber, $message);
            } catch (\Exception $e) {
                Log::error("Erreur lors de l'envoi du SMS de rejet (mariage groupe) : " . $e->getMessage());
            }
        }
    }

    private function validerGroupe(MariageGroupe $groupe, $agent): void
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
            $code = 'LIVMG' . str_pad((string) random_int(1, 9999999), 7, '0', STR_PAD_LEFT);
            while (MariageGroupe::where('livraison_code', $code)->exists()) {
                $code = 'LIVMG' . str_pad((string) random_int(1, 9999999), 7, '0', STR_PAD_LEFT);
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

    private function notifierUtilisateur(MariageGroupe $groupe, bool $rejete): void
    {
        $user = $groupe->user;
        if (!$user) return;

        UserNotification::notifyStatusChange($user->id, 'mariage_groupe', $groupe->id, $groupe->reference, 'en attente', $groupe->etat);

        if ($rejete) {
            $title = 'Demande groupée mariage rejetée';
            $body = "Votre demande groupée d'actes de mariage ({$groupe->reference}) a été rejetée.";
        } else {
            $title = 'Demande groupée mariage traitée ✔';
            $body = "Votre demande groupée d'actes de mariage ({$groupe->reference}) a été validée.";
            if ($groupe->livraison_code) $body .= ' Code de livraison : ' . $groupe->livraison_code;
        }

        $user->notify(new GeneralPushNotification($title, $body, [
            'type' => 'tracking',
            'reference' => $groupe->reference,
            'url' => 'plateauapps://demande?reference=' . $groupe->reference,
        ]));
    }
}
