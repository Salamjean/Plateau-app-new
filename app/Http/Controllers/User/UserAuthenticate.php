<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\User;
use App\Notifications\SendUserConfirmationNotification;
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
        $credentials = $request->only('password');
        
        if ($request->filled('email')) {
            $credentials['email'] = $request->email;
        } else {
            $credentials['contact'] = $request->contact;
            $credentials['indicatif'] = $request->indicatif;
        }

        if (!Auth::attempt($credentials, $request->filled('remember'))) {
            return redirect()->route('login')->withErrors([
                'password' => 'Identifiants ou mot de passe incorrects.',
            ])->withInput($request->except('password'));
        }

        // Si l'authentification réussit, régénérer la session
        $request->session()->regenerate();

        // Rediriger vers la page de tableau de bord avec un message de succès
        return redirect()->intended(route('user.dashboard', false))->with('success', 'Bienvenue sur votre page!');
    }

    
    public function register(){
        return view('user.auth.register');
    }

    public function handleRegister(UserRegisterRequest $request): RedirectResponse
    {
        // Création de l'utilisateur
        $profilePicturePath = null;
        try {
            if ($request->hasFile('profile_picture')) {
                $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
                Log::info('Profile picture stored at: ' . $profilePicturePath);
            }
            
            $user = new User();
            $user->name = $request->name;
            $user->prenom = $request->prenom;
            $user->email = $request->email;
            $user->commune = 'plateau';
            $user->indicatif = $request->indicatif;
            $user->contact = $request->contact;
            $user->CMU = $request->CMU;
            $user->password = Hash::make($request->password);
            $user->profile_picture = $profilePicturePath;
            
            // Gestion de la diaspora
            $user->diaspora = $request->has('diaspora') ? true : false;
            if ($user->diaspora) {
                $user->pays_residence = $request->pays_residence;
                $user->ville_residence = $request->ville_residence;
                $user->adresse_etrangere = $request->adresse_etrangere;
            }
            
            $user->save();

            // Envoi de l'email de confirmation
            try {
                $user->notify(new SendUserConfirmationNotification($user));
                Log::info('Confirmation email sent to: ' . $user->email);
            } catch (\Exception $emailException) {
                Log::error('Failed to send confirmation email: ' . $emailException->getMessage());
                // On continue même si l'email échoue
            }

            return redirect()->route('login')->with('success', 'Votre compte a été créé avec succès. Un email de confirmation vous a été envoyé.');

        } catch (\Exception $e) {
            Log::error('Error during registration: ' . $e->getMessage());
            
            // Ajout (inspiré de votre API) : Supprimer l'image si la création échoue
            if (isset($profilePicturePath) && Storage::disk('public')->exists($profilePicturePath)) {
                Storage::disk('public')->delete($profilePicturePath);
                Log::info('Rolled back profile picture upload: ' . $profilePicturePath);
            }

            return back()->withErrors(['error' => 'Une erreur est survenue. Veuillez réessayer.'])->withInput();
        }
    }

    public function history() {
        $userId = Auth::user()->id;
        
        $historique = DB::table('naissances')
            ->leftJoin('agents', 'naissances.agent_id', '=', 'agents.id')
            ->select(
                'naissances.id',
                'naissances.reference',
                'naissances.type as demande_type',
                'naissances.etat',
                'naissances.statut_livraison',
                'naissances.created_at',
                DB::raw("'naissance' as table_name"),
                'agents.name as agent_nom',
                'agents.prenom as agent_prenom'
            )
            ->where('naissances.user_id', $userId)
            
            ->unionAll(
                DB::table('deces')
                    ->leftJoin('agents', 'deces.agent_id', '=', 'agents.id')
                    ->select(
                        'deces.id',
                        'deces.reference',
                        DB::raw("'Décès' as demande_type"),
                        'deces.etat',
                        'deces.statut_livraison',
                        'deces.created_at',
                        DB::raw("'deces' as table_name"),
                        'agents.name as agent_nom',
                        'agents.prenom as agent_prenom'
                    )
                    ->where('deces.user_id', $userId)
            )

            ->unionAll(
                DB::table('mariages')
                    ->leftJoin('agents', 'mariages.agent_id', '=', 'agents.id')
                    ->select(
                        'mariages.id',
                        'mariages.reference',
                        DB::raw("'Mariage' as demande_type"),
                        'mariages.etat',
                        'mariages.statut_livraison',
                        'mariages.created_at',
                        DB::raw("'mariage' as table_name"),
                        'agents.name as agent_nom',
                        'agents.prenom as agent_prenom'
                    )
                    ->where('mariages.user_id', $userId)
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
                    $demande = Naissance::with('agent')->where('user_id', $userId)->findOrFail($id);
                    break;
                case 'deces':
                    $demande = Deces::with('agent')->where('user_id', $userId)->findOrFail($id);
                    break;
                case 'mariage':
                    $demande = Mariage::with('agent')->where('user_id', $userId)->findOrFail($id);
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
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:25600',
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
