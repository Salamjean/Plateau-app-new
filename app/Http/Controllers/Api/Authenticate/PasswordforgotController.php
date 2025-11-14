<?php

namespace App\Http\Controllers\Api\Authenticate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\ApiPasswordResetNotification;
use Carbon\Carbon;
use Illuminate\Support\Str; // <-- Assurez-vous d'importer Str

class PasswordforgotController extends Controller
{
    /**
     * Étape 1 : Demande de réinitialisation
     * Génère un code à 6 chiffres et l'envoie par email.
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        $code = random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($code),
                'created_at' => Carbon::now()
            ]
        );

        $user = User::where('email', $request->email)->first();
        $user->notify(new ApiPasswordResetNotification($code));

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
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string|min:6|max:6', // Le code à 6 chiffres
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(config('auth.passwords.users.expire'))->isPast()) {
            return response()->json(['message' => 'Le code de réinitialisation est invalide ou a expiré.'], 400);
        }

        if (!Hash::check($request->token, $resetRecord->token)) {
            return response()->json(['message' => 'Le code de réinitialisation est invalide.'], 400);
        }

        // Si le code est bon, on génère un nouveau token sécurisé pour l'étape finale
        $secureToken = Str::random(60);

        // On met à jour l'enregistrement avec ce nouveau token
        DB::table('password_reset_tokens')->where('email', $request->email)->update([
            'token' => Hash::make($secureToken), // On le stocke haché pour la sécurité
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
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string', // Le token sécurisé de l'étape 2
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(config('auth.passwords.users.expire'))->isPast()) {
            return response()->json(['message' => 'La demande de réinitialisation est invalide ou a expiré.'], 400);
        }
        
        // On compare le token sécurisé fourni avec la version hachée en DB
        if (!Hash::check($request->token, $resetRecord->token)) {
            return response()->json(['message' => 'Le token de réinitialisation est invalide.'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Votre mot de passe a été réinitialisé avec succès.'], 200);
    }
}