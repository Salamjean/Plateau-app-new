<?php

namespace App\Http\Controllers\Dhl\Agence;

use App\Http\Controllers\Controller;
use App\Models\DHLAgence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AgenceDelivery extends Controller
{
     public function delivery(){
        $livreur = Auth::guard('agency')->user();
        
        // Récupérer toutes les demandes attribuées à cette poste
        $demandes = collect();
        
        // Liste des modèles à vérifier
        $modeles = [
            'Naissance',
            'Deces',
            'Mariage'
        ];
        
        foreach ($modeles as $modele) {
            $classeModele = "App\\Models\\$modele";
            $demandesModele = $classeModele::where('agence_id', $livreur->id)
                ->with('user','livreur') // Si vous avez une relation avec l'utilisateur
                ->where('statut_livraison', '!=','livré')
                ->get()
                ->map(function($item) use ($modele) {
                    $item->type_demande = $this->getTypeDemande($modele);
                    return $item;
                });
                
            $demandes = $demandes->merge($demandesModele);
        }

        $livreurs = DHLAgence::get();
        
        // Trier par date de création (les plus récentes en premier)
        $demandes = $demandes->sortBy('created_at');
        return view('agency.livraison.demandes-attribuees', compact('demandes','livreurs'));
    }

    public function livree()
    {
        $livreur = Auth::guard('agency')->user();
        
        // Récupérer toutes les demandes attribuées à cette poste
        $demandes = collect();
        
        // Liste des modèles à vérifier
        $modeles = [
            'Naissance',
            'Deces',
            'Mariage'
        ];
        
        foreach ($modeles as $modele) {
            $classeModele = "App\\Models\\$modele";
            $demandesModele = $classeModele::where('agence_id', $livreur->id)
                ->with('user','livreur') // Si vous avez une relation avec l'utilisateur
                ->where('statut_livraison','livré')
                ->get()
                ->map(function($item) use ($modele) {
                    $item->type_demande = $this->getTypeDemande($modele);
                    return $item;
                });
                
            $demandes = $demandes->merge($demandesModele);
        }

        $livreurs = DHLAgence::get();
        
        // Trier par date de création (les plus récentes en premier)
        $demandes = $demandes->sortBy('created_at');
        
        return view('agency.livraison.demandes_livre', compact('demandes','livreurs'));
    }

     private function getTypeDemande($modele)
    {
        $types = [
            'Naissance' => 'Naissance',
            'Deces' => 'Deces',
            'Mariage' => 'Mariage'
        ];
        
        return $types[$modele] ?? 'Demande';
    }

    public function validated(Request $request)
    {
        // Vérification de la méthode HTTP
        if (!$request->isMethod('post')) {
            return view('agency.livraison.validate');
        }

        $request->validate([
            'reference' => 'required|string|max:255',
            'demande_id' => 'required|integer',
        ]);

        try {
            $modelClass = "App\\Models\\" . Str::studly(str_replace('-', '', $request->demande_type));
            
            if (!class_exists($modelClass)) {
                throw new \Exception("Type de demande non valide");
            }

            $demande = $modelClass::where('id', $request->demande_id)
                        ->where('reference', $request->reference)
                        ->where('statut_livraison', 'en cours')
                        ->firstOrFail();

            // Mise à jour avec la date et heure actuelle
            $demande->update([
                'statut_livraison' => 'livré',
                'agence_id' => Auth::guard('agency')->user()->id, // Si besoin d'enregistrer le livreur qui a fait la livraison
            ]);

           return response()->json([
                'success' => true,
                'message' => 'Livraison confirmée avec succès',
                'redirect' => route('agency.livree') // URL de la page de confirmation
            ]);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur: '.$e->getMessage());
        }
    }

    public function checkReference(Request $request)
    {
        $request->validate(['reference' => 'required|string']);

        $models = [
            'Naissance',
            'Deces',
            'Mariage'
        ];

        foreach ($models as $model) {
            $class = "App\\Models\\$model";
            $demande = $class::where('reference', $request->reference)
                        ->where('statut_livraison', 'en cours')
                        ->first();

            if ($demande) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $demande->id,
                        'type' => Str::slug($model),
                        'destinataire' => $demande->nom_destinataire . ' ' . $demande->prenom_destinataire,
                        'contact' => $demande->contact_destinataire,
                        'adresse' => $demande->adresse_livraison,
                        'email' => $demande->email_destinataire,
                        'livraison_code' => $demande->livraison_code,
                        'commune' => $demande->commune_livraison,
                        'quartier' => $demande->quartier,
                        'ville' => $demande->ville,
                        'montant' => $demande->montant_livraison
                    ]
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Aucune demande trouvée avec cette référence ou déjà livrée'
        ], 404);
    }
}
