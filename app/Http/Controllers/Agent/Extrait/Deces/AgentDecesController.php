<?php

namespace App\Http\Controllers\Agent\Extrait\Deces;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use PDF;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Log;  

class AgentDecesController extends Controller
{
    public function index(Request $request)
    {
        // ... (votre code index inchangé)
        $admin = Auth::guard('agent')->user();

        $decesQuery = Deces::where('commune', $admin->communeM)
            ->where('agent_id', $admin->id)
            ->where(function($query) {
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

    // ...
    // Deces edit 
    public function edit($id)
    {
        // ... (votre code edit inchangé)
        $deces = Deces::findOrFail($id);
        $isDiaspora = $deces->user->diaspora ?? false;
        $etats = ['en attente', 'réçu', 'terminé', 'rejetée']; 
        return view('agent.extraits.deces.edit_etat', compact('deces', 'etats', 'isDiaspora'));
    }

    public function updateEtat(Request $request, $id)
    {
        // On charge la demande AVEC l'utilisateur associé
        $deces = Deces::with('user')->findOrFail($id); 
        
        // Validation de l'état
        $request->validate([
            'etat' => 'required|string|in:en attente,réçu,terminé,rejetée',
            'livraison_id' => 'nullable|exists:postes,id',
            'motif_de_rejet' => 'required_if:etat,rejetée|string|nullable',
        ]);

        // Mise à jour de l'état
        $deces->etat = $request->etat;
        
        // ... (toute votre logique if/elseif/else pour 'rejetée', 'terminé', etc. reste inchangée)
        if ($deces->etat === 'rejetée') {
            $deces->motif_de_rejet = $request->motif_de_rejet;
            $deces->statut_livraison = null;
            $deces->livraison_code = null;
            $deces->qr_code_path = null;
            $deces->livraison_id = null;
            $deces->dhl_id = null;
            $deces->agent_id = null;
            $deces->livreur_id = null;
        } elseif ($deces->etat === 'terminé' && $deces->choix_option === 'livraison' && is_null($deces->livraison_code)) {
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
            $deces->motif_de_rejet = null;
        } else {
            $deces->motif_de_rejet = null;
        }
        
        $deces->save();
        
        // =================================================================
        // BLOC D'ENVOI DE NOTIFICATION PUSH
        // =================================================================
        $user = $deces->user;
        
        // Vérifier si l'utilisateur existe et s'il a un token de notification
        if ($user && !empty($user->push_notification)) {
            $title = '';
            $body = '';
            $data = []; // <-- NOUVEAU : Initialisation de $data

            // La ligne '$data = [...]' qui était ici a été SUPPRIMÉE.

            switch ($deces->etat) {
                case 'réçu':
                    $title = 'Demande reçue';
                    $body = 'Votre demande est bien reçue par la mairie et est en cours de traitement.';
                    // <-- NOUVEAU : Lien pour 'réçu'
                    $data = ['url' => 'plateauapps://demande?reference=' . $deces->reference];
                    break;
                
                case 'terminé':
                    $title = 'Demande terminée';
                    $body = 'Votre demande d\'extrait de décès a été traitée et est maintenant terminée.';
                    if ($deces->livraison_code) {
                         $body .= ' Votre code de livraison est : ' . $deces->livraison_code;
                    }
                    // <-- NOUVEAU : Lien pour 'terminé'
                    $data = ['url' => 'plateauapps://demande?reference=' . $deces->reference];
                    break;
                
                case 'rejetée':
                    $title = 'Demande rejetée';
                    $body = 'Votre demande a été rejetée. Motif : ' . ($deces->motif_de_rejet ?? 'Non spécifié');
                    // <-- NOUVEAU : Lien pour 'rejetée'
                    $data = ['url' => 'plateauapps://echec?reference=' . $deces->reference];
                    break;
            }

            // Si un titre et un corps ont été définis (donc pour 'réçu', 'terminé', 'rejetée')
            if (!empty($title) && !empty($body)) {
                $this->sendPushNotification($user->push_notification, $title, $body, $data);
            }
        }
        // =================================================================
        // FIN DU BLOC DE NOTIFICATION
        // =================================================================
        
        // ... (votre code de redirection inchangé)
        if ($deces->etat === 'terminé') {
            return redirect()->route('agent.demandes.deces.index')
                ->with('success', 'État mis à jour avec succès' .
                    ($deces->choix_option === 'livraison' ? ' et livraison initiée (Code: ' . $deces->livraison_code . ')' : ''));
        
        } else {
            return redirect()->route('agent.demandes.deces.index')
                ->with('success', 'État mis à jour avec succès');
        }
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
        // ... (votre code inchangé)
        $validator = Validator::make($request->all(), [
            'statut_livraison' => 'required|string|in:livré',
            'reference' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $deces = Deces::find($id);
        if (!$deces) {
            return response()->json(['error' => 'Demande non trouvée'], 404);
        }
        if ($deces->reference !== $request->reference) {
            return response()->json(['error' => 'Référence incorrecte'], 400);
        }
        $deces->statut_livraison = $request->statut_livraison;
        $deces->save();
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


    // =================================================================
    // FONCTION PRIVÉE POUR ENVOYER LA NOTIFICATION (INCHANGÉE)
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
            'data' => (object) $data, // Ceci enverra la bonne URL en fonction de l'état
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
            Log::error('Exception lors de l\'envoi de notification Expo: ' . $e->getMessage());
        }
    }
}