<?php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Timbre;

class ComptableDemandeController extends Controller
{
    public function index()
    {
        $comptable = Auth::guard('comptable')->user();
        $commune = $comptable->communeM;

        // Récupérer les demandes en "Livraison" pour la commune
        $naissances = Naissance::where('commune', $commune)
            ->where('choix_option', 'Livraison')
            ->get()->map(function ($item) {
                $item->type_demande = 'naissance';
                $item->type_label = 'Naissance';
                $item->demandeur_nom = $item->name . ' ' . $item->prenom;
                $item->contact = $item->user->contact;
                return $item;
            });

        $deces = Deces::where('commune', $commune)
            ->where('choix_option', 'Livraison')
            ->get()->map(function ($item) {
                $item->type_demande = 'deces';
                $item->type_label = 'Décès';
                $item->demandeur_nom = $item->name . ' ' . $item->prenom; // Adapter si champs différents
                $item->contact = $item->user->contact;
                return $item;
            });

        $mariages = Mariage::where('commune', $commune)
            ->where('choix_option', 'Livraison')
            ->get()->map(function ($item) {
                $item->type_demande = 'mariage';
                $item->type_label = 'Mariage';
                $item->demandeur_nom = $item->nomEpoux . ' ' . $item->prenomEpoux; // Adapter champs
                $item->contact = $item->user->contact;
                return $item;
            });

        // Fusionner et trier
        $allDemandes = $naissances->concat($deces)->concat($mariages)->sortByDesc('created_at');

        // Calcul du Solde Restant pour affichage
        $montantTotalAjoute = 0;
        if ($comptable->finance && $comptable->finance->mairie) {
            $montantTotalAjoute = $comptable->finance->mairie->solde;
        }

        $naissDebit = Naissance::where('commune', $commune)
            ->where(function ($q) {
                $q->where('choix_option', '!=', 'Livraison')
                    ->orWhere('timbre_recupere', 1);
            })->count();

        $decesDebit = Deces::where('commune', $commune)
            ->where(function ($q) {
                $q->where('choix_option', '!=', 'Livraison')
                    ->orWhere('timbre_recupere', 1);
            })->count();

        $mariageDebit = Mariage::where('commune', $commune)
            ->where(function ($q) {
                $q->where('choix_option', '!=', 'Livraison')
                    ->orWhere('timbre_recupere', 1);
            })->count();

        $totalDebiteCount = $naissDebit + $decesDebit + $mariageDebit;
        $montantTotalDebite = $totalDebiteCount * 500;
        $montantRestant = $montantTotalAjoute - $montantTotalDebite;

        return view('comptable.demandes.index', compact('allDemandes', 'montantRestant'));
    }

    public function markRecovered($type, $id)
    {
        \Illuminate\Support\Facades\Log::info("Tentative de récupération de timbre. Type: $type, ID: $id");

        $model = null;

        switch ($type) {
            case 'naissance':
                $model = Naissance::findOrFail($id);
                break;
            case 'deces':
                $model = Deces::findOrFail($id);
                break;
            case 'mariage':
                $model = Mariage::findOrFail($id);
                break;
            default:
                \Illuminate\Support\Facades\Log::error("Type invalide: $type");
                return redirect()->back()->with('error', 'Type de demande invalide');
        }

        if ($model) {
            $model->timbre_recupere = 1; // Force 1 integer
            $saved = $model->save();

            if ($saved) {
                // Débiter le stock physique de timbres
                Timbre::create([
                    'nombre_timbre' => -1, // Débit de 1 timbre
                    'comptable_id' => Auth::guard('comptable')->id(),
                    // 'finance_id' => null, // Laisser null si c'est le comptable qui agit
                ]);

                \Illuminate\Support\Facades\Log::info("Stock timbres débité de 1.");
            }

            \Illuminate\Support\Facades\Log::info("Modèle sauvegardé? " . ($saved ? 'OUI' : 'NON'));
            \Illuminate\Support\Facades\Log::info("Nouvel état timbre_recupere: " . $model->timbre_recupere);

            return redirect()->back()->with('success', 'Timbre validé et stock débité.');
        }

        return redirect()->back()->with('error', 'Demande introuvable.');
    }
}
