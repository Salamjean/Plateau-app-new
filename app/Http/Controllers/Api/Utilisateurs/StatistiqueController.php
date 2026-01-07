<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Models\Naissance;
use App\Models\Mariage;
use App\Models\Livreur;
use App\Models\Deces;
use App\Models\Paiement;
use Illuminate\Http\Request;
use App\Models\Rendezvous;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

            // --- Définir explicitement les états considérés comme "en cours" ---
            $statutsEnCours = ['en traitement', 'en cours', 'validé', 'en cours de traitement', 'réçu'];

            // --- MODIFICATION : Ajouter 'en attente de paiement' dans les états exclus ---
            $etatsExclus = ['paiement_echoue', 'rejetée', 'en attente de paiement'];

            // --- Formatage des statistiques par ÉTAT ---
            $statsNaissance = [
                'en cours' => $statsNaissanceRaw->only($statutsEnCours)->sum(),
                'terminé'  => $statsNaissanceRaw->get('terminé', 0),
                'en attente' => $statsNaissanceRaw->get('en attente', 0),
                'paiement_echoue' => $statsNaissanceRaw->get('paiement_echoue', 0),
                'rejetée' => $statsNaissanceRaw->get('rejetée', 0),
                'en attente de paiement' => $statsNaissanceRaw->get('en attente de paiement', 0), // Pour information seulement
                'total'    => $statsNaissanceRaw->except($etatsExclus)->sum()
            ];

            $statsMariage = [
                'en cours' => $statsMariageRaw->only($statutsEnCours)->sum(),
                'terminé'  => $statsMariageRaw->get('terminé', 0),
                'en attente' => $statsMariageRaw->get('en attente', 0),
                'paiement_echoue' => $statsMariageRaw->get('paiement_echoue', 0),
                'rejetée' => $statsMariageRaw->get('rejetée', 0),
                'en attente de paiement' => $statsMariageRaw->get('en attente de paiement', 0), // Pour information seulement
                'total'    => $statsMariageRaw->except($etatsExclus)->sum()
            ];

            $statsDeces = [
                'en cours' => $statsDecesRaw->only($statutsEnCours)->sum(),
                'terminé'  => $statsDecesRaw->get('terminé', 0),
                'en attente' => $statsDecesRaw->get('en attente', 0),
                'paiement_echoue' => $statsDecesRaw->get('paiement_echoue', 0),
                'rejetée' => $statsDecesRaw->get('rejetée', 0),
                'en attente de paiement' => $statsDecesRaw->get('en attente de paiement', 0), // Pour information seulement
                'total'    => $statsDecesRaw->except($etatsExclus)->sum()
            ];

            // --- Statistiques par LIVRAISON pour les demandes terminées ---
            $statsNaissanceLivraison = Naissance::where('user_id', $user->id)
                ->where('etat', 'terminé')
                ->select('statut_livraison', DB::raw('COUNT(*) as count'))
                ->groupBy('statut_livraison')
                ->get()
                ->pluck('count', 'statut_livraison');

            $statsMariageLivraison = Mariage::where('user_id', $user->id)
                ->where('etat', 'terminé')
                ->select('statut_livraison', DB::raw('COUNT(*) as count'))
                ->groupBy('statut_livraison')
                ->get()
                ->pluck('count', 'statut_livraison');

            $statsDecesLivraison = Deces::where('user_id', $user->id)
                ->where('etat', 'terminé')
                ->select('statut_livraison', DB::raw('COUNT(*) as count'))
                ->groupBy('statut_livraison')
                ->get()
                ->pluck('count', 'statut_livraison');

            // --- Formatage des statistiques par LIVRAISON ---
            $statsNaissanceL = [
                'en attente' => $statsNaissanceLivraison->get('en attente', 0),
                'en cours' => $statsNaissanceLivraison->get('en cours', 0),
                'livré' => $statsNaissanceLivraison->get('livré', 0),
            ];

            $statsMariageL = [
                'en attente' => $statsMariageLivraison->get('en attente', 0),
                'en cours' => $statsMariageLivraison->get('en cours', 0),
                'livré' => $statsMariageLivraison->get('livré', 0),
            ];

            $statsDecesL = [
                'en attente' => $statsDecesLivraison->get('en attente', 0),
                'en cours' => $statsDecesLivraison->get('en cours', 0),
                'livré' => $statsDecesLivraison->get('livré', 0),
            ];

            // --- Calcul des TOTAUX agrégés ---
            $totalTermine = $statsNaissance['terminé'] + $statsMariage['terminé'] + $statsDeces['terminé'];
            $totalLivre = $statsNaissanceL['livré'] + $statsMariageL['livré'] + $statsDecesL['livré'];

            // MODIFICATION : Calcul du total des en_cours (sans 'en attente de paiement')
            $totalEnCours =
                // Demandes avec état dans statutsEnCours (en traitement, en cours, validé, réçu, etc.)
                ($statsNaissance['en cours'] + $statsMariage['en cours'] + $statsDeces['en cours']) +
                // Demandes terminées avec livraison en attente
                ($statsNaissanceL['en attente'] + $statsMariageL['en attente'] + $statsDecesL['en attente']) +
                // Demandes terminées avec livraison en cours
                ($statsNaissanceL['en cours'] + $statsMariageL['en cours'] + $statsDecesL['en cours']);

            // MODIFICATION : Le total général exclut maintenant 'en attente de paiement'
            $totalGeneral = $statsNaissance['total'] + $statsMariage['total'] + $statsDeces['total'];

            $totalPaiementEchoue = $statsNaissance['paiement_echoue'] + $statsMariage['paiement_echoue'] + $statsDeces['paiement_echoue'];
            $totalRejetee = $statsNaissance['rejetée'] + $statsMariage['rejetée'] + $statsDeces['rejetée'];
            $totalAttentePaiement = $statsNaissance['en attente de paiement'] + $statsMariage['en attente de paiement'] + $statsDeces['en attente de paiement'];

            // --- Réponse JSON ---
            return response()->json([
                // Détails par catégorie - ÉTAT
                'naissance' => $statsNaissance,
                'mariage'   => $statsMariage,
                'deces'     => $statsDeces,

                // Détails par catégorie - LIVRAISON (pour les demandes terminées)
                'livraison_naissance' => $statsNaissanceL,
                'livraison_mariage'   => $statsMariageL,
                'livraison_deces'     => $statsDecesL,

                // Totaux agrégés
                'total_general'     => $totalGeneral,
                'en_cours'          => $totalEnCours,
                'total_livre'       => $totalLivre,
                'paiement_echoue'   => $totalPaiementEchoue,
                'rejetee'           => $totalRejetee,
                'en_attente_paiement' => $totalAttentePaiement, // Pour information

                // Détails supplémentaires pour vérification
                'details_calcul_en_cours' => [
                    'etats_en_cours' => $statsNaissance['en cours'] + $statsMariage['en cours'] + $statsDeces['en cours'],
                    'termine_livraison_attente' => $statsNaissanceL['en attente'] + $statsMariageL['en attente'] + $statsDecesL['en attente'],
                    'termine_livraison_cours' => $statsNaissanceL['en cours'] + $statsMariageL['en cours'] + $statsDecesL['en cours']
                ]
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

            // --- MODIFICATION (2) : Préparer les requêtes de base avec exclusion de 'en attente de paiement' ---
            $queryNaissances = Naissance::where('user_id', $user->id)
                ->where('etat', '!=', 'en attente de paiement');

            $queryMariages = Mariage::where('user_id', $user->id)
                ->where('etat', '!=', 'en attente de paiement');

            $queryDeces = Deces::where('user_id', $user->id)
                ->where('etat', '!=', 'en attente de paiement');

            // --- MODIFICATION (3) : Appliquer le filtre si il est fourni ---
            if ($statutFiltre) {
                // On filtre sur la colonne 'etat' de la base de données
                $queryNaissances->where('etat', $statutFiltre);
                $queryMariages->where('etat', $statutFiltre);
                $queryDeces->where('etat', $statutFiltre);
            }

            // --- MODIFICATION (4) : Exécuter les requêtes (maintenant filtrées) ---

            // Naissances
            $naissances = $queryNaissances->get()
                ->map(function ($item) {
                    $item->type_demande = 'naissance';
                    $item->statut = $this->calculerStatut($item);
                    return $item;
                });

            // Mariages
            $mariages = $queryMariages->get()
                ->map(function ($item) {
                    $item->type_demande = 'mariage';
                    $item->statut = $this->calculerStatut($item);
                    return $item;
                });

            // Décès
            $deces = $queryDeces->get()
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

            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            $reference = $request->input('reference');

            if (empty($reference)) {
                return response()->json(['error' => 'La référence de la demande est requise'], 400);
            }

            // Recherche dans tous les modèles sans restriction d'utilisateur
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

            // Si aucune demande trouvée avec cette référence
            if (!$demande) {
                return response()->json(['error' => 'Aucune demande trouvée avec cette référence'], 404);
            }

            // Calculer le statut personnalisé
            $statutCalcule = $this->calculerStatut($demande);

            // Convertir le modèle en tableau
            $demandeData = $demande->toArray();
            $demandeData['type_demande'] = $type;
            $demandeData['statut'] = $statutCalcule;

            // Ajouter une information sur la propriété de la demande
            $demandeData['appartient_a_utilisateur'] = $demande->user_id === $user->id;

            // RÉCUPÉRATION DU PAIEMENT
            $paiementInfo = null;

            if ($type === 'naissance') {
                $paiementInfo = Paiement::where('naissance_id', $demande->id)->first();
            } elseif ($type === 'mariage') {
                $paiementInfo = Paiement::where('mariage_id', $demande->id)->first();
            } elseif ($type === 'deces') {
                $paiementInfo = Paiement::where('deces_id', $demande->id)->first();
            }

            // RÉCUPÉRATION DES INFORMATIONS DU LIVREUR
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
                            ? "/storage/" . $livreur->profile_picture
                            : null
                    ];
                }
            }

            // Construction de l'historique
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

            // DOCUMENTS
            $documents = [];

            if ($type === 'naissance') {
                if ($demande->CNI) {
                    $documents[] = [
                        'uri' => "/storage/" . $demande->CNI,
                        'type' => 'image',
                        'nom' => "Carte d'identité"
                    ];
                }
            } elseif ($type === 'mariage') {
                if ($demande->pieceIdentite) {
                    $documents[] = [
                        'uri' => "/storage/" . $demande->pieceIdentite,
                        'type' => 'image',
                        'nom' => "Pièce d'identité"
                    ];
                }
                if ($demande->extraitMariage) {
                    $documents[] = [
                        'uri' => "/storage/" . $demande->extraitMariage,
                        'type' => 'image',
                        'nom' => "Extrait de mariage"
                    ];
                }
            } elseif ($type === 'deces') {
                if ($demande->CNIdfnt) {
                    $documents[] = [
                        'uri' => "/storage/" . $demande->CNIdfnt,
                        'type' => 'image',
                        'nom' => "CNI du défunt"
                    ];
                }
                if ($demande->CNIdcl) {
                    $documents[] = [
                        'uri' => "/storage/" . $demande->CNIdcl,
                        'type' => 'image',
                        'nom' => "CNI du déclarant"
                    ];
                }
                if ($demande->documentMariage) {
                    $documents[] = [
                        'uri' => "/storage/" . $demande->documentMariage,
                        'type' => 'image',
                        'nom' => "Document de mariage"
                    ];
                }
                if ($demande->RequisPolice) {
                    $documents[] = [
                        'uri' => "/storage/" . $demande->RequisPolice,
                        'type' => 'image',
                        'nom' => "Réquisition de police"
                    ];
                }
            }

            // RÉPONSE JSON
            return response()->json([
                'demande' => $demandeData,
                'paiement' => $paiementInfo,
                'livreur' => $livreurInfo,
                'document' => $documents,
                'historique' => $historique,
                'prochaines_etapes' => $this->getProchainesEtapes($demande->etat),
                'statut' => $statutCalcule
            ]);
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
    /**
     * Liste l'historique des paiements avec les détails de la demande associée
     */
    public function historiquePaiements(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            // Récupérer les paiements de l'utilisateur avec les relations chargées
            $paiements = Paiement::where('user_id', $user->id)
                ->with(['naissance', 'mariage', 'deces']) // On charge les données liées
                ->orderBy('created_at', 'desc') // Les plus récents en premier
                ->get();

            // Formatage des données pour que ce soit propre en JSON
            $historique = $paiements->map(function ($paiement) {

                // On détermine quel type de demande est lié à ce paiement
                $detailsDemande = null;
                $typeDemande = null;

                if ($paiement->naissance) {
                    $detailsDemande = $paiement->naissance;
                    $typeDemande = 'naissance';
                } elseif ($paiement->mariage) {
                    $detailsDemande = $paiement->mariage;
                    $typeDemande = 'mariage';
                } elseif ($paiement->deces) {
                    $detailsDemande = $paiement->deces;
                    $typeDemande = 'deces';
                }

                return [
                    'id_paiement' => $paiement->id,
                    'transaction_id' => $paiement->transaction_id ?? 'N/A',
                    'montant' => $paiement->montant,
                    'date_paiement' => $paiement->created_at->format('d/m/Y H:i'),
                    'status' => $paiement->status,
                    'operateur' => $paiement->operator_id,

                    // Information cruciale : quel type de document a été payé
                    'type_demande' => $typeDemande,

                    // Ici on injecte tout l'objet de la demande (Naissance, Mariage ou Décès)
                    'details_demande' => $detailsDemande
                ];
            });

            return response()->json([
                'total' => $historique->count(),
                'paiements' => $historique
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur historiquePaiements: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }
    public function getPaiementByTransaction(Request $request, $transactionId)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            // Recherche par transaction_id ET user_id (sécurité)
            $paiement = Paiement::where('transaction_id', $transactionId)
                ->where('user_id', $user->id)
                ->with(['naissance', 'mariage', 'deces'])
                ->first();

            if (!$paiement) {
                return response()->json(['error' => 'Transaction introuvable ou non autorisée'], 404);
            }

            // Déterminer le type de demande liée
            $detailsDemande = null;
            $typeDemande = null;

            if ($paiement->naissance) {
                $detailsDemande = $paiement->naissance;
                $typeDemande = 'naissance';
            } elseif ($paiement->mariage) {
                $detailsDemande = $paiement->mariage;
                $typeDemande = 'mariage';
            } elseif ($paiement->deces) {
                $detailsDemande = $paiement->deces;
                $typeDemande = 'deces';
            }

            // Construction de la réponse
            $response = [
                'id_paiement' => $paiement->id,
                'transaction_id' => $paiement->transaction_id,
                // 'operateur_reference' => $paiement->payment_token, // Souvent utile pour le support
                'montant' => $paiement->montant,
                'currency' => $paiement->currency,
                'date_paiement' => $paiement->created_at->format('d/m/Y H:i'),
                'status' => $paiement->status,
                // 'moyen_paiement' => $paiement->operator_id, // Orange Money, MTN, etc.

                // Détails de la demande liée
                'type_demande' => $typeDemande,
                'details_demande' => $detailsDemande
            ];

            return response()->json(['paiement' => $response]);
        } catch (\Exception $e) {
            Log::error('Erreur getPaiementByTransaction: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }
    /**
     * Récupérer les statistiques d'une demande de rendez-vous spécifique par ID
     */
    public function statistiquesRendezvousParId(Request $request, $id)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            // Récupérer la demande de rendez-vous spécifique
            $rendezvous = Rendezvous::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$rendezvous) {
                return response()->json(['error' => 'Rendez-vous non trouvé'], 404);
            }

            // Statistiques générales des rendez-vous de l'utilisateur
            $statsRendezvous = Rendezvous::where('user_id', $user->id)
                ->select('statut', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
                ->groupBy('statut')
                ->get()
                ->pluck('count', 'statut');

            // Calcul des totaux
            $totalRendezvous = $statsRendezvous->sum();
            $totalConfirmes = $statsRendezvous->get('confirmé', 0);
            $totalEnAttente = $statsRendezvous->get('en attente', 0);
            $totalAnnules = $statsRendezvous->get('annulé', 0);

            return response()->json(

                $rendezvous


            );
        } catch (\Exception $e) {
            Log::error('Erreur statistiquesRendezvousParId: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Statistiques complètes des rendez-vous avec pagination
     */
    public function statistiquesRendezvousComplet(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            // Récupération avec pagination
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            $rendezvousQuery = Rendezvous::where('user_id', $user->id);

            // Filtre par statut si fourni
            if ($request->has('statut')) {
                $rendezvousQuery->where('statut', $request->input('statut'));
            }

            // Tri
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $rendezvousQuery->orderBy($sortBy, $sortOrder);

            $rendezvousPaginated = $rendezvousQuery->paginate($perPage, ['*'], 'page', $page);

            // Statistiques générales
            $statsRendezvous = Rendezvous::where('user_id', $user->id)
                ->select('statut', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
                ->groupBy('statut')
                ->get()
                ->pluck('count', 'statut');

            $totalRendezvous = $statsRendezvous->sum();
            $totalConfirmes = $statsRendezvous->get('confirmé', 0);
            $totalEnAttente = $statsRendezvous->get('en attente', 0);
            $totalAnnules = $statsRendezvous->get('annulé', 0);

            // Statistiques par mois (pour graphiques)
            $statsParMois = Rendezvous::where('user_id', $user->id)
                ->select(
                    \Illuminate\Support\Facades\DB::raw('YEAR(created_at) as year'),
                    \Illuminate\Support\Facades\DB::raw('MONTH(created_at) as month'),
                    \Illuminate\Support\Facades\DB::raw('COUNT(*) as count')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get();

            return response()->json([
                'statistiques_generales' => [
                    'total' => $totalRendezvous,
                    'confirmes' => $totalConfirmes,
                    'en_attente' => $totalEnAttente,
                    'annules' => $totalAnnules,
                    'taux_confirmation' => $totalRendezvous > 0 ? round(($totalConfirmes / $totalRendezvous) * 100, 2) : 0,
                    'details_par_statut' => $statsRendezvous->toArray()
                ],
                'evolution_mensuelle' => $statsParMois,
                'liste_rendezvous' => $rendezvousPaginated->items(),
                'pagination' => [
                    'current_page' => $rendezvousPaginated->currentPage(),
                    'last_page' => $rendezvousPaginated->lastPage(),
                    'per_page' => $rendezvousPaginated->perPage(),
                    'total' => $rendezvousPaginated->total(),
                    'from' => $rendezvousPaginated->firstItem(),
                    'to' => $rendezvousPaginated->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur statistiquesRendezvousComplet: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Récupérer les détails d'un rendez-vous spécifique avec informations étendues
     */
    public function getRendezvousDetails(Request $request, $id)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            $rendezvous = Rendezvous::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$rendezvous) {
                return response()->json(['error' => 'Rendez-vous non trouvé'], 404);
            }

            // Récupérer les rendez-vous similaires (même statut)
            $rendezvousSimilaires = Rendezvous::where('user_id', $user->id)
                ->where('statut', $rendezvous->statut)
                ->where('id', '!=', $id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'rendezvous' => $rendezvous,
                'informations_complementaires' => [
                    'duree_attente' => $this->calculerDureeAttente($rendezvous),
                    'prochaines_etapes' => $this->getProchainesEtapesRendezvous($rendezvous->statut),
                    'contact_urgence' => [
                        'email' => $rendezvous->email,
                        'telephone' => $rendezvous->telephone
                    ]
                ],
                'rendezvous_similaires' => $rendezvousSimilaires,
                'statistiques_rapides' => [
                    'total_rendezvous' => Rendezvous::where('user_id', $user->id)->count(),
                    'rendezvous_meme_mairie' => Rendezvous::where('user_id', $user->id)
                        ->where('mairie', $rendezvous->mairie)
                        ->count(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur getRendezvousDetails: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Calculer la durée d'attente d'un rendez-vous
     */
    private function calculerDureeAttente($rendezvous)
    {
        if ($rendezvous->statut === 'confirmé') {
            return 'Rendez-vous confirmé';
        }

        $dateCreation = $rendezvous->created_at;
        $maintenant = now();
        $difference = $dateCreation->diff($maintenant);

        if ($difference->days > 0) {
            return $difference->days . ' jour(s) d\'attente';
        } elseif ($difference->h > 0) {
            return $difference->h . ' heure(s) d\'attente';
        } else {
            return 'Moins d\'une heure d\'attente';
        }
    }

    /**
     * Obtenir les prochaines étapes pour un rendez-vous
     */
    private function getProchainesEtapesRendezvous($statut)
    {
        $etapes = [
            'en attente' => [
                'Vérification des disponibilités',
                'Confirmation par la mairie',
                'Réception de la confirmation par email'
            ],
            'confirmé' => [
                'Préparation des documents nécessaires',
                'Présentation à la mairie à la date convenue',
                'Paiement des frais éventuels'
            ],
            'annulé' => [
                'Contactez la mairie pour plus d\'informations',
                'Possibilité de reprogrammer'
            ]
        ];

        return $etapes[$statut] ?? ['Statut non reconnu'];
    }
}
