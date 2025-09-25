<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserAuthenticate extends Controller
{
    public function login(){
         // Vérifier si l'utilisateur est déjà authentifié
        if (auth('web')->check()) {
            return redirect()->route('user.dashboard');
        }
        return view('user.auth.login');
    }

    public function handleLogin(UserLoginRequest $request): RedirectResponse
    {
        if (!Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            return redirect()->route('login')->withErrors([
                'password' => 'Le mot de passe incorrect.',
            ]);
        }

        // Si l'authentification réussit, régénérer la session
        $request->session()->regenerate();

        // Rediriger vers la page de tableau de bord avec un message de succès
        return redirect()->intended(route('user.dashboard', absolute: false))->with('success', 'Bienvenue sur votre page!');
    }

    
    public function register(){
        return view('user.auth.register');
    }

    public function handleRegister(UserRegisterRequest $request): RedirectResponse
    {
        // Création de l'utilisateur
        try {
            $profilePicturePath = null;
            if ($request->hasFile('profile_picture')) {
                $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
                Log::info('Profile picture stored at: ' . $profilePicturePath);
            }
            
            $users = new User();
            $users->name = $request->name;
            $users->prenom = $request->prenom;
            $users->email = $request->email;
            $users->commune = $request->commune;
            $users->indicatif = $request->indicatif;
            $users->contact = $request->contact;
            $users->CMU = $request->CMU;
            $users->password = Hash::make($request->password);
            $users->profile_picture = $profilePicturePath;
            
            // Gestion de la diaspora
            $users->diaspora = $request->has('diaspora') ? true : false;
            if ($users->diaspora) {
                $users->pays_residence = $request->pays_residence;
                $users->ville_residence = $request->ville_residence;
                $users->adresse_etrangere = $request->adresse_etrangere;
            }
            
            $users->save();

            return redirect()->route('login')->with('success', 'Votre compte a été créé avec succès. Vous pouvez vous connecter.');

        } catch (\Exception $e) {
            Log::error('Error during registration: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Une erreur est survenue. Veuillez réessayer.'])->withInput();
        }
    }


    public function history() {
        $userId = Auth::user()->id;
        
        $historique = DB::table('naissances')
            ->select(
                'id',
                'reference',
                'type as demande_type',
                'etat',
                'statut_livraison',
                'created_at',
                DB::raw("'naissance' as table_name")
            )
            ->where('user_id', $userId)
            ->where('etat','terminé')
            
            ->unionAll(
                DB::table('deces')
                    ->select(
                        'id',
                        'reference',
                        DB::raw("'Décès' as demande_type"),
                        'etat',
                        'statut_livraison',
                        'created_at',
                        DB::raw("'deces' as table_name")
                    )
                    ->where('user_id', $userId)
                    ->where('etat','terminé')
            )

            ->unionAll(
                DB::table('mariages')
                    ->select(
                        'id',
                        'reference',
                        DB::raw("'Mariage' as demande_type"),
                        'etat',
                        'statut_livraison',
                        'created_at',
                        DB::raw("'mariage' as table_name")
                    )
                    ->where('user_id', $userId)
                    ->where('etat','terminé')
            )
            
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('user.history', compact('historique'));
    }

    public function getDemandeDetails($type, $id)
    {
        $userId = Auth::user()->id;
        
        try {
            switch($type) {
                case 'naissance':
                    $demande = Naissance::where('user_id', $userId)->findOrFail($id);
                    break;
                case 'deces':
                    $demande = Deces::where('user_id', $userId)->findOrFail($id);
                    break;
                case 'mariage':
                    $demande = Mariage::where('user_id', $userId)->findOrFail($id);
                    break;
                default:
                    return response()->json(['error' => 'Type de demande non trouvé'], 404);
            }
            
            return response()->json([
                'success' => true,
                'demande' => $demande,
                'type' => $type
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Demande non trouvée'], 404);
        }
    }
}
