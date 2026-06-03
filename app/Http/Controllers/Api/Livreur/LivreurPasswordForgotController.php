<?php

namespace App\Http\Controllers\Api\Livreur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Livreur;
use App\Models\ResetCodePasswordLivreur;
use App\Notifications\ApiPasswordResetNotification;
use Carbon\Carbon;
use Illuminate\Support\Str;

class LivreurPasswordForgotController extends Controller
{
    /**
     * Étape 1 : Demande de réinitialisation
     * Génère un code à 6 chiffres et l'envoie par email.
     */
    public function forgotPassword(Request $request)
    {
        Log::info('LivreurPasswordForgotController@forgotPassword: Start', ['email' => $request->email]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:livreurs,email',
        ]);

        if ($validator->fails()) {
            Log::error('LivreurPasswordForgotController@forgotPassword: Validation failed', ['errors' => $validator->errors()]);
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        try {
            $code = random_int(100000, 999999);
            Log::info('LivreurPasswordForgotController@forgotPassword: Code generated', ['email' => $request->email]);

            $existing = ResetCodePasswordLivreur::where('email', $request->email)->first();

            if ($existing) {
                Log::info('LivreurPasswordForgotController@forgotPassword: Updating existing reset record', ['email' => $request->email]);
                $existing->update([
                    'code' => Hash::make($code),
                    'created_at' => Carbon::now()
                ]);
            } else {
                Log::info('LivreurPasswordForgotController@forgotPassword: Creating new reset record', ['email' => $request->email]);
                ResetCodePasswordLivreur::create([
                    'email' => $request->email,
                    'code' => Hash::make($code)
                ]);
            }

            $livreur = Livreur::where('email', $request->email)->first();
            Log::info('LivreurPasswordForgotController@forgotPassword: Sending notification to livreur', ['email' => $request->email]);
            $livreur->notify(new ApiPasswordResetNotification($code));

            Log::info('LivreurPasswordForgotController@forgotPassword: Process successful', ['email' => $request->email]);
            return response()->json([
                'message' => 'Un code de réinitialisation de mot de passe a été envoyé à votre adresse email.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('LivreurPasswordForgotController@forgotPassword: Exception occurred', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Une erreur est survenue lors de la demande.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Étape 2 : Vérification du code
     * Vérifie le code à 6 chiffres et renvoie un token sécurisé si c'est correct.
     */
    public function verifyResetCode(Request $request)
    {
        Log::info('LivreurPasswordForgotController@verifyResetCode: Start', ['email' => $request->email, 'token' => $request->token]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:livreurs,email',
            'token' => 'required', // Removed strict string validation to avoid issues with numeric JSON values
        ]);

        if ($validator->fails()) {
            Log::error('LivreurPasswordForgotController@verifyResetCode: Validation failed', ['errors' => $validator->errors()]);
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        try {
            $resetRecord = ResetCodePasswordLivreur::where('email', $request->email)->first();
            $expiration = config('auth.passwords.users.expire', 60);

            if (!$resetRecord) {
                Log::warning('LivreurPasswordForgotController@verifyResetCode: No reset record found', ['email' => $request->email]);
                return response()->json(['message' => 'Le code de réinitialisation est invalide ou a expiré.'], 400);
            }

            if (Carbon::parse($resetRecord->updated_at)->addMinutes($expiration)->isPast()) {
                Log::warning('LivreurPasswordForgotController@verifyResetCode: Code expired', ['email' => $request->email, 'updated_at' => $resetRecord->updated_at]);
                return response()->json(['message' => 'Le code de réinitialisation est invalide ou a expiré.'], 400);
            }

            // Ensure token is cast to string for hash checking
            $providedToken = (string) $request->token;

            if (!Hash::check($providedToken, $resetRecord->code)) {
                Log::warning('LivreurPasswordForgotController@verifyResetCode: Invalid code provided', ['email' => $request->email]);
                return response()->json(['message' => 'Le code de réinitialisation est invalide.'], 400);
            }

            Log::info('LivreurPasswordForgotController@verifyResetCode: Code verified successfully', ['email' => $request->email]);

            return response()->json([
                'message' => 'Code vérifié avec succès. Vous pouvez maintenant réinitialiser votre mot de passe.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('LivreurPasswordForgotController@verifyResetCode: Exception occurred', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Une erreur est survenue lors de la vérification.', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Étape 3 : Réinitialisation finale du mot de passe
     * Utilise le token sécurisé de l'étape 2.
     */
    public function resetPassword(Request $request)
    {
        Log::info('LivreurPasswordForgotController@resetPassword: Start', ['email' => $request->email]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:livreurs,email',
            'token' => 'required|string', 
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            Log::error('LivreurPasswordForgotController@resetPassword: Validation failed', ['errors' => $validator->errors()]);
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        try {
            $resetRecord = ResetCodePasswordLivreur::where('email', $request->email)->first();
            $expiration = config('auth.passwords.users.expire', 60);

            if (!$resetRecord) {
                Log::warning('LivreurPasswordForgotController@resetPassword: No reset record found', ['email' => $request->email]);
                return response()->json(['message' => 'La demande de réinitialisation est invalide ou a expiré.'], 400);
            }
            
            if (Carbon::parse($resetRecord->updated_at)->addMinutes($expiration)->isPast()) {
                Log::warning('LivreurPasswordForgotController@resetPassword: Token expired', ['email' => $request->email]);
                return response()->json(['message' => 'La demande de réinitialisation est invalide ou a expiré.'], 400);
            }
            
            if (!Hash::check($request->token, $resetRecord->code)) {
                Log::warning('LivreurPasswordForgotController@resetPassword: Invalid secure token', ['email' => $request->email]);
                return response()->json(['message' => 'Le token de réinitialisation est invalide.'], 400);
            }

            $livreur = Livreur::where('email', $request->email)->first();
            
            if (!$livreur) {
                Log::error('LivreurPasswordForgotController@resetPassword: Livreur not found (should not happen)', ['email' => $request->email]);
                return response()->json(['message' => 'Livreur introuvable.'], 404);
            }

            $livreur->password = Hash::make($request->password);
            $livreur->save();
            Log::info('LivreurPasswordForgotController@resetPassword: Password successfully updated', ['email' => $request->email]);

            $resetRecord->delete();
            Log::info('LivreurPasswordForgotController@resetPassword: Reset record deleted', ['email' => $request->email]);

            return response()->json(['message' => 'Votre mot de passe a été réinitialisé avec succès.'], 200);

        } catch (\Exception $e) {
            Log::error('LivreurPasswordForgotController@resetPassword: Exception occurred', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Une erreur est survenue lors de la réinitialisation.', 'error' => $e->getMessage()], 500);
        }
    }
}
