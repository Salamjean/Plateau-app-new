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

class AgentMariageController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer l'admin connecté
        $admin = Auth::guard('agent')->user();

        // Initialiser la requête pour Mariage et filtrer par commune de l'admin
        $query = Mariage::where('commune', $admin->communeM)
            ->where(function($query) {
            $query->whereNull('statut_livraison')
                    ->orWhere('statut_livraison', '!=', 'livré');
            })
            ->where('agent_id', $admin->id)
            ->with('user'); // Ajout de la récupération de la relation 'user'

        // Vérifier le type de recherche et appliquer le filtre
        if ($request->filled('searchType') && $request->filled('searchInput')) {
            if ($request->searchType === 'nomConjoint') {
                $query->where(function($q) use ($request) {
                    $q->where('nomEpoux', 'like', '%' . $request->searchInput . '%')
                        ->orWhere('nomEpouse', 'like', '%' . $request->searchInput . '%');
                });
            } elseif ($request->searchType === 'prenomConjoint') {
                $query->where(function($q) use ($request) {
                $q->where('prenomEpoux', 'like', '%' . $request->searchInput . '%')
                    ->orWhere('prenomEpouse', 'like', '%' . $request->searchInput . '%');
                });
            } elseif ($request->searchType === 'lieuNaissance') {
                $query->where(function($q) use ($request) {
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

        // Les états possibles à afficher dans le formulaire
        $etats = ['en attente', 'réçu', 'terminé'];

        return view('agent.extraits.mariages.edit', compact('mariage', 'etats', 'isDiaspora'));
    }

    public function updateEtat(Request $request, $id)
    {
        $mariage = Mariage::findOrFail($id);
        
        // Validation de l'état
        $request->validate([
            'etat' => 'required|string|in:en attente,réçu,terminé',
            'livraison_id' => 'nullable|exists:postes,id'
        ]);

        // Mise à jour de l'état
        $mariage->etat = $request->etat;
        
        // Si l'état est "terminé" ET choix_option = "livraison" ET pas encore de code
        if ($mariage->etat === 'terminé' && $mariage->choix_option === 'livraison' && is_null($mariage->livraison_code)) {
            // Générer un code de livraison unique
            $livraisonCode = 'LIVM' . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);

            // Vérifier que le code est unique
            while (Mariage::where('livraison_code', $livraisonCode)->exists()) {
                $livraisonCode = 'LIVM' . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
            }

            // Générer le code QR
            $qrCodePath = $this->generateQrCode($livraisonCode);

            // Mettre à jour le statut de livraison, le code et le chemin du QR code
            $mariage->statut_livraison = 'en attente';
            $mariage->livraison_code = $livraisonCode;
            $mariage->qr_code_path = $qrCodePath; // Assurez-vous d'avoir ce champ dans votre table
            
            // Toujours mettre livraison_id à 1
            $mariage->livraison_id = 1;
            $mariage->dhl_id = null; // Désactiver DHL
        }
        
        $mariage->save();
        
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

        // Trouver la demande par ID
        $mariage = Mariage::find($id);
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
}
