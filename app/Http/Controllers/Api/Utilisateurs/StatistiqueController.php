<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Models\Naissance;
use App\Models\Mariage;
use App\Models\Livreur;
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

            // --- Statistiques par ÉTAT (etat) ---
            $statsNaissanceRaw = Naissance::where('user_id', $user->id)
                ->select('etat', DB::raw('COUNT(*) as count'))
                ->groupBy('etat')
                ->get()
                ->pluck('count', 'etat');

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

            // --- DEBUG: Voir les états réels dans la base ---
            $debugEtats = [
                'naissance_etats' => Naissance::where('user_id', $user->id)->pluck('etat')->all(),
                'mariage_etats' => Mariage::where('user_id', $user->id)->pluck('etat')->all(),
                'deces_etats' => Deces::where('user_id', $user->id)->pluck('etat')->all()
            ];
            // Log::info('Debug États:', $debugEtats);

            // --- Définir explicitement les états considérés comme "en cours" ---
            $statutsEnCours = ['en traitement', 'en cours', 'validé', 'en cours de traitement','réçu','terminé']; // Ajoutez les vôtres
            
            // --- Liste des états à exclure des totaux ---
            $etatsExclus = ['paiement_echoue', 'rejetée']; // <-- MODIFIÉ

            // --- Formatage des statistiques par ÉTAT ---
            $statsNaissance = [
                'en cours' => $statsNaissanceRaw->only($statutsEnCours)->sum(),
                'terminé'  => $statsNaissanceRaw->get('terminé', 0),
                'en attente' => $statsNaissanceRaw->get('en attente', 0),
                'paiement_echoue' => $statsNaissanceRaw->get('paiement_echoue', 0),
                'rejetée' => $statsNaissanceRaw->get('rejetée', 0), // <-- AJOUTÉ
                // On somme tout SAUF les états exclus
                'total'    => $statsNaissanceRaw->except($etatsExclus)->sum() // <-- MODIFIÉ
            ];

            $statsMariage = [
                'en cours' => $statsMariageRaw->only($statutsEnCours)->sum(),
                'terminé'  => $statsMariageRaw->get('terminé', 0),
                'en attente' => $statsMariageRaw->get('en attente', 0),
                'paiement_echoue' => $statsMariageRaw->get('paiement_echoue', 0),
                'rejetée' => $statsMariageRaw->get('rejetée', 0), // <-- AJOUTÉ
                 // On somme tout SAUF les états exclus
                'total'    => $statsMariageRaw->except($etatsExclus)->sum() // <-- MODIFIÉ
            ];

            $statsDeces = [
                'en cours' => $statsDecesRaw->only($statutsEnCours)->sum(),
                'terminé'  => $statsDecesRaw->get('terminé', 0),
                'en attente' => $statsDecesRaw->get('en attente', 0),
                'paiement_echoue' => $statsDecesRaw->get('paiement_echoue', 0),
                'rejetée' => $statsDecesRaw->get('rejetée', 0), // <-- AJOUTÉ
                 // On somme tout SAUF les états exclus
                'total'    => $statsDecesRaw->except($etatsExclus)->sum() // <-- MODIFIÉ
            ];

            // --- Statistiques par LIVRAISON ---
            $statsNaissanceLivre = Naissance::where('user_id', $user->id)
                ->select('statut_livraison', DB::raw('COUNT(*) as count'))
                ->groupBy('statut_livraison')
                ->get()
                ->pluck('count', 'statut_livraison');

            $statsMariageLivre = Mariage::where('user_id', $user->id)
                ->select('statut_livraison', DB::raw('COUNT(*) as count'))
                ->groupBy('statut_livraison')
                ->get()
                ->pluck('count', 'statut_livraison');

            $statsDecesLivre = Deces::where('user_id', $user->id)
                ->select('statut_livraison', DB::raw('COUNT(*) as count'))
                ->groupBy('statut_livraison')
                ->get()
                ->pluck('count', 'statut_livraison');


            // --- Formatage des statistiques par LIVRAISON ---
            $statsNaissanceL = [
                'livré' => $statsNaissanceLivre->get('livré', 0),
            ];

            $statsMariageL = [
                'livré' => $statsMariageLivre->get('livré', 0),
            ];

            $statsDecesL = [
                'livré' => $statsDecesLivre->get('livré', 0),
            ];

                    // --- Calcul des TOTAUX agrégés ---
            $totalTermine = $statsNaissance['terminé'] + $statsMariage['terminé'] + $statsDeces['terminé'];

            // D'ABORD calculer totalLivre
            $totalLivre = $statsNaissanceL['livré'] + $statsMariageL['livré'] + $statsDecesL['livré'];

            // MODIFICATION : Calculer en_cours en excluant les demandes livrées
            $totalEnCours = ($statsNaissance['en cours'] + $statsMariage['en cours'] + $statsDeces['en cours']) - $totalLivre;
            // Assurer que le total ne soit pas négatif
            $totalEnCours = max(0, $totalEnCours);

            // ENSUITE l'utiliser dans totalGeneral
            $totalGeneral = $statsNaissance['total'] + $statsMariage['total'] + $statsDeces['total'] + $totalLivre;

            $totalPaiementEchoue = $statsNaissance['paiement_echoue'] + $statsMariage['paiement_echoue'] + $statsDeces['paiement_echoue'];
            $totalRejetee = $statsNaissance['rejetée'] + $statsMariage['rejetée'] + $statsDeces['rejetée'];

            // --- Réponse JSON ---
            return response()->json([
                // Détails par catégorie - ÉTAT
                'naissance' => $statsNaissance,
                'mariage'   => $statsMariage,
                'deces'     => $statsDeces,
                
                // Détails par catégorie - LIVRAISON
                'livraison_naissance' => $statsNaissanceL,
                'livraison_mariage'   => $statsMariageL,
                'livraison_deces'     => $statsDecesL,
                
                // Totaux agrégés
                'total_general'     => $totalGeneral,
                'en_cours'          => $totalEnCours,
                // 'termine'           => $totalTermine,
                'total_livre'       => $totalLivre,
                'paiement_echoue'   => $totalPaiementEchoue,
                'rejetee'           => $totalRejetee, // <-- AJOUTÉ
                
                // Debug (à enlever en production)
                'debug_etats' => $debugEtats
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur statistiquesParStatut: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }
   
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

        // Si l'état est "en attente", le statut est "en attente"
        elseif ($demande->etat === 'en attente') {
            return 'en attente';
        }

        // Statut par défaut si aucune condition n'est remplie
        return 'en attente';
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

            // --- MODIFICATION (1) : Récupérer le filtre de statut depuis l'URL ---
            $statutFiltre = $request->input('statut');

            // --- MODIFICATION (2) : Préparer les requêtes de base ---
            $queryNaissances = Naissance::where('user_id', $user->id);
            $queryMariages = Mariage::where('user_id', $user->id);
            $queryDeces = Deces::where('user_id', $user->id);

            // --- MODIFICATION (3) : Appliquer le filtre si il est fourni ---
            if ($statutFiltre) {
                // On filtre sur la colonne 'etat' de la base de données
                $queryNaissances->where('etat', $statutFiltre);
                $queryMariages->where('etat', $statutFiltre);
                $queryDeces->where('etat', $statutFiltre);
            }
            
            // --- MODIFICATION (4) : Exécuter les requêtes (maintenant filtrées) ---
            
            // Naissances
            $naissances = $queryNaissances->get() // Utilise la requête préparée
                ->map(function ($item) {
                    $item->type_demande = 'naissance';
                    $item->statut = $this->calculerStatut($item); // Calcule le statut personnalisé
                    return $item;
                });

            // Mariages
            $mariages = $queryMariages->get() // Utilise la requête préparée
                ->map(function ($item) {
                    $item->type_demande = 'mariage';
                    $item->statut = $this->calculerStatut($item);
                    return $item;
                });

            // Décès
            $deces = $queryDeces->get() // Utilise la requête préparée
                ->map(function ($item) {
                    $item->type_demande = 'deces';
                    $item->statut = $this->calculerStatut($item);
                    return $item;
                });

            // Le reste de la fonction ne change pas
            $toutesDemandes = $naissances->concat($mariages)->concat($deces);
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
                // On renvoie 404 pour ne pas indifier si la référence existe ou non (sécurité)
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
    
            // --- CORRECTION : RÉCUPÉRATION DES INFORMATIONS DU LIVREUR ---
            $livreurInfo = null;
            if ($demande->livreur_id) {
                $livreur = Livreur::select('name', 'prenom', 'contact', 'profile_picture')
                    ->where('id', $demande->livreur_id)
                    ->first();
                
                if ($livreur) {
                    $livreurInfo = [
                        'nom' => $livreur->name,
                        'prenom' => $livreur->prenom,
                        'contact' => $livreur->contact,
                        'profile_picture' => $livreur->profile_picture 
                            ? "/storage/" . $livreur->profile_picture // CORRECTION ICI : /storage/ au lieu de asset()
                            : null
                    ];
                }
            }
    
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
                'livreur' => $livreurInfo, // <-- NOUVEAU : Informations du livreur
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
            // NOTE: Ajoutez ici 'en attente de paiement', 'paiement_echoue' etc. si nécessaire
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
            // NOTE: Ajoutez ici 'en attente de paiement', 'paiement_echoue' etc. si nécessaire
        ];

        return $etapes[$statutActuel] ?? [];
    }
}