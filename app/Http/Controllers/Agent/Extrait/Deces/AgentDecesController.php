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

class AgentDecesController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::guard('agent')->user();

        $decesQuery = Deces::where('commune', $admin->communeM)
            ->where('agent_id', $admin->id)
            ->where(function($query) {
            $query->whereNull('statut_livraison')
                    ->orWhere('statut_livraison', '!=', 'livré');
            })
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
        
        // Récupérer le statut diaspora de l'utilisateur
        $isDiaspora = $deces->user->diaspora ?? false;

        // Les états possibles à afficher dans le formulaire
        $etats = ['en attente', 'réçu', 'terminé'];

        return view('agent.extraits.deces.edit_etat', compact('deces', 'etats', 'isDiaspora'));
    }

    public function updateEtat(Request $request, $id)
    {
        $deces = Deces::findOrFail($id);
        
        // Validation de l'état
        $request->validate([
            'etat' => 'required|string|in:en attente,réçu,terminé',
            'livraison_id' => 'nullable|exists:postes,id'
        ]);

        // Mise à jour de l'état
        $deces->etat = $request->etat;
        
        // Si l'état est "terminé" ET choix_option = "livraison" ET pas encore de code
        if ($deces->etat === 'terminé' && $deces->choix_option === 'livraison' && is_null($deces->livraison_code)) {
            // Générer un code de livraison unique
            $livraisonCode = 'LIVD' . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);

            // Vérifier que le code est unique
            while (Deces::where('livraison_code', $livraisonCode)->exists()) {
                $livraisonCode = 'LIVD' . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
            }

            // Générer le code QR
            $qrCodePath = $this->generateQrCode($livraisonCode);

            // Mettre à jour le statut de livraison, le code et le chemin du QR code
            $deces->statut_livraison = 'en attente';
            $deces->livraison_code = $livraisonCode;
            $deces->qr_code_path = $qrCodePath; // Assurez-vous d'avoir ce champ dans votre table
            
            // Toujours mettre livraison_id à 1
            $deces->livraison_id = 1;
            $deces->dhl_id = null; // Désactiver DHL
        }
        
        $deces->save();
        
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


    public function markAsDeliveredDeces(Request $request, $id)
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
        $deces = Deces::find($id);
        if (!$deces) {
            return response()->json(['error' => 'Demande non trouvée'], 404);
        }

        // Vérifier si la référence est correcte
        if ($deces->reference !== $request->reference) {
            return response()->json(['error' => 'Référence incorrecte'], 400);
        }

        // Mettre à jour le statut de livraison
        $deces->statut_livraison = $request->statut_livraison;
        $deces->save();

        return response()->json(['success' => 'La demande a été marquée comme livrée.']);
    }

    public function downloadDeliveryInfo($id)
    {
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
