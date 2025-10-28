<?php

namespace App\Http\Controllers\Api\Authenticate;

use App\Http\Controllers\Controller;
use App\Models\Admin; // ← AJOUT IMPORT MANQUANT
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // ← AJOUT IMPORT MANQUANT
use Illuminate\Support\Facades\Log; // ← AJOUT IMPORT MANQUANT

class ConnexionController extends Controller
{
    public function handleLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
            'password' => 'required|min:8',
        ], [
            'email.required' => 'Le mail est obligatoire.',
            'email.email' => 'Le format de l\'email est invalide.',
            'email.exists' => 'Cette adresse mail n\'existe pas.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
        ]);

        try {
            $admin = Admin::where('email', $request->email)->first();

            if ($admin && Hash::check($request->password, $admin->password)) {
                // Révoquer les anciens tokens si nécessaire
                // $admin->tokens()->delete();
                
                // Créer un nouveau token
                $token = $admin->createToken('admin-api-token', ['admin'])->plainTextToken;
                
                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie',
                    'data' => [
                        'token' => $token,
                        'token_type' => 'Bearer',
                        'admin' => [
                            'id' => $admin->id,
                            'email' => $admin->email,
                            'name' => $admin->name,
                            'created_at' => $admin->created_at,
                        ]
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Identifiants incorrects.'
                ], 401);
            }
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage()); // ← CORRECTION ICI
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la connexion',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }
}