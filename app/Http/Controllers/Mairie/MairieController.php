<?php

namespace App\Http\Controllers\Mairie;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mairie;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\ResetCodePasswordMairie;
use App\Notifications\SendEmailToMairieAfterRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class MairieController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer uniquement les mairies non archivées
        $mairies = Mairie::whereNull('archived_at')->get();

        // Compter les demandes par commune
        $naissanceCountByCommune = Naissance::select('commune', DB::raw('count(*) as total'))
            ->groupBy('commune')
            ->get();
        $naissanceCount = [];
        foreach ($naissanceCountByCommune as $item) {
            $naissanceCount[$item->commune] = $item->total;
        }

        $decesCountByCommune = Deces::select('commune', DB::raw('count(*) as total'))
            ->groupBy('commune')
            ->get();
        $decesCount = [];
        foreach ($decesCountByCommune as $item) {
            $decesCount[$item->commune] = $item->total;
        }

        $mariageCountByCommune = Mariage::select('commune', DB::raw('count(*) as total'))
            ->groupBy('commune')
            ->get();
        $mariageCount = [];
        foreach ($mariageCountByCommune as $item) {
            $mariageCount[$item->commune] = $item->total;
        }

        // Récupérer la mairie sélectionnée et le montant saisi
        $mairieSelectionnee = $request->input('mairie_selectionnee');
        $ajoutSolde = $request->input('ajout_solde', 0); // Par défaut, 0 si non renseigné

        // Mettre à jour le solde de la mairie sélectionnée
        if ($mairieSelectionnee && $ajoutSolde) {
            $vendor = Mairie::find($mairieSelectionnee);
            if ($vendor) {
                $vendor->solde += $ajoutSolde; // Ajouter le montant saisi au solde actuel
                $vendor->save(); // Sauvegarder les modifications
            }
        }

        // Récupérer les soldes mis à jour
        $soldeRestantParCommune = [];
        foreach ($mairies as $vendor) {
            $soldeRestantParCommune[$vendor->name] = $vendor->solde;
        }

        return view('admin.mairie.index', compact('mairies','soldeRestantParCommune', 'ajoutSolde', 'mairieSelectionnee'));
    }

    // Les routes de creations de la mairie
    public function create(){
        $admin = Auth::guard('admin')->user();
        $vendor = Mairie::all();
        return view('admin.mairie.create', compact('vendor'));
    }

    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'name' => 'required|string|unique:mairies,name',
            'email' => 'required|email|unique:mairies,email',
        ],[
            'name.required' => 'Le nom est obligatoire.',
            'name.unique' => 'Cette mairie est déjà inscrite.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'Veuillez fournir une adresse e-mail valide.',
            'email.unique' => 'Cette adresse e-mail existe déjà.',
        ]);

        try {
            // Récupérer le vendor connecté
            $vendor = Auth::guard('admin')->user();

            if (!$vendor) {
                return redirect()->back()->withErrors(['error' => 'Impossible de récupérer les informations du mairie.']);
            }

            // Création du vendor
            $vendor = new Mairie();
            $vendor->name = $request->name;
            $vendor->email = $request->email;
            $vendor->password = Hash::make('password');
            $vendor->save();

            // Envoi de l'e-mail de vérification
            ResetCodePasswordMairie::where('email', $vendor->email)->delete();
            $code = rand(1000, 4000);

            ResetCodePasswordMairie::create([
                'code' => $code,
                'email' => $vendor->email,
            ]);

            Notification::route('mail', $vendor->email)
                ->notify(new SendEmailToMairieAfterRegistrationNotification($code, $vendor->email));

            return redirect()->route('admin.index')
                ->with('success', 'Mairie enregistré avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'enregistrement du vendor: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e,
            ]);

            return redirect()->back()->withErrors(['error' => 'Une erreur est survenue : ' . $e->getMessage()]);
        }
    }

    public function addSolde(Request $request)
    {
        // Valider les entrées
        $request->validate([
            'mairie_selectionnee' => 'required|exists:mairies,id',
            'ajout_solde' => 'required|numeric|min:0',
            'action' => 'required|in:ajouter,retirer', // Assurez-vous que l'action est soit 'ajouter' soit 'retirer'
        ]);

        // Récupérer la mairie sélectionnée, le montant saisi et l'action
        $mairieSelectionnee = $request->input('mairie_selectionnee');
        $ajoutSolde = $request->input('ajout_solde');
        $action = $request->input('action');

        // Mettre à jour le solde de la mairie sélectionnée
        $vendor = Mairie::find($mairieSelectionnee);
        if ($vendor) {
            if ($action === 'ajouter') {
                $vendor->solde += $ajoutSolde; // Ajouter le montant saisi au solde actuel
            } elseif ($action === 'retirer') {
                $vendor->solde -= $ajoutSolde; // Retirer le montant saisi du solde actuel
            }
            $vendor->save(); // Sauvegarder les modifications
        }

        // Rediriger vers la page précédente avec un message de succès
        return redirect()->route('admin.index')->with('success', 'Le montant a été ' . ($action === 'ajouter' ? 'ajouté' : 'retiré') . ' avec succès.');
}
}
