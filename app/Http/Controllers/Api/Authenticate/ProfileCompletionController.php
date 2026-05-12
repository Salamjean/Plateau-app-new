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
     * Finalise le profil d'un utilisateur inscrit via Google.
     */
    public function finalizeProfileGoogle(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'NNI'              => 'nullable|string|max:50',
            'password'         => 'required|string|min:8|confirmed',
            'indicatif'        => 'required|string|max:10',
            'contact'          => 'required|string|max:20|unique:users,contact,' . $user->id,
            'diaspora'         => 'nullable|boolean',
            'pays_residence'   => 'nullable|string|max:255',
            'push_notification' => 'nullable|string|max:255',
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
                'NNI', 'indicatif', 'contact', 'diaspora', 'pays_residence'
            ]);

            $data['password'] = Hash::make($request->password);

            if ($request->has('diaspora')) {
                $data['diaspora'] = $request->boolean('diaspora');
            }

            // Sauvegarder le push token s'il est fourni
            if ($request->filled('push_notification')) {
                $data['push_notification'] = $request->push_notification;
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Profil finalisé avec succès (Google).',
                'data' => [
                    'user' => [
                        'id'               => $user->id,
                        'name'             => $user->name,
                        'prenom'           => $user->prenom,
                        'email'            => $user->email,
                        'contact'          => $user->contact,
                        'indicatif'        => $user->indicatif,
                        'NNI'              => $user->NNI,
                        'diaspora'         => $user->diaspora,
                        'pays_residence'   => $user->pays_residence,
                        'push_notification' => $user->push_notification,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur finalizeProfileGoogle: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la finalisation du profil par Google.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Finalise le profil d'un utilisateur inscrit via Apple.
     */
    public function finalizeProfileApple(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'              => 'required|string|max:255',
            'prenom'            => 'required|string|max:255',
            'NNI'               => 'nullable|string|max:50',
            'indicatif'         => 'nullable|string|max:10',
            'contact'           => 'nullable|string|max:20|unique:users,contact,' . $user->id,
            'diaspora'          => 'nullable|boolean',
            'pays_residence'    => 'nullable|string|max:255',
            'push_notification' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->only(['name', 'prenom', 'NNI', 'indicatif', 'contact', 'pays_residence']);

            if ($request->has('diaspora')) {
                $data['diaspora'] = $request->boolean('diaspora');
            }
            if ($request->filled('push_notification')) {
                $data['push_notification'] = $request->push_notification;
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Profil finalisé avec succès (Apple).',
                'data'    => [
                    'user' => [
                        'id'                => $user->id,
                        'name'              => $user->name,
                        'prenom'            => $user->prenom,
                        'email'             => $user->email,
                        'contact'           => $user->contact,
                        'indicatif'         => $user->indicatif,
                        'NNI'               => $user->NNI,
                        'diaspora'          => $user->diaspora,
                        'pays_residence'    => $user->pays_residence,
                        'push_notification' => $user->push_notification,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur finalizeProfileApple: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la finalisation du profil Apple.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Finalise le profil d'un utilisateur inscrit via Téléphone (OTP).
     */
    public function finalizeProfilePhone(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:255',
            'prenom'           => 'required|string|max:255',
            'NNI'              => 'nullable|string|max:50',
            'email'            => 'nullable|email|unique:users,email,' . $user->id,
            'password'         => 'required|string|min:8|confirmed',
            'indicatif'        => 'nullable|string|max:10',
            'diaspora'         => 'nullable|boolean',
            'pays_residence'   => 'nullable|string|max:255',
            'push_notification' => 'nullable|string|max:255',
        ], [
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre compte.',
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
                'name', 'prenom', 'email', 'NNI', 'indicatif', 'diaspora', 'pays_residence'
            ]);

            $data['password'] = Hash::make($request->password);

            if ($request->has('diaspora')) {
                $data['diaspora'] = $request->boolean('diaspora');
            }

            // Sauvegarder le push token s'il est fourni
            if ($request->filled('push_notification')) {
                $data['push_notification'] = $request->push_notification;
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Profil finalisé avec succès (Téléphone).',
                'data' => [
                    'user' => [
                        'id'               => $user->id,
                        'name'             => $user->name,
                        'prenom'           => $user->prenom,
                        'email'            => $user->email,
                        'contact'          => $user->contact,
                        'indicatif'        => $user->indicatif,
                        'NNI'              => $user->NNI,
                        'diaspora'         => $user->diaspora,
                        'pays_residence'   => $user->pays_residence,
                        'push_notification' => $user->push_notification,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur finalizeProfilePhone: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la finalisation du profil par téléphone.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
