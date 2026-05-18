<?php

namespace App\Http\Controllers\Agent\Extrait\Deces;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\UserNotification;
use App\Models\ActionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use PDF;
use App\Notifications\GeneralPushNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AgentDecesController extends Controller
{
    public function index(Request $request)
    {
        // ... (votre code index inchangé)
        $admin = Auth::guard('agent')->user();

        $decesQuery = Deces::where('commune', $admin->communeM)
            ->where('agent_id', $admin->id)
            ->paye()
            ->where(function ($query) {
                $query->whereNull('statut_livraison')
                    ->orWhere('statut_livraison', '!=', 'livré');
            })
            ->where('etat', '!=', 'rejetée')
            ->with('user');

        if ($request->filled('searchType') && $request->filled('searchInput')) {
            if ($request->searchType === 'nomDefunt') {
                $decesQuery->where('nomDefunt', 'like', '%' . $request->searchInput . '%');
            } elseif ($request->searchType === 'nomHopital') {
                $decesQuery->where('nomHopital', 'like', '%' . $request->searchInput . '%');
            }
        }
        $deces = $decesQuery->paginate(10);

        return view('agent.extraits.deces.deces', compact('deces'));
    }

    // Deces edit 
    public function edit($id)
    {
        $deces = Deces::findOrFail($id);
        $isDiaspora = $deces->user->diaspora ?? false;
        $etats = ['réçu', 'terminé', 'rejetée'];
        return view('agent.extraits.deces.edit_etat', compact('deces', 'etats', 'isDiaspora'));
    }

    public function updateEtat(Request $request, $id)
    {
        $deces = Deces::with('user')->findOrFail($id);

        $request->validate([
            'etat' => 'required|string|in:en attente,réçu,terminé,rejetée',
            'livraison_id' => 'nullable|exists:postes,id',
            'motif_champs' => 'required_if:etat,rejetée|array',
            'motif_commentaire' => 'nullable|string',
            'motif_de_rejet' => 'nullable|string',
        ]);

        $deces->etat = $request->etat;

        if ($deces->etat === 'rejetée') {
            // Construire le motif structuré
            $motif = $this->construireMotifRejet($request);
            $deces->motif_de_rejet = $motif;

            // Enregistrer les champs à modifier
            $deces->champs_a_modifier = json_encode($request->motif_champs);
            $deces->peut_modifier = true; // Activer la possibilité de modifier

            // Annuler toute livraison
            $deces->statut_livraison = null;
            $deces->livraison_code = null;
            $deces->qr_code_path = null;
            $deces->livraison_id = null;
            $deces->dhl_id = null;
            $deces->agent_id = null;
            $deces->livreur_id = null;
        } elseif ($deces->etat === 'terminé') {
            // Logique existante pour 'terminé'
            if ($deces->choix_option === 'livraison' && is_null($deces->livraison_code)) {
                $livraisonCode = 'LIVD' . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
                while (Deces::where('livraison_code', $livraisonCode)->exists()) {
                    $livraisonCode = 'LIVD' . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
                }
                $qrCodePath = $this->generateQrCode($livraisonCode);
                $deces->statut_livraison = 'en attente';
                $deces->livraison_code = $livraisonCode;
                $deces->qr_code_path = $qrCodePath;
                $deces->livraison_id = 1;
                $deces->dhl_id = null;
            }

            // Désactiver la modification pour les demandes terminées
            $deces->champs_a_modifier = null;
            $deces->peut_modifier = false;
            $deces->motif_de_rejet = null;
        } else {
            // Pour 'en attente' ou 'réçu', désactiver la modification
            $deces->champs_a_modifier = null;
            $deces->peut_modifier = false;
            $deces->motif_de_rejet = null;
        }

        // Sauvegarder l'ancien état avant modification
        $ancienEtat = $deces->getOriginal('etat') ?? 'nouveau';
        $user = $deces->user;
        $deces->save();

        // =================================================================
        // CRÉATION DE NOTIFICATION WEB POUR L'UTILISATEUR
        // =================================================================
        if ($user && $ancienEtat !== $deces->etat) {
            UserNotification::notifyStatusChange(
                $user->id,
                'deces',
                $deces->id,
                $deces->reference,
                $ancienEtat,
                $deces->etat
            );
        }

        // =================================================================
        // ENREGISTREMENT DANS L'HISTORIQUE DES ACTIONS
        // =================================================================
        $action = $deces->etat === 'rejetée' ? 'rejet' : 'changement_etat';
        $champsModifies = null;
        if ($deces->etat === 'rejetée' && $request->motif_champs) {
            $champsModifies = $request->motif_champs;
        }
        
        ActionHistory::logAction(
            'deces',
            $deces->id,
            $deces->reference,
            $action,
            $ancienEtat,
            $deces->etat,
            $deces->motif_de_rejet,
            $champsModifies,
            null
        );

        // Notification push + DB
        $user = $deces->user;
        $pushTitle = '';
        $pushBody  = '';

        switch ($deces->etat) {
            case 'réçu':
                $pushTitle = 'Demande reçue';
                $pushBody  = "Votre demande d’extrait de décès a été reçue et sera traitée dans les plus brefs délais.";
                break;
            case 'terminé':
                $pushTitle = 'Demande traitée ✔';
                $pushBody  = "Votre demande d’extrait de décès a été traitée.";
                if ($deces->livraison_code) {
                    $pushBody .= ' Code de livraison : ' . $deces->livraison_code;
                }
                break;
            case 'rejetée':
                $pushTitle = 'Demande rejetée — Modification requise';
                $pushBody  = "Votre demande d’extrait de décès n’a pas pu être traitée. Veuillez corriger les informations.";
                break;
        }

        if ($user && !empty($pushTitle)) {
            $user->notify(new GeneralPushNotification(
                $pushTitle,
                $pushBody,
                ['type' => 'tracking', 'reference' => $deces->reference, 'url' => 'plateauapps://demande?reference=' . $deces->reference]
            ));
        }

        // Redirection en fonction de l'état
        if ($deces->etat === 'terminé') {
            return redirect()->route('agent.demandes.deces.index')
                ->with('success', 'État mis à jour avec succès' .
                    ($deces->choix_option === 'livraison' ? ' et livraison initiée (Code: ' . $deces->livraison_code . ')' : ''));
        } else {
            return redirect()->route('agent.demandes.deces.index')
                ->with('success', 'État mis à jour avec succès');
        }
    }

    /**
     * Construire le motif de rejet structuré
     */
    private function construireMotifRejet(Request $request)
    {
        // Mapping des champs vers leurs libellés
        $labels = [
            'name' => 'Nom et Prénoms du Défunt',
            'numberR' => 'Numéro de Registre',
            'dateR' => 'Date de Registre',
            'commune' => 'Commune',
            'quantite' => 'Quantité',
            'CNIdfnt' => 'CNI/extrait de naissance du défunt',
            'CNIdcl' => 'Certificat médical de décès',
            'documentMariage' => 'Document de mariage',
            'RequisPolice' => 'Réquisition de police'
        ];

        $motif = "Les champs suivants contiennent des informations incorrectes ou incomplètes :\n\n";

        // Ajouter les champs cochés
        foreach ($request->motif_champs ?? [] as $champ) {
            if (isset($labels[$champ])) {
                $motif .= "• " . $labels[$champ] . "\n";
            }
        }

        // Ajouter le commentaire additionnel si présent
        if (!empty($request->motif_commentaire)) {
            $motif .= "\nCommentaire additionnel : " . $request->motif_commentaire;
        }

        return $motif;
    }

    private function generateQrCode($livraisonCode)
    {
        // ... (votre code inchangé)
        $qrCode = new QrCode($livraisonCode);
        $qrCode->setSize(300);
        $qrCode->setMargin(10);
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::High);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $fileName = 'qrcodes/' . $livraisonCode . '.png';
        Storage::disk('public')->put($fileName, $result->getString());
        return $fileName;
    }


    public function markAsDeliveredDeces(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'statut_livraison' => 'required|string|in:livré',
            'reference' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $deces = Deces::with('user')->find($id);
        if (!$deces) {
            return response()->json(['error' => 'Demande non trouvée'], 404);
        }
        if ($deces->reference !== $request->reference) {
            return response()->json(['error' => 'Référence incorrecte'], 400);
        }
        
        // Sauvegarder l'ancien statut
        $ancienStatut = $deces->statut_livraison ?? 'en attente';
        $user = $deces->user;
        
        $deces->statut_livraison = $request->statut_livraison;
        $deces->save();

        // =================================================================
        // CRÉATION DE NOTIFICATION WEB POUR L'UTILISATEUR
        // =================================================================
        if ($user) {
            UserNotification::notifyStatusChange(
                $user->id,
                'deces',
                $deces->id,
                $deces->reference,
                $ancienStatut,
                'livré'
            );
        }

        // Notification push + DB
        if ($user) {
            $user->notify(new GeneralPushNotification(
                "Retrait d’extrait de décès",
                'La récupération de votre extrait de décès a été effectuée avec succès.',
                ['type' => 'tracking', 'reference' => $deces->reference, 'url' => 'plateauapps://demande?reference=' . $deces->reference]
            ));
        }

        return response()->json(['success' => 'La demande a été marquée comme livrée.']);
    }

    public function downloadDeliveryInfo($id)
    {
        // ... (votre code inchangé)
        $naissance = Deces::with(['user'])->findOrFail($id);
        $data = [
            'naissance' => $naissance,
            'livraison' => $naissance->livraison,
            'date' => now()->format('d/m/Y'),
        ];
        $pdf = PDF::loadView('agent.pdf.delivery-info-deces', $data)
            ->setPaper('a6', 'landscape')
            ->setOption('isRemoteEnabled', true);
        return $pdf->download('etiquette-livraison-' . $naissance->livraison_code . '.pdf');
    }


}
