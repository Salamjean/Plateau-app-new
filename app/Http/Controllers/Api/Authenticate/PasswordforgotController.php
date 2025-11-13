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

class PasswordforgotController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        // 1. Générer un code court et lisible par un humain
        $code = random_int(100000, 999999);

        // 2. Stocker une version sécurisée (hachée) de ce code dans la base de données
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($code),
                'created_at' => Carbon::now()
            ]
        );

        // 3. Envoyer le code lisible (non haché) par email
        $user = User::where('email', $request->email)->first();
        $user->notify(new ApiPasswordResetNotification($code)); // On envoie le code court !

        return response()->json([
            'message' => 'Un code de réinitialisation de mot de passe a été envoyé à votre adresse email.'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|min:6|max:6', // On attend maintenant un code à 6 chiffres
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        // 1. Récupérer le token haché stocké pour cet email
        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        // 2. Vérifier si le token existe et n'a pas expiré
        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(config('auth.passwords.users.expire'))->isPast()) {
            return response()->json(['message' => 'Le code de réinitialisation est invalide ou a expiré.'], 400);
        }
        
        // 3. Comparer le code fourni par l'utilisateur avec le code haché en base de données
        if (!Hash::check($request->token, $resetRecord->token)) {
            return response()->json(['message' => 'Le code de réinitialisation est invalide.'], 400);
        }

        // 4. Si tout est bon, mettre à jour le mot de passe
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // 5. Supprimer le token pour qu'il ne puisse pas être réutilisé
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Votre mot de passe a été réinitialisé avec succès.'], 200);
    }
}