<?php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\DecesGroupe;
use App\Models\Mariage;
use App\Models\MariageGroupe;
use App\Models\Naissance;
use App\Models\NaissanceGroupe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Timbre;

class ComptableDemandeController extends Controller
{
    public function index()
    {
        $comptable = Auth::guard('comptable')->user();
        $commune = $comptable->communeM;

        $mapNaissance = function ($item) {
            $item->type_demande = 'naissance';
            $item->demandeur_nom = ($item->user->name ?? '') . ' ' . ($item->user->prenom ?? '');
            $item->nom_sur_extrait = trim(($item->name ?? '') . ' ' . ($item->prenom ?? ''));
            $item->contact = $item->user->contact ?? '';
            return $item;
        };

        $mapDeces = function ($item) {
            $item->type_demande = 'deces';
            $item->demandeur_nom = ($item->user->name ?? '') . ' ' . ($item->user->prenom ?? '');
            $item->nom_sur_extrait = trim($item->name ?? '');
            $item->contact = $item->user->contact ?? '';
            return $item;
        };

        $mapMariage = function ($item) {
            $item->type_demande = 'mariage';
            $item->demandeur_nom = ($item->user->name ?? '') . ' ' . ($item->user->prenom ?? '');
            $item->nom_sur_extrait = trim(($item->nomEpoux ?? '') . ' ' . ($item->prenomEpoux ?? '')) . ' & ' . trim(($item->nomEpouse ?? '') . ' ' . ($item->prenomEpouse ?? ''));
            $item->contact = $item->user->contact ?? '';
            return $item;
        };


        // Filtre : Livraison OU Retrait sur place / Retrait (toutes les demandes en ligne)
        // Utilise LOWER() pour être insensible à la casse
        $filterRequests = function ($query) {
            $query->whereIn(DB::raw('LOWER(choix_option)'), ['livraison', 'retrait sur place', 'retrait'])
                ->orWhereNull('choix_option');
        };

        $naissances = Naissance::where('commune', $commune)
            ->where($filterRequests)
            ->paye()
            ->get()->map($mapNaissance);

        $deces = Deces::where('commune', $commune)
            ->where($filterRequests)
            ->paye()
            ->get()->map($mapDeces);

        $mariages = Mariage::where('commune', $commune)
            ->where($filterRequests)
            ->paye()
            ->get()->map($mapMariage);

        $all = $naissances->concat($deces)->concat($mariages);

        // Toggle : séparation par timbre_recupere
        $demandesEnAttente = $all->where('timbre_recupere', 0)->sortByDesc('created_at')->values();
        $demandesTraitees = $all->where('timbre_recupere', 1)->sortByDesc('created_at')->values();
        $allDemandes = $demandesEnAttente->concat($demandesTraitees);

        // Solde Argent
        $montantRestant = 0;
        if ($comptable->finance && $comptable->finance->mairie) {
            $montantRestant = $comptable->finance->mairie->solde;
        }

        // Solde Timbres
        $soldeTimbres = Timbre::sum('nombre_timbre');

        return view('comptable.demandes.index', compact(
            'allDemandes',
            'demandesEnAttente',
            'demandesTraitees',
            'montantRestant',
            'soldeTimbres'
        ));
    }

    public function markRecovered($type, $id)
    {
        \Illuminate\Support\Facades\Log::info("Récupération timbre. Type: $type, ID: $id");

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
            case 'naissance_groupe':
                $model = NaissanceGroupe::findOrFail($id);
                break;
            case 'deces_groupe':
                $model = DecesGroupe::findOrFail($id);
                break;
            case 'mariage_groupe':
                $model = MariageGroupe::findOrFail($id);
                break;
            default:
                return redirect()->back()->with('error', 'Type de demande invalide');
        }

        $model->timbre_recupere = 1;
        $saved = $model->save();

        if ($saved) {
            // Pour les groupes : quantite_totale (accessor) ; pour les simples : quantite
            $totalQty = isset($model->quantite)
                ? (int) ($model->quantite ?? 1)
                : (int) ($model->qty_simple ?? 0) + (int) ($model->qty_integral ?? 0);
            $freeQty = (int) ($model->free_timbres_count ?? 0);

            // Décrémenter le stock physique par la quantité totale de la demande
            Timbre::create([
                'nombre_timbre' => -$totalQty,
                'comptable_id' => Auth::guard('comptable')->id(),
            ]);

            // Décrémenter le solde de la mairie uniquement pour les timbres qui étaient gratuits
            if ($freeQty > 0) {
                $comptable = Auth::guard('comptable')->user();
                if ($comptable && $comptable->finance && $comptable->finance->mairie) {
                    $mairie = $comptable->finance->mairie;
                    $mairie->solde = max(0, $mairie->solde - ($freeQty * 500));
                    $mairie->save();
                }
            }

            return redirect()->back()->with('success', 'Timbre validé et stock débité.');
        }

        return redirect()->back()->with('error', 'Demande introuvable.');
    }
}
