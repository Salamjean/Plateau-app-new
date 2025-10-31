<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Models\Naissance;
use App\Models\Mariage;
use App\Models\Deces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator; // <-- Importez ceci
class StatistiqueController extends Controller
{
    /**
     * Statistiques des demandes par statut pour l'utilisateur connecté
     */
    public function statistiquesParStatut(Request $request)
{
    try {
        $user = $request->user();
        
        // Vérifier si l'utilisateur est authentifié
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        // --- Statistiques BRUTES (par état réel de la BDD) ---

        $statsNaissanceRaw = Naissance::where('user_id', $user->id)
            ->select('etat', DB::raw('COUNT(*) as count'))
            ->groupBy('etat')
            ->get()
            ->pluck('count', 'etat'); // Ex: ['en attente' => 5, 'terminé' => 10]

        $statsMariageRaw = Mariage::where('user_id', $user->id)
            ->select('etat', DB::raw('COUNT(*) as count'))
            ->groupBy('etat')
            ->get()
            ->pluck('count', 'etat');

        $statsDecesRaw = Deces::where('user_id', $user->id)
            ->select('etat', DB::raw('COUNT(*) as count'))
            ->groupBy('etat')
            ->get()
            ->pluck('count', 'etat');

        // --- 1. Formatage par catégorie ---

        $statsNaissance = [
            'en cours' => $statsNaissanceRaw->get('en attente', 0),
            'terminé'  => $statsNaissanceRaw->get('terminé', 0),
            'total'    => $statsNaissanceRaw->sum() // Somme de tous les états (en attente, terminé, rejeté, etc.)
        ];

        $statsMariage = [
            'en cours' => $statsMariageRaw->get('en attente', 0),
            'terminé'  => $statsMariageRaw->get('terminé', 0),
            'total'    => $statsMariageRaw->sum()
        ];

        $statsDeces = [
            'en cours' => $statsDecesRaw->get('en attente', 0),
            'terminé'  => $statsDecesRaw->get('terminé', 0),
            'total'    => $statsDecesRaw->sum()
        ];

        // --- 2. Calcul des TOTAUX agrégés ---

        // Total "En cours" (somme de tous les 'en attente')
        $totalEnCours = $statsNaissance['en cours'] + $statsMariage['en cours'] + $statsDeces['en cours'];

        // Total "Terminé" (somme de tous les 'terminé')
        $totalTermine = $statsNaissance['terminé'] + $statsMariage['terminé'] + $statsDeces['terminé'];

        // Total "Général" (somme de toutes les demandes, peu importe l'état)
        $totalGeneral = $statsNaissance['total'] + $statsMariage['total'] + $statsDeces['total'];
        
        // --- Réponse JSON Combinée ---

        return response()->json([
            // Partie 1: Détails par catégorie
            'naissance' => $statsNaissance,
            'mariage'   => $statsMariage,
            'deces'     => $statsDeces,
            
            // Partie 2: Totaux agrégés
            'total_general' => $totalGeneral,
            'en_cours'      => $totalEnCours,
            'termine'       => $totalTermine,
        ]);

    } catch (\Exception $e) {
        \Log::error('Erreur statistiquesParStatut: ' . $e->getMessage());
        return response()->json(['error' => 'Erreur serveur'], 500);
    }
}
    /**
     * Récupérer une demande spécifique par type et ID
     */
    public function getDemandeSpecifique(Request $request, $type, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            switch ($type) {
                case 'naissance':
                    $demande = Naissance::where('user_id', $user->id)->find($id);
                    break;
                case 'mariage':
                    $demande = Mariage::where('user_id', $user->id)->find($id);
                    break;
                case 'deces':
                    $demande = Deces::where('user_id', $user->id)->find($id);
                    break;
                default:
                    return response()->json(['error' => 'Type de demande non valide'], 400);
            }

            if (!$demande) {
                return response()->json(['error' => 'Demande non trouvée'], 404);
            }

            return response()->json([
                'demande' => $demande,
                'type' => $type
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur getDemandeSpecifique: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Liste de toutes les demandes de l'utilisateur (tous types confondus)
     */
    public function listeToutesDemandes(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 20);

            // 1. Récupérer toutes les naissances avec toutes leurs colonnes
            // Nous utilisons map() pour ajouter un champ 'type_demande'
            $naissances = Naissance::where('user_id', $user->id)
                ->get()
                ->map(function ($item) {
                    $item->type_demande = 'naissance';
                    return $item;
                });

            // 2. Récupérer tous les mariages
            $mariages = Mariage::where('user_id', $user->id)
                ->get()
                ->map(function ($item) {
                    $item->type_demande = 'mariage';
                    return $item;
                });

            // 3. Récupérer tous les décès
            $deces = Deces::where('user_id', $user->id)
                ->get()
                ->map(function ($item) {
                    $item->type_demande = 'deces';
                    return $item;
                });

            // 4. Fusionner les collections et les trier
            $toutesDemandes = $naissances->merge($mariages)->merge($deces);
            $demandesTriees = $toutesDemandes->sortByDesc('created_at');

            // 5. Paginer manuellement la collection résultante
            $total = $demandesTriees->count();
            
            // values() est important pour réindexer la collection après le tri
            $itemsPourPage = $demandesTriees->forPage($page, $perPage)->values(); 

            $pagination = new LengthAwarePaginator(
                $itemsPourPage,
                $total,
                $perPage,
                $page,
                // Génère les URLs pour la pagination
                ['path' => $request->url(), 'query' => $request->query()]
            );

            // 6. Renvoyer la réponse JSON paginée
            return response()->json([
                'demandes' => $pagination->items(),
                'pagination' => [
                    'current_page' => $pagination->currentPage(),
                    'last_page' => $pagination->lastPage(),
                    'per_page' => $pagination->perPage(),
                    'total' => $pagination->total(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur listeToutesDemandes: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * État de suivi d'une demande spécifique
     */
    public function suiviDemande(Request $request, $type, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            switch ($type) {
                case 'naissance':
                    $demande = Naissance::where('user_id', $user->id)->find($id);
                    break;
                case 'mariage':
                    $demande = Mariage::where('user_id', $user->id)->find($id);
                    break;
                case 'deces':
                    $demande = Deces::where('user_id', $user->id)->find($id);
                    break;
                default:
                    return response()->json(['error' => 'Type de demande non valide'], 400);
            }

            if (!$demande) {
                return response()->json(['error' => 'Demande non trouvée'], 404);
            }

            // Historique des statuts
            $historique = [
                [
                    'statut' => $demande->etat,
                    'date' => $demande->updated_at ? $demande->updated_at->format('d/m/Y H:i') : 'N/A',
                    'description' => $this->getDescriptionStatut($demande->etat)
                ]
            ];

            // Si la demande est traitée, ajouter la date de traitement
            if ($demande->etat === 'traité' && $demande->updated_at) {
                $historique[] = [
                    'statut' => 'traitement',
                    'date' => $demande->updated_at->format('d/m/Y H:i'),
                    'description' => 'Demande traitée avec succès'
                ];
            }

            // Statut de création
            $historique[] = [
                'statut' => 'création',
                'date' => $demande->created_at ? $demande->created_at->format('d/m/Y H:i') : 'N/A',
                'description' => 'Demande créée'
            ];

            // Inverser pour avoir l'ordre chronologique
            $historique = array_reverse($historique);

            return response()->json([
                'demande' => [
                    'id' => $demande->id,
                    'reference' => $demande->reference,
                    'type' => $type,
                    'statut_actuel' => $demande->etat,
                    'date_creation' => $demande->created_at ? $demande->created_at->format('d/m/Y H:i') : 'N/A',
                    'date_mise_a_jour' => $demande->updated_at ? $demande->updated_at->format('d/m/Y H:i') : 'N/A',
                ],
                'historique' => $historique,
                'prochaines_etapes' => $this->getProchainesEtapes($demande->etat)
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur suiviDemande: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }
    public function suiviDemandeParReference(Request $request)
    {
        try {
            $user = $request->user();
            
            // 1. Vérifier l'authentification (Sanctum s'en charge, mais double vérification)
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            $reference = $request->input('reference');
            
            if (empty($reference)) {
                return response()->json(['error' => 'La référence de la demande est requise'], 400);
            }

            // 2. Tentative de trouver la demande dans chaque modèle
            $demande = Naissance::where('reference', $reference)->first();
            $type = 'naissance';

            if (!$demande) {
                $demande = Mariage::where('reference', $reference)->first();
                $type = 'mariage';
            }

            if (!$demande) {
                $demande = Deces::where('reference', $reference)->first();
                $type = 'deces';
            }

            // 3. Vérifier si la demande existe ET si elle appartient à l'utilisateur connecté
            if (!$demande || $demande->user_id !== $user->id) {
                // On renvoie 404 pour ne pas indiquer si la référence existe ou non (sécurité)
                return response()->json(['error' => 'Demande non trouvée pour cette référence'], 404);
            }
            
            // 4. Logique de construction de l'historique (similaire à suiviDemande)

            // Historique des statuts
            $historique = [
                [
                    'statut' => $demande->etat,
                    'date' => $demande->updated_at ? $demande->updated_at->format('d/m/Y H:i') : 'N/A',
                    'description' => $this->getDescriptionStatut($demande->etat)
                ]
            ];

            // Si la demande est traitée, ajouter la date de traitement
            if ($demande->etat === 'traité' && $demande->updated_at) {
                $historique[] = [
                    'statut' => 'traitement',
                    'date' => $demande->updated_at->format('d/m/Y H:i'),
                    'description' => 'Demande traitée avec succès'
                ];
            }

            // Statut de création
            $historique[] = [
                'statut' => 'création',
                'date' => $demande->created_at ? $demande->created_at->format('d/m/Y H:i') : 'N/A',
                'description' => 'Demande créée'
            ];

            // Inverser pour avoir l'ordre chronologique
            $historique = array_reverse($historique);

            return response()->json([
                'demande' => [
                    'id' => $demande->id,
                    'reference' => $demande->reference,
                    'type' => $type,
                    'statut_actuel' => $demande->etat,
                    'date_creation' => $demande->created_at ? $demande->created_at->format('d/m/Y H:i') : 'N/A',
                    'date_mise_a_jour' => $demande->updated_at ? $demande->updated_at->format('d/m/Y H:i') : 'N/A',
                ],
                'historique' => $historique,
                'prochaines_etapes' => $this->getProchainesEtapes($demande->etat)
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur suiviDemandeParReference: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }
    /**
     * Helper pour obtenir la description du statut
     */
    private function getDescriptionStatut($statut)
    {
        $descriptions = [
            'en attente' => 'Votre demande est en attente de traitement',
            'en cours' => 'Votre demande est en cours de traitement',
            'traité' => 'Votre demande a été traitée',
            'refusé' => 'Votre demande a été refusée',
            'annulé' => 'Votre demande a été annulée'
        ];

        return $descriptions[$statut] ?? 'Statut inconnu';
    }

    /**
     * Helper pour obtenir les prochaines étapes
     */
    private function getProchainesEtapes($statutActuel)
    {
        $etapes = [
            'en attente' => ['Vérification des documents', 'Validation administrative'],
            'en cours' => ['Traitement final', 'Préparation du document'],
            'traité' => ['Document prêt pour retrait/livraison'],
            'refusé' => ['Contactez le service pour plus d\'informations'],
            'annulé' => ['Demande annulée']
        ];

        return $etapes[$statutActuel] ?? [];
    }
}