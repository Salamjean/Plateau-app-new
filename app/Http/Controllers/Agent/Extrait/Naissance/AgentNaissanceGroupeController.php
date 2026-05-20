<?php

namespace App\Http\Controllers\Agent\Extrait\Naissance;

use App\Http\Controllers\Controller;
use App\Models\NaissanceGroupe;
use App\Models\Naissance;
use App\Models\UserNotification;
use App\Models\ActionHistory;
use App\Notifications\GeneralPushNotification;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\YellikaSmsService;

/**
 * Gère le traitement par l'agent des demandes groupées d'actes de naissance.
 *
 * Logique métier :
 *   - Pour chaque ligne du groupe, l'agent peut choisir : "valider" ou "rejeter"
 *   - Si AU MOINS UNE ligne est rejetée → TOUT le groupe est rejeté
 *     (mais le motif n'est inscrit que sur les lignes effectivement rejetées
 *      par l'agent)
 *   - Si TOUTES sont validées → tout le groupe passe à "terminé"
 *     + génération d'un code de livraison unique pour le groupe (si livraison)
 */
class AgentNaissanceGroupeController extends Controller
{
    /**
     * Liste des groupes en cours pour l'agent (par commune).
     */
    public function index()
    {
        $admin = Auth::guard('agent')->user();

        $groupes = NaissanceGroupe::where('commune', $admin->communeM)
            ->paye()
            ->where(function ($q) {
                $q->whereNull('statut_livraison')
                    ->orWhere('statut_livraison', '!=', 'livré');
            })
            ->where('etat', '!=', 'rejetée')
            ->with(['user', 'lignes'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('agent.extraits.naissances.groupes_index', compact('groupes'));
    }

    /**
     * Affiche le détail d'un groupe avec toutes ses lignes
     * et le formulaire de traitement.
     */
    public function show($id)
    {
        $groupe = NaissanceGroupe::with(['lignes', 'user'])->findOrFail($id);

        return view('agent.extraits.naissances.groupe_detail', compact('groupe'));
    }

    /**
     * Traite le groupe : applique la décision de l'agent ligne par ligne.
     *
     * Format attendu de la requête :
     *   - decisions[ligne_id] : 'valider' | 'rejeter'
     *   - motifs[ligne_id]    : string (motif si rejeter)
     *   - motif_global        : string (commentaire général)
     */
    public function process(Request $request, $id)
    {
        $request->validate([
            'decisions'             => 'required|array|min:1',
            'decisions.*'           => 'required|in:valider,rejeter',
            'motifs'                => 'nullable|array',
            'motifs.*'              => 'nullable|string|max:1000',
            'motif_global'          => 'nullable|string|max:1000',
        ]);

        $groupe = NaissanceGroupe::with('lignes')->findOrFail($id);
        $agent  = Auth::guard('agent')->user();
        $decisions = $request->input('decisions');
        $motifs    = $request->input('motifs', []);

        // Vérifier qu'il y a une décision pour chaque ligne du groupe
        foreach ($groupe->lignes as $ligne) {
            if (!isset($decisions[$ligne->id])) {
                return back()->with('error', "Veuillez prendre une décision pour chaque acte (ligne #{$ligne->position_in_groupe} manquante).");
            }
        }

        // Y a-t-il au moins un rejet ?
        $auMoinsUnRejet = in_array('rejeter', $decisions, true);

        try {
            DB::beginTransaction();

            $ancienEtatGroupe = $groupe->etat;

            if ($auMoinsUnRejet) {
                // ─── REJET GLOBAL ───
                $this->rejeterGroupe($groupe, $decisions, $motifs, $request->input('motif_global'), $agent);
                $messageSuccess = "Demande groupée rejetée. L'utilisateur a été notifié des motifs.";
            } else {
                // ─── VALIDATION TOTALE ───
                $this->validerGroupe($groupe, $agent);
                $messageSuccess = "Demande groupée validée. Tous les actes sont marqués comme terminés.";
                if ($groupe->livraison_code) {
                    $messageSuccess .= " Code de livraison : {$groupe->livraison_code}";
                }
            }

            // Notifications utilisateur
            $this->notifierUtilisateur($groupe, $auMoinsUnRejet);

            // Historique
            ActionHistory::logAction(
                'naissance_groupe',
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

            return redirect()->route('agent.demandes.naissance.groupes.index')->with('success', $messageSuccess);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur traitement groupe naissance ' . $groupe->reference . ' : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du traitement : ' . $e->getMessage());
        }
    }

    /**
     * Rejette tout le groupe avec motif sur les lignes concernées.
     */
    private function rejeterGroupe(NaissanceGroupe $groupe, array $decisions, array $motifs, ?string $motifGlobal, $agent): void
    {
        foreach ($groupe->lignes as $ligne) {
            $ligne->etat = 'rejetée';
            $ligne->agent_id = $agent->id;

            $decision = $decisions[$ligne->id] ?? null;
            if ($decision === 'rejeter') {
                $motif = trim($motifs[$ligne->id] ?? '');
                if ($motifGlobal) {
                    $motif = $motif ? $motif . "\n\n" . $motifGlobal : $motifGlobal;
                }
                $ligne->motif_de_rejet = $motif ?: 'Document rejeté par l\'agent (sans précision).';
            } else {
                // Lignes que l'agent avait validées mais qui sont rejetées en bloc
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
            $message = "Bonjour {$user->name}, votre demande groupée d'extraits de naissance (Réf: {$groupe->reference}) a été rejetée. Veuillez consulter l'application pour plus de détails.";
            try {
                $yellikaSmsService = app(YellikaSmsService::class);
                $yellikaSmsService->sendSms($phoneNumber, $message);
            } catch (\Exception $e) {
                Log::error("Erreur lors de l'envoi du SMS de rejet (naissance groupe) : " . $e->getMessage());
            }
        }
    }

    /**
     * Valide tout le groupe et génère le code de livraison si nécessaire.
     */
    private function validerGroupe(NaissanceGroupe $groupe, $agent): void
    {
        // Mettre toutes les lignes à "terminé"
        foreach ($groupe->lignes as $ligne) {
            $ligne->etat = 'terminé';
            $ligne->agent_id = $agent->id;
            $ligne->motif_de_rejet = null;
            $ligne->save();
        }

        $groupe->etat = 'terminé';
        $groupe->agent_id = $agent->id;

        // Générer code livraison + QR si livraison
        if ($groupe->choix_option === 'livraison' && is_null($groupe->livraison_code)) {
            $code = 'LIVNG' . str_pad((string) random_int(1, 9999999), 7, '0', STR_PAD_LEFT);
            while (NaissanceGroupe::where('livraison_code', $code)->exists()) {
                $code = 'LIVNG' . str_pad((string) random_int(1, 9999999), 7, '0', STR_PAD_LEFT);
            }

            $groupe->livraison_code = $code;
            $groupe->qr_code_path = $this->generateQrCode($code);
            $groupe->statut_livraison = 'en attente';
            $groupe->livraison_id = 1;
        }

        $groupe->save();
    }

    /**
     * Génère le QR code du livraison_code.
     */
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

    /**
     * Envoie les notifications (web + push) à l'utilisateur.
     */
    private function notifierUtilisateur(NaissanceGroupe $groupe, bool $rejete): void
    {
        $user = $groupe->user;
        if (!$user) return;

        // Notification DB
        UserNotification::notifyStatusChange(
            $user->id,
            'naissance_groupe',
            $groupe->id,
            $groupe->reference,
            'en attente',
            $groupe->etat
        );

        // Notification push
        if ($rejete) {
            $title = 'Demande groupée rejetée';
            $body  = "Votre demande groupée d'actes de naissance ({$groupe->reference}) a été rejetée. Veuillez consulter les motifs.";
        } else {
            $title = 'Demande groupée traitée ✔';
            $body  = "Votre demande groupée d'actes de naissance ({$groupe->reference}) a été validée.";
            if ($groupe->livraison_code) {
                $body .= ' Code de livraison : ' . $groupe->livraison_code;
            }
        }

        $user->notify(new GeneralPushNotification(
            $title,
            $body,
            ['type' => 'tracking', 'reference' => $groupe->reference, 'url' => 'plateauapps://demande?reference=' . $groupe->reference]
        ));
    }
}
