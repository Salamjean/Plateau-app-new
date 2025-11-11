<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Models\Naissance;
use App\Models\Mariage;
use App\Models\Deces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class StatistiqueController extends Controller
{
    /**
     * Statistiques des demandes par statut pour l'utilisateur connecté
     */
    public function statistiquesParStatut(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            // --- Statistiques BRUTES (par état réel de la BDD) ---

            $statsNaissanceRaw = Naissance::where('user_id', $user->id)
                ->select('etat', DB::raw('COUNT(*) as count'))
                ->groupBy('etat')
                ->get()
                ->pluck('count', 'etat'); // Ex: ['en attente' => 5, 'terminé' => 10, 'paiement échoué' => 1]

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

            // Statuts à exclure du comptage "en cours" (logique inchangée)
            $statutsExclusEnCours = ['terminé', 'en_attente_de_livraison', 'paiement échoué'];

            $statsNaissance = [
                'en cours' => $statsNaissanceRaw->except($statutsExclusEnCours)->sum(),
                'terminé'  => $statsNaissanceRaw->get('terminé', 0),
                'total'    => $statsNaissanceRaw->sum() 
            ];

            $statsMariage = [
                'en cours' => $statsMariageRaw->except($statutsExclusEnCours)->sum(),
                'terminé'  => $statsMariageRaw->get('terminé', 0),
                'total'    => $statsMariageRaw->sum()
            ];

            $statsDeces = [
                'en cours' => $statsDecesRaw->except($statutsExclusEnCours)->sum(),
                'terminé'  => $statsDecesRaw->get('terminé', 0),
                'total'    => $statsDecesRaw->sum()
            ];

            // --- 2. Calcul des TOTAUX agrégés ---

            // Total "En cours" (somme de tous les 'en cours' de chaque catégorie)
            $totalEnCours = $statsNaissance['en cours'] + $statsMariage['en cours'] + $statsDeces['en cours'];

            // Total "Terminé" (somme de tous les 'terminé')
            $totalTermine = $statsNaissance['terminé'] + $statsMariage['terminé'] + $statsDeces['terminé'];

            // Total "Général" (somme de toutes les demandes, peu importe l'état)
            $totalGeneral = $statsNaissance['total'] + $statsMariage['total'] + $statsDeces['total'];
            
            // <-- NOUVELLE LIGNE : Calcul du total des paiements échoués
            $totalPaiementEchoue = $statsNaissanceRaw->get('paiement échoué', 0) 
                                 + $statsMariageRaw->get('paiement échoué', 0) 
                                 + $statsDecesRaw->get('paiement échoué', 0);

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
                'paiement_echoue' => $totalPaiementEchoue, // <-- NOUVELLE LIGNE AJOUTÉE
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur statistiquesParStatut: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }
    
    // ... (Le reste de votre fichier reste inchangé) ...
    // getDemandeSpecifique, calculerStatut, listeToutesDemandes, etc.
    
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

            // Calcul du statut selon la logique demandée
            $statut = $this->calculerStatut($demande);

            // Ajout du statut calculé à la demande
            $demande->statut = $statut;

            return response()->json([
                'demande' => $demande,
                'type' => $type,
                'statut' => $statut
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur getDemandeSpecifique: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Calcule le statut d'une demande selon la logique métier
     */
    private function calculerStatut($demande)
    {
        // Si le statut de livraison est "livré", c'est la priorité maximale
        if ($demande->statut_livraison === 'livré') {
            return 'livrer';
        }
        // Si choix_option == "livraison" ET livreur_id existe → "en_cours_livraison"
        elseif ($demande->choix_option === 'livraison' && $demande->livreur_id) {
            return 'en_cours_livraison';
        }
        // Si agent_id existe, le statut est "recuperer"
        elseif ($demande->agent_id) {
            return 'recuperer';
        }

        // Si l'état est "en attente", le statut est "en_attente"
        elseif ($demande->etat === 'en attente') {
            return 'en_attente';
        }

        // Statut par défaut si aucune condition n'est remplie
        return 'en_attente';
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

            // Naissances
            $naissances = Naissance::where('user_id', $user->id)
                ->get()
                ->map(function ($item) {
                    $item->type_demande = 'naissance';
                    $item->statut = $this->calculerStatut($item);
                    return $item;
                });

            // Mariages
            $mariages = Mariage::where('user_id', $user->id)
                ->get()
                ->map(function ($item) {
                    $item->type_demande = 'mariage';
                    $item->statut = $this->calculerStatut($item);
                    return $item;
                });

            // Décès
            $deces = Deces::where('user_id', $user->id)
                ->get()
                ->map(function ($item) {
                    $item->type_demande = 'deces';
                    $item->statut = $this->calculerStatut($item);
                    return $item;
                });

                
    
            // Fusionner correctement les collections
            $toutesDemandes = $naissances->concat($mariages)->concat($deces);
            
            // Trier par created_at descendant
            $demandesTriees = $toutesDemandes->sortByDesc('created_at')->values();

            return response()->json([
                'demandes' => $demandesTriees->toArray(),
                'total' => $demandesTriees->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur listeToutesDemandes: ' . $e->getMessage());
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
            Log::error('Erreur suiviDemande: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    public function suiviDemandeParReference(Request $request)
    {
        try {
            $user = $request->user();
            
            // 1. Vérifier l'authentification
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
            
            // --- MODIFICATION AJOUTÉE ---
            // 1. Calculer le statut personnalisé en utilisant la logique métier
            $statutCalcule = $this->calculerStatut($demande);

            // 2. Convertir le modèle en tableau pour pouvoir y ajouter nos champs
            $demandeData = $demande->toArray();
            
            // 3. Ajout du type pour que le client sache de quel modèle il s'agit
            $demandeData['type_demande'] = $type; 
            
            // 4. Ajout du statut calculé à l'objet demande
            $demandeData['statut'] = $statutCalcule;
            // --- FIN MODIFICATION ---


            // 4. Logique de construction de l'historique
            $historique = [
                [
                    'statut' => $demande->etat,
                    'date' => $demande->updated_at ? $demande->updated_at->format('d/m/Y H:i') : 'N/A',
                    'description' => $this->getDescriptionStatut($demande->etat)
                ]
            ];

            if ($demande->etat === 'traité' && $demande->updated_at) {
                $historique[] = [
                    'statut' => 'traitement',
                    'date' => $demande->updated_at->format('d/m/Y H:i'),
                    'description' => 'Demande traitée avec succès'
                ];
            }

            $historique[] = [
                'statut' => 'création',
                'date' => $demande->created_at ? $demande->created_at->format('d/m/Y H:i') : 'N/A',
                'description' => 'Demande créée'
            ];

            $historique = array_reverse($historique);

            // --- MODIFICATION DE LA RÉPONSE ---
            return response()->json([
                // $demandeData contient maintenant 'type_demande' et 'statut_calcule'
                'demande' => $demandeData, 
                'historique' => $historique,
                'prochaines_etapes' => $this->getProchainesEtapes($demande->etat),
                // Ajout du statut en tant que clé de premier niveau pour un accès facile
                'statut' => $statutCalcule 
            ]);
            // --- FIN MODIFICATION DE LA RÉPONSE ---

        } catch (\Exception $e) {
            Log::error('Erreur suiviDemandeParReference: ' . $e->getMessage());
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
            // NOTE: Ajoutez ici 'en attente de paiement', 'paiement échoué' etc. si nécessaire
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
            // NOTE: Ajoutez ici 'en attente de paiement', 'paiement échoué' etc. si nécessaire
        ];

        return $etapes[$statutActuel] ?? [];
    }
}