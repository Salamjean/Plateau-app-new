<?php

namespace App\Http\Controllers\Api\Authenticate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProfileCompletionController extends Controller
{
    /**
     * Finalise le profil d'un utilisateur (Google ou Téléphone).
     */
    public function finalizeProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'NNI' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
            'indicatif' => 'nullable|string|max:10',
            'contact' => 'nullable|string|max:20',
            'diaspora' => 'nullable|boolean',
            'pays_residence' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->only([
                'name', 'prenom', 'NNI', 'indicatif', 'contact', 'diaspora', 'pays_residence'
            ]);

            // Mise à jour du mot de passe si fourni
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Gestion particulière pour diaspora/pays_residence
            if ($request->has('diaspora')) {
                $data['diaspora'] = $request->boolean('diaspora');
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Profil finalisé avec succès.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'prenom' => $user->prenom,
                        'email' => $user->email,
                        'contact' => $user->contact,
                        'indicatif' => $user->indicatif,
                        'NNI' => $user->NNI,
                        'diaspora' => $user->diaspora,
                        'pays_residence' => $user->pays_residence,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur finalizeProfile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la finalisation du profil.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
