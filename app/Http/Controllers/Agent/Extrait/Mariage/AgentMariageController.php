<?php

namespace App\Http\Controllers\Agent\Extrait\Mariage;

use App\Http\Controllers\Controller;
use App\Models\Mariage;
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

class AgentMariageController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer l'admin connecté
        $admin = Auth::guard('agent')->user();

        // Initialiser la requête pour Mariage et filtrer par commune de l'admin
        $query = Mariage::where('commune', $admin->communeM)
            ->where(function ($query) {
                $query->whereNull('statut_livraison')
                    ->orWhere('statut_livraison', '!=', 'livré');
            })
            ->where('agent_id', $admin->id)
            ->where('etat', '!=', 'rejetée')
            ->with('user'); // Ajout de la récupération de la relation 'user'

        // Vérifier le type de recherche et appliquer le filtre
        if ($request->filled('searchType') && $request->filled('searchInput')) {
            if ($request->searchType === 'nomConjoint') {
                $query->where(function ($q) use ($request) {
                    $q->where('nomEpoux', 'like', '%' . $request->searchInput . '%')
                        ->orWhere('nomEpouse', 'like', '%' . $request->searchInput . '%');
                });
            } elseif ($request->searchType === 'prenomConjoint') {
                $query->where(function ($q) use ($request) {
                    $q->where('prenomEpoux', 'like', '%' . $request->searchInput . '%')
                        ->orWhere('prenomEpouse', 'like', '%' . $request->searchInput . '%');
                });
            } elseif ($request->searchType === 'lieuNaissance') {
                $query->where(function ($q) use ($request) {
                    $q->where('lieuNaissanceEpoux', 'like', '%' . $request->searchInput . '%')
                        ->orWhere('lieuNaissanceEpouse', 'like', '%' . $request->searchInput . '%');
                });
            }
        }

        // Récupérer tous les mariages correspondant aux critères de filtrage
        $mariages = $query->paginate(10);

        // Retourner la vue avec les mariages filtrés et les alertes
        return view('agent.extraits.mariages.index', compact('mariages'));
    }

    public function edit($id)
    {
        $mariage = Mariage::findOrFail($id);

        // Récupérer le statut diaspora de l'utilisateur
        $isDiaspora = $mariage->user->diaspora ?? false;

        $etats = ['en attente', 'réçu', 'terminé', 'rejetée'];

        return view('agent.extraits.mariages.edit', compact('mariage', 'etats', 'isDiaspora'));
    }

    public function updateEtat(Request $request, $id)
    {
        $mariage = Mariage::with('user')->findOrFail($id);

        $request->validate([
            'etat' => 'required|string|in:en attente,réçu,terminé,rejetée',
            'livraison_id' => 'nullable|exists:postes,id',
            'motif_champs' => 'required_if:etat,rejetée|array',
            'motif_commentaire' => 'nullable|string',
            'motif_de_rejet' => 'nullable|string',
        ]);

        $mariage->etat = $request->etat;

        if ($mariage->etat === 'rejetée') {
            // Construire le motif structuré
            $motif = $this->construireMotifRejet($request);
            $mariage->motif_de_rejet = $motif;

            // Enregistrer les champs à modifier
            $mariage->champs_a_modifier = json_encode($request->motif_champs);
            $mariage->peut_modifier = true; // Activer la possibilité de modifier

            // Annuler toute livraison
            $mariage->statut_livraison = null;
            $mariage->livraison_code = null;
            $mariage->qr_code_path = null;
            $mariage->livraison_id = null;
            $mariage->dhl_id = null;
            $mariage->livreur_id = null;
            $mariage->agent_id = null;
        } elseif ($mariage->etat === 'terminé') {
            // Logique existante pour 'terminé'
            if ($mariage->choix_option === 'livraison' && is_null($mariage->livraison_code)) {
                $livraisonCode = 'LIVM' . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);

                while (Mariage::where('livraison_code', $livraisonCode)->exists()) {
                    $livraisonCode = 'LIVM' . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
                }

                $qrCodePath = $this->generateQrCode($livraisonCode);

                $mariage->statut_livraison = 'en attente';
                $mariage->livraison_code = $livraisonCode;
                $mariage->qr_code_path = $qrCodePath;
                $mariage->livraison_id = 1;
                $mariage->dhl_id = null;
            }

            // Désactiver la modification pour les demandes terminées
            $mariage->champs_a_modifier = null;
            $mariage->peut_modifier = false;
            $mariage->motif_de_rejet = null;
        } else {
            // Pour 'en attente' ou 'réçu', désactiver la modification
            $mariage->champs_a_modifier = null;
            $mariage->peut_modifier = false;
            $mariage->motif_de_rejet = null;
        }

        $mariage->save();

        // =================================================================
        // ENVOI DE NOTIFICATION PUSH
        // =================================================================
        $user = $mariage->user;

        if ($user && !empty($user->push_notification)) {
            $title = '';
            $body = '';
            $data = [];

            switch ($mariage->etat) {
                case 'réçu':
                    $title = 'Demande reçue';
                    $body = 'Votre demande d\'extrait de mariage a été bien reçue et est en cours de traitement.';
                    $data = ['url' => 'plateauapps://demande?reference=' . $mariage->reference];
                    break;

                case 'terminé':
                    $title = 'Demande Traitée';
                    $body = 'Votre demande d\'extrait de mariage a été traitée.';
                    if ($mariage->livraison_code) {
                        $body .= ' Votre code de livraison est : ' . $mariage->livraison_code;
                    }
                    $data = ['url' => 'plateauapps://demande?reference=' . $mariage->reference];
                    break;

                case 'rejetée':
                    $title = 'Demande Rejetée - Modification requise';
                    $body = 'Votre demande d\'extrait de mariage a été rejetée. Vous pouvez maintenant modifier les informations incorrectes.';
                    $data = ['url' => 'plateauapps://demande?reference=' . $mariage->reference];
                    break;
            }

            if (!empty($title) && !empty($body)) {
                $this->sendPushNotification($user->push_notification, $title, $body, $data);
            }
        }
        // =================================================================

        // Redirection en fonction de l'état
        if ($mariage->etat === 'terminé') {
            return redirect()->route('agent.demandes.wedding.index')
                ->with('success', 'État mis à jour avec succès' .
                    ($mariage->choix_option === 'livraison' ? ' et livraison initiée (Code: ' . $mariage->livraison_code . ')' : ''));
        } else {
            return redirect()->route('agent.demandes.wedding.index')
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
            'typeDemande' => 'Type de demande',
            'nomEpoux' => 'Nom du conjoint',
            'prenomEpoux' => 'Prénom du conjoint',
            'dateNaissanceEpoux' => 'Date de naissance du conjoint',
            'lieuNaissanceEpoux' => 'Lieu de naissance du conjoint',
            'commune' => 'Commune',
            'quantite' => 'Quantité',
            'pieceIdentite' => 'Pièce d\'identité',
            'extraitMariage' => 'Extrait de mariage',
            'CMU' => 'Numéro NNI'
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

    public function markAsDeliveredMariage(Request $request, $id)
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
        $mariage = Mariage::with('user')->find($id);
        if (!$mariage) {
            return response()->json(['error' => 'Demande non trouvée'], 404);
        }

        // Vérifier si la référence est correcte
        if ($mariage->reference !== $request->reference) {
            return response()->json(['error' => 'Référence incorrecte'], 400);
        }

        // Mettre à jour le statut de livraison
        $mariage->statut_livraison = $request->statut_livraison;
        $mariage->save();

        // =================================================================
        // <-- NOTIFICATION PUSH POUR MOBILE
        // =================================================================
        $user = $mariage->user;

        if ($user && !empty($user->push_notification)) {
            $title = 'Extrait de Mariage Remis';
            $body = 'Votre demande d\'extrait de mariage vous a été remis avec succès.';
            $data = ['url' => 'plateauapps://demande?reference=' . $mariage->reference];

            $this->sendPushNotification($user->push_notification, $title, $body, $data);
        }
        // =================================================================
        // FIN DU BLOC DE NOTIFICATION
        // =================================================================

        return response()->json(['success' => 'La demande a été marquée comme livrée.']);
    }

    public function downloadDeliveryInfo($id)
    {
        $naissance = Mariage::with(['user'])->findOrFail($id);

        $data = [
            'naissance' => $naissance,
            'livraison' => $naissance->livraison,
            'date' => now()->format('d/m/Y'),
        ];

        $pdf = PDF::loadView('agent.pdf.delivery-info-mariage', $data)
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
