<?php

namespace App\Http\Controllers\Api\Livreur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:livreurs,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        $code = random_int(100000, 999999);

        // Utilisation du modèle ResetCodePasswordLivreur ou DB directe
        // Le modèle a 'code' et 'email'. On va stocker le hash du code dans 'code'.
        
        // On vérifie s'il existe déjà une entrée pour cet email
        $existing = ResetCodePasswordLivreur::where('email', $request->email)->first();

        if ($existing) {
            $existing->update([
                'code' => Hash::make($code),
                'created_at' => Carbon::now()
            ]);
        } else {
            ResetCodePasswordLivreur::create([
                'email' => $request->email,
                'code' => Hash::make($code)
            ]);
        }

        $livreur = Livreur::where('email', $request->email)->first();
        // On utilise la même notification que pour les users, car elle est générique
        $livreur->notify(new ApiPasswordResetNotification($code));

        return response()->json([
            'message' => 'Un code de réinitialisation de mot de passe a été envoyé à votre adresse email.'
        ], 200);
    }

    /**
     * Étape 2 : Vérification du code
     * Vérifie le code à 6 chiffres et renvoie un token sécurisé si c'est correct.
     */
    public function verifyResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:livreurs,email',
            'token' => 'required|string|min:6|max:6', // Le code à 6 chiffres
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        $resetRecord = ResetCodePasswordLivreur::where('email', $request->email)->first();

        // On assume une expiration similaire à celle des users (config('auth.passwords.users.expire'))
        // ou une valeur par défaut de 60 minutes si non configuré.
        $expiration = config('auth.passwords.users.expire', 60);

        if (!$resetRecord || Carbon::parse($resetRecord->updated_at)->addMinutes($expiration)->isPast()) {
            return response()->json(['message' => 'Le code de réinitialisation est invalide ou a expiré.'], 400);
        }

        if (!Hash::check($request->token, $resetRecord->code)) {
            return response()->json(['message' => 'Le code de réinitialisation est invalide.'], 400);
        }

        // Si le code est bon, on génère un nouveau token sécurisé pour l'étape finale
        $secureToken = Str::random(60);

        // On met à jour l'enregistrement avec ce nouveau token
        $resetRecord->update([
            'code' => Hash::make($secureToken), // On le stocke haché pour la sécurité
        ]);

        // On renvoie le token non-haché à l'application mobile
        return response()->json([
            'message' => 'Code vérifié avec succès. Vous pouvez maintenant réinitialiser votre mot de passe.',
            'reset_token' => $secureToken // L'app mobile doit utiliser ce token pour la prochaine étape
        ], 200);
    }


    /**
     * Étape 3 : Réinitialisation finale du mot de passe
     * Utilise le token sécurisé de l'étape 2.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:livreurs,email',
            'token' => 'required|string', // Le token sécurisé de l'étape 2
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        $resetRecord = ResetCodePasswordLivreur::where('email', $request->email)->first();
        $expiration = config('auth.passwords.users.expire', 60);

        if (!$resetRecord || Carbon::parse($resetRecord->updated_at)->addMinutes($expiration)->isPast()) {
            return response()->json(['message' => 'La demande de réinitialisation est invalide ou a expiré.'], 400);
        }
        
        // On compare le token sécurisé fourni avec la version hachée en DB
        if (!Hash::check($request->token, $resetRecord->code)) {
            return response()->json(['message' => 'Le token de réinitialisation est invalide.'], 400);
        }

        $livreur = Livreur::where('email', $request->email)->first();
        $livreur->password = Hash::make($request->password);
        $livreur->save();

        // On supprime le code de reset après utilisation
        $resetRecord->delete();

        return response()->json(['message' => 'Votre mot de passe a été réinitialisé avec succès.'], 200);
    }
}
