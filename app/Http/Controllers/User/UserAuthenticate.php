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
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

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

    public function profil(){
        return view('user.auth.profil');
    }

    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mot de passe incorrect'
            ], 401);
        }

        // Stocker en session que le mot de passe est vérifié
        session(['password_verified' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe vérifié avec succès'
        ]);
    }

    // Méthode de mise à jour du profil
    public function updateProfile(Request $request): RedirectResponse
    {
        try {
            // Vérification de la session (au lieu de vérifier le mot de passe directement)
            if (!session('password_verified')) {
                return back()->withErrors(['error' => 'Vérification de sécurité requise. Veuillez confirmer votre mot de passe.']);
            }

            $user = Auth::user();
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'commune' => 'required|string|max:255',
                'indicatif' => 'required|string|max:10',
                'contact' => 'required|string|max:20',
                'CMU' => 'nullable|string|max:50',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'diaspora' => 'nullable|boolean',
                'pays_residence' => 'nullable|string|max:255',
                'ville_residence' => 'nullable|string|max:255',
                'adresse_etrangere' => 'nullable|string|max:500',
            ]);

            // Gestion de la photo de profil
            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
                $user->profile_picture = $profilePicturePath;
            }

            // Mise à jour des informations de base
            $user->name = $validated['name'];
            $user->prenom = $validated['prenom'];
            $user->email = $validated['email'];
            $user->commune = $validated['commune'];
            $user->indicatif = $validated['indicatif'];
            $user->contact = $validated['contact'];
            $user->CMU = $validated['CMU'];

            // Gestion de la diaspora
            $user->diaspora = $request->has('diaspora') ? true : false;
            
            if ($user->diaspora) {
                $user->pays_residence = $validated['pays_residence'];
                $user->ville_residence = $validated['ville_residence'];
                $user->adresse_etrangere = $validated['adresse_etrangere'];
            } else {
                // Nettoyer les champs diaspora si non cochée
                $user->pays_residence = null;
                $user->ville_residence = null;
                $user->adresse_etrangere = null;
            }

            $user->save();

            // Nettoyer la session après utilisation
            session()->forget('password_verified');

            return redirect()->route('user.profil')->with('success', 'Profil mis à jour avec succès!');

        } catch (\Exception $e) {
            Log::error('Error during profile update: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour. Veuillez réessayer.'])->withInput();
        }
    }
}
