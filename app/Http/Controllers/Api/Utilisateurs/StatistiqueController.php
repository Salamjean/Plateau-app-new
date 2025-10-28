<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Models\Naissance;
use App\Models\Mariage;
use App\Models\Deces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

            // Statistiques pour les naissances
            $statsNaissance = Naissance::where('user_id', $user->id)
                ->select('etat', DB::raw('COUNT(*) as count'))
                ->groupBy('etat')
                ->get()
                ->pluck('count', 'etat');

            // Statistiques pour les mariages
            $statsMariage = Mariage::where('user_id', $user->id)
                ->select('etat', DB::raw('COUNT(*) as count'))
                ->groupBy('etat')
                ->get()
                ->pluck('count', 'etat');

            // Statistiques pour les décès
            $statsDeces = Deces::where('user_id', $user->id)
                ->select('etat', DB::raw('COUNT(*) as count'))
                ->groupBy('etat')
                ->get()
                ->pluck('count', 'etat');

            return response()->json([
                'naissance' => $statsNaissance,
                'mariage' => $statsMariage,
                'deces' => $statsDeces,
                'total_demandes' => [
                    'naissance' => array_sum($statsNaissance->toArray()),
                    'mariage' => array_sum($statsMariage->toArray()),
                    'deces' => array_sum($statsDeces->toArray()),
                    'general' => array_sum($statsNaissance->toArray()) + 
                                array_sum($statsMariage->toArray()) + 
                                array_sum($statsDeces->toArray())
                ]
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

            // Récupérer toutes les demandes avec pagination
            $naissances = Naissance::where('user_id', $user->id)
                ->select('id', 'reference', 'etat', 'created_at', DB::raw("'naissance' as type"))
                ->orderBy('created_at', 'desc');

            $mariages = Mariage::where('user_id', $user->id)
                ->select('id', 'reference', 'etat', 'created_at', DB::raw("'mariage' as type"))
                ->orderBy('created_at', 'desc');

            $deces = Deces::where('user_id', $user->id)
                ->select('id', 'reference', 'etat', 'created_at', DB::raw("'deces' as type"))
                ->orderBy('created_at', 'desc');

            // Union des trois requêtes
            $toutesDemandes = $naissances->union($mariages)->union($deces)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'demandes' => $toutesDemandes->items(),
                'pagination' => [
                    'current_page' => $toutesDemandes->currentPage(),
                    'last_page' => $toutesDemandes->lastPage(),
                    'per_page' => $toutesDemandes->perPage(),
                    'total' => $toutesDemandes->total(),
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