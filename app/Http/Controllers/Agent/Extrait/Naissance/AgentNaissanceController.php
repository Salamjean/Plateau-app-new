<?php

namespace App\Http\Controllers\Agent\Extrait\Naissance;

use App\Http\Controllers\Controller;
use App\Models\Naissance;
use App\Models\UserNotification;
use App\Models\ActionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Support\Facades\Storage;
use PDF;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; 

class AgentNaissanceController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('agent')->user();

        $naissances = Naissance::where('commune', $admin->communeM)
            ->where('agent_id', $admin->id)
            ->paye()
            ->where(function ($query) {
                $query->whereNull('statut_livraison')
                    ->orWhere('statut_livraison', '!=', 'livré');
            })
            ->where('etat', '!=', 'rejetée')
            ->with('user')
            ->paginate(10);

        return view('agent.extraits.naissances.naissance', compact('naissances'));
    }


    public function edit($id)
    {
        $naissance = Naissance::findOrFail($id);

        // Récupérer le statut diaspora de l'utilisateur
        $isDiaspora = $naissance->user->diaspora ?? false;

        $etats = ['en attente', 'réçu', 'terminé', 'rejetée'];

        return view('agent.extraits.naissances.edit_etat', compact('naissance', 'etats', 'isDiaspora'));
    }

    public function updateEtat(Request $request, $id)
    {
        $naissance = Naissance::with('user')->findOrFail($id);

        $request->validate([
            'etat' => 'required|string|in:en attente,réçu,terminé,rejetée',
            'livraison_id' => 'nullable|exists:postes,id',
            'motif_champs' => 'required_if:etat,rejetée|array',
            'motif_commentaire' => 'nullable|string',
            'motif_de_rejet' => 'nullable|string',
        ]);

        $naissance->etat = $request->etat;

        if ($naissance->etat === 'rejetée') {
            // Construire le motif structuré
            $motif = $this->construireMotifRejet($request);
            $naissance->motif_de_rejet = $motif;

            // Enregistrer les champs à modifier
            $naissance->champs_a_modifier = json_encode($request->motif_champs);
            $naissance->peut_modifier = true; // Activer la possibilité de modifier

            // Annuler toute livraison
            $naissance->statut_livraison = null;
            $naissance->livraison_code = null;
            $naissance->qr_code_path = null;
            $naissance->livraison_id = null;
            $naissance->dhl_id = null;
            $naissance->livreur_id = null;
            $naissance->agent_id = null;
        } elseif ($naissance->etat === 'terminé') {
            // Logique existante pour 'terminé'
            if ($naissance->choix_option === 'livraison' && is_null($naissance->livraison_code)) {
                $livraisonCode = 'LIVN' . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);

                while (Naissance::where('livraison_code', $livraisonCode)->exists()) {
                    $livraisonCode = 'LIVN' . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
                }

                $qrCodePath = $this->generateQrCode($livraisonCode);

                $naissance->statut_livraison = 'en attente';
                $naissance->livraison_code = $livraisonCode;
                $naissance->qr_code_path = $qrCodePath;
                $naissance->livraison_id = 1;
                $naissance->dhl_id = null;
            }

            // Désactiver la modification pour les demandes terminées
            $naissance->champs_a_modifier = null;
            $naissance->peut_modifier = false;
            $naissance->motif_de_rejet = null;
        } else {
            // Pour 'en attente' ou 'réçu', désactiver la modification
            $naissance->champs_a_modifier = null;
            $naissance->peut_modifier = false;
            $naissance->motif_de_rejet = null;
        }

        // Récupérer l'utilisateur et l'ancien état AVANT le save
        $user = $naissance->user;
        $ancienEtat = $naissance->getOriginal('etat') ?? 'nouveau';
        
        $naissance->save();

        // =================================================================
        // CRÉATION DE NOTIFICATION WEB POUR L'UTILISATEUR
        // =================================================================
        if ($user && $ancienEtat !== $naissance->etat) {
            UserNotification::notifyStatusChange(
                $user->id,
                'naissance',
                $naissance->id,
                $naissance->reference,
                $ancienEtat,
                $naissance->etat
            );
        }

        // =================================================================
        // ENREGISTREMENT DANS L'HISTORIQUE DES ACTIONS
        // =================================================================
        $action = $naissance->etat === 'rejetée' ? 'rejet' : 'changement_etat';
        $champsModifies = null;
        if ($naissance->etat === 'rejetée' && $request->motif_champs) {
            $champsModifies = $request->motif_champs;
        }
        
        ActionHistory::logAction(
            'naissance',
            $naissance->id,
            $naissance->reference,
            $action,
            $ancienEtat,
            $naissance->etat,
            $naissance->motif_de_rejet,
            $champsModifies,
            null
        );

        // =================================================================
        // ENVOI DE NOTIFICATION PUSH
        // =================================================================
        $user = $naissance->user;

        if ($user && !empty($user->push_notification)) {
            $title = '';
            $body = '';
            $data = [];

            switch ($naissance->etat) {
                case 'réçu':
                    $title = 'Demande reçue';
                    $body = 'Votre demande d’extrait de naissance a été reçue et sera traitée dans les plus brefs délais.';
                    $data = ['url' => 'plateauapps://demande?reference=' . $naissance->reference];
                    break;

                case 'terminé':
                    $title = 'Demande Traitée';
                    $body = 'Votre demande d\'extrait de mariage a été traitée.';
                    if ($naissance->livraison_code) {
                        $body .= ' Votre code de livraison est : ' . $naissance->livraison_code;
                    }
                    $data = ['url' => 'plateauapps://demande?reference=' . $naissance->reference];
                    break;

                case 'rejetée':
                    $title = 'Demande Rejetée - Modification requise';
                    $body = 'Votre demande d’extrait de naissance n’a pas pu être traitée. Veuillez vérifier et corriger les informations fournies.';
                    $data = ['url' => 'plateauapps://demande?reference=' . $naissance->reference];
                    break;
            }

            if (!empty($title) && !empty($body)) {
                $this->sendPushNotification($user->push_notification, $title, $body, $data);
            }
        }
        // =================================================================

        // Redirection en fonction de l'état
        if ($naissance->etat === 'terminé') {
            return redirect()->route('agent.demandes.naissance.index')
                ->with('success', 'État mis à jour avec succès' .
                    ($naissance->choix_option === 'livraison' ? ' et livraison initiée (Code: ' . $naissance->livraison_code . ')' : ''));
        } else {
            return redirect()->route('agent.demandes.naissance.index')
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
            'name' => 'Nom',
            'prenom' => 'Prénoms',
            'number' => 'Numéro de registre',
            'DateR' => 'Date de registre',
            'commune' => 'Commune de naissance',
            'CNI' => 'Pièce d\'identité (CNI/Passeport)',
            'type' => 'Type de document',
            'quantite' => 'Quantité'
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

    /**
     * Modifier une demande rejetée
     */
    public function modifierDemande(Request $request, $id)
    {
        $demande = Naissance::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('peut_modifier', true)
            ->firstOrFail();

        // Valider uniquement les champs qui étaient à modifier
        $champsAModifier = json_decode($demande->champs_a_modifier, true);
        $rules = [];

        // Définir les règles de validation par champ
        foreach ($champsAModifier as $champ) {
            switch ($champ) {
                case 'name':
                    $rules['name'] = 'required|string|max:255';
                    break;
                case 'prenom':
                    $rules['prenom'] = 'required|string|max:255';
                    break;
                case 'number':
                    $rules['number'] = 'required|string|max:50';
                    break;
                case 'DateR':
                    $rules['DateR'] = 'required|date';
                    break;
                case 'commune':
                    $rules['commune'] = 'required|string';
                    break;
                case 'type':
                    $rules['type'] = 'required|string|in:simple,extrait_integral';
                    break;
                case 'quantite':
                    $rules['quantite'] = 'required|integer|min:1|max:10';
                    break;
                case 'CNI':
                    $rules['CNI'] = 'required|file|mimes:jpeg,png,jpg,pdf|max:1024';
                    break;
            }
        }

        $validated = $request->validate($rules);

        // Mettre à jour uniquement les champs spécifiés
        foreach ($champsAModifier as $champ) {
            if ($champ === 'CNI' && $request->hasFile('CNI')) {
                // Supprimer l'ancien fichier si existe
                if ($demande->CNI) {
                    Storage::delete($demande->CNI);
                }

                // Enregistrer le nouveau fichier
                $file = $request->file('CNI');
                $path = $file->store('pieces-identite', 'public');
                $demande->CNI = $path;
            } elseif (isset($validated[$champ])) {
                $demande->$champ = $validated[$champ];
            }
        }

        // Réinitialiser l'état et désactiver la modification
        $demande->etat = 'en attente';
        $demande->peut_modifier = false;
        $demande->champs_a_modifier = null;
        $demande->motif_de_rejet = null;
        $demande->save();

        // Notification à l'admin/agent
        // Vous pouvez ajouter ici une notification ou un email

        return response()->json([
            'success' => true,
            'message' => 'Demande modifiée avec succès et soumise à nouveau.',
            'demande' => $demande
        ]);
    }

    private function generateQrCode($livraisonCode)
    {
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

    public function markAsDelivered(Request $request, $id)
    {
        // Valider la requête
        $validator = Validator::make($request->all(), [
            'statut_livraison' => 'required|string|in:livré',
            'reference' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Trouver la demande par ID avec l'utilisateur associé
        $naissance = Naissance::with('user')->find($id);
        if (!$naissance) {
            return response()->json(['error' => 'Demande non trouvée'], 404);
        }

        // Vérifier si la référence est correcte
        if ($naissance->reference !== $request->reference) {
            return response()->json(['error' => 'Référence incorrecte'], 400);
        }

        // Sauvegarder l'ancien statut
        $ancienStatut = $naissance->statut_livraison ?? 'en attente';
        
        // Récupérer l'utilisateur associé à la demande
        $user = $naissance->user;
        
        // Mettre à jour le statut de livraison
        $naissance->statut_livraison = $request->statut_livraison;
        $naissance->save();

        // =================================================================
        // CRÉATION DE NOTIFICATION WEB POUR L'UTILISATEUR
        // =================================================================
        if ($user) {
            UserNotification::notifyStatusChange(
                $user->id,
                'naissance',
                $naissance->id,
                $naissance->reference,
                $ancienStatut,
                'livré'
            );
        }

        // =================================================================
        // <-- NOTIFICATION PUSH POUR MOBILE
        // =================================================================

        if ($user && !empty($user->push_notification)) {
            $title = 'Retrait d’extrait de naissance';
            $body = 'La récupération de votre extrait de naissance a été effectuée avec succès.';
            $data = ['url' => 'plateauapps://demande?reference=' . $naissance->reference];

            $this->sendPushNotification($user->push_notification, $title, $body, $data);
        }
        // =================================================================
        // FIN DU BLOC DE NOTIFICATION
        // =================================================================

        return response()->json(['success' => 'La demande a été marquée comme livrée.']);
    }

    public function downloadDeliveryInfo($id)
    {
        $naissance = Naissance::with(['user'])->findOrFail($id);

        $data = [
            'naissance' => $naissance,
            'livraison' => $naissance->livraison,
            'date' => now()->format('d/m/Y'),
        ];

        $pdf = PDF::loadView('agent.pdf.delivery-info', $data)
            ->setPaper('a6', 'landscape')
            ->setOption('isRemoteEnabled', true);

        return $pdf->download('etiquette-livraison-' . $naissance->livraison_code . '.pdf');
    }

    // =================================================================
    // <-- NOUVEAU : FONCTION PRIVÉE POUR ENVOYER LA NOTIFICATION
    // =================================================================
    private function sendPushNotification(string $userToken, string $title, string $body, array $data = [])
    {
        if (empty($userToken)) {
            Log::warning('Tentative d\'envoi de notif push sans token utilisateur.');
            return;
        }

        $payload = [
            'to' => $userToken,
            'sound' => 'default',
            'title' => $title,
            'body' => $body,
            'data' => (object) $data,
        ];

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Encoding' => 'gzip, deflate',
                ])->post('https://exp.host/--/api/v2/push/send', $payload);

            if ($response->failed()) {
                Log::error('Échec envoi notification Expo: ' . $response->body());
            } else {
                Log::info('Notification Expo envoyée: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Exception lors de lenvoi de notification Expo: ' . $e->getMessage());
        }
    }
}
