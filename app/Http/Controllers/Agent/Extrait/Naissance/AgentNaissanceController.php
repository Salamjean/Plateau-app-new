<?php

namespace App\Http\Controllers\Agent\Extrait\Naissance;

use App\Http\Controllers\Controller;
use App\Models\Naissance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Support\Facades\Storage;
use PDF;
use Illuminate\Support\Facades\Http; // <-- AJOUT
use Illuminate\Support\Facades\Log;  // <-- AJOUT

class AgentNaissanceController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('agent')->user();

        $naissances = Naissance::where('commune', $admin->communeM)
            ->where('agent_id', $admin->id)
            ->where(function($query) {
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
        // <-- MODIFIÉ : Chargement de l'utilisateur avec la demande
        $naissance = Naissance::with('user')->findOrFail($id);
        
        // Validation de l'état
        $request->validate([
            'etat' => 'required|string|in:en attente,réçu,terminé,rejetée',
            'livraison_id' => 'nullable|exists:postes,id',
            'motif_de_rejet' => 'required_if:etat,rejetée|string|nullable', // Ajout de la validation
        ]);

        // Mise à jour de l'état
        $naissance->etat = $request->etat;
        
        if ($naissance->etat === 'rejetée') {
            $naissance->motif_de_rejet = $request->motif_de_rejet;
            
            // Annuler toute livraison si la demande est rejetée
            $naissance->statut_livraison = null;
            $naissance->livraison_code = null;
            $naissance->qr_code_path = null;
            $naissance->livraison_id = null;
            $naissance->dhl_id = null;
            $naissance->livreur_id = null;
            $naissance->agent_id = null;
        } elseif ($naissance->etat === 'terminé' && $naissance->choix_option === 'livraison' && is_null($naissance->livraison_code)) {
            // (Logique existante pour 'terminé'...)
            
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

            // Vider le motif si on termine
            $naissance->motif_de_rejet = null;

        } else {
             // Pour 'en attente' ou 'réçu', vider aussi le motif
             $naissance->motif_de_rejet = null;
        }
        
        $naissance->save();
        
        // =================================================================
        // <-- NOUVEAU : BLOC D'ENVOI DE NOTIFICATION PUSH
        // =================================================================
        $user = $naissance->user;
        
        if ($user && !empty($user->push_notification)) {
            $title = '';
            $body = '';
            $data = []; 

            switch ($naissance->etat) {
                case 'réçu':
                    $title = 'Demande reçue';
                    $body = 'Votre demande d\'extrait de naissance a été bien reçue et en cours de traitement.';
                    $data = ['url' => 'plateauapps://demande?reference=' . $naissance->reference];
                    break;
                
                case 'terminé':
                    $title = 'Demande Traitée';
                    $body = 'Votre demande d\'extrait de naissance a été traitée.';
                    if ($naissance->livraison_code) {
                         $body .= ' Votre code de livraison est : ' . $naissance->livraison_code;
                    }
                    $data = ['url' => 'plateauapps://demande?reference=' . $naissance->reference];
                    break;
                
                case 'rejetée':
                    $title = 'Demande Rejetée';
                    $body = 'Votre demande d\'extrait de naissance a été rejetée. Motif : ' . ($naissance->motif_de_rejet ?? 'Non spécifié');
                    $data = ['url' => 'plateauapps://echec?reference=' . $naissance->reference];
                    break;
            }

            if (!empty($title) && !empty($body)) {
                $this->sendPushNotification($user->push_notification, $title, $body, $data);
            }
        }
        // =================================================================
        // FIN DU BLOC DE NOTIFICATION
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

        // Trouver la demande par ID
        $naissance = Naissance::find($id);
        if (!$naissance) {
            return response()->json(['error' => 'Demande non trouvée'], 404);
        }

        // Vérifier si la référence est correcte
        if ($naissance->reference !== $request->reference) {
            return response()->json(['error' => 'Référence incorrecte'], 400);
        }

        // Mettre à jour le statut de livraison
        $naissance->statut_livraison = $request->statut_livraison;
        $naissance->save();

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