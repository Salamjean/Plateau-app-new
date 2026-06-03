<?php

namespace App\Http\Controllers\Api\Authenticate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\ApiPasswordResetNotification;
use App\Services\YellikaSmsService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PasswordforgotController extends Controller
{
    /**
     * Étape 1 : Demande de réinitialisation
     * Génère un code à 6 chiffres et l'envoie par email.
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login_identifier' => 'required|string', // Peut être un email ou un téléphone
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        $identifier = $request->login_identifier;
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
        $user = null;

        if ($isEmail) {
            $user = User::where('email', $identifier)->first();
        } else {
            $user = $this->findUserByPhone($identifier);
        }

        if (!$user) {
            return response()->json(['message' => 'Aucun compte n\'est associé à cet identifiant.'], 404);
        }

        // Bloquer les comptes créés via Google (ils doivent passer par Google pour se connecter)
        if ($user->google_id && !$user->contact) {
            return response()->json([
                'message' => 'Ce compte a été créé via Google. Veuillez vous connecter avec Google.',
                'method' => 'google'
            ], 403);
        }

        $code = random_int(100000, 999999);

        // On utilise l'identifier fourni pour stocker le token
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $identifier],
            [
                'token' => Hash::make($code),
                'created_at' => Carbon::now()
            ]
        );

        if ($isEmail) {
            try {
                Log::info('Reset password: envoi email à ' . $user->email);
                $user->notify(new ApiPasswordResetNotification($code));
                Log::info('Reset password: email envoyé avec succès à ' . $user->email);
                $message = 'Un code de réinitialisation de mot de passe a été envoyé à votre adresse email.';
            } catch (\Exception $e) {
                Log::error('Reset password: erreur envoi email: ' . $e->getMessage());
                return response()->json(['message' => 'Erreur lors de l\'envoi de l\'email de réinitialisation.'], 500);
            }
        } else {
            // Envoi par SMS
            try {
                $smsService = app(YellikaSmsService::class);
                $smsMessage = "Votre code de réinitialisation de mot de passe est : " . $code;
                
                // On utilise le numéro de l'utilisateur ou l'identifier fourni
                $phoneToSend = $user->contact;
                if ($user->indicatif) {
                    $phoneToSend = preg_replace('/[^0-9]/', '', $user->indicatif) . preg_replace('/[^0-9]/', '', $user->contact);
                }

                $smsService->sendSms($phoneToSend, $smsMessage);
                $message = 'Un code de réinitialisation de mot de passe a été envoyé par SMS sur votre numéro.';
            } catch (\Exception $e) {
                Log::error('Erreur envoi SMS reset password: ' . $e->getMessage());
                return response()->json(['message' => 'Erreur lors de l\'envoi du SMS de réinitialisation.'], 500);
            }
        }

        return response()->json([
            'message' => $message
        ], 200);
    }

    /**
     * Étape 2 : Vérification du code
     * Vérifie le code à 6 chiffres et renvoie un token sécurisé si c'est correct.
     */
    public function verifyResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login_identifier' => 'required|string', // Identifiant (email ou téléphone)
            'token' => 'required|string|min:6|max:6', // Le code à 6 chiffres
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->login_identifier)->first();

        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(config('auth.passwords.users.expire', 60))->isPast()) {
            return response()->json(['message' => 'Le code de réinitialisation est invalide ou a expiré.'], 400);
        }

        if (!Hash::check($request->token, $resetRecord->token)) {
            return response()->json(['message' => 'Le code de réinitialisation est invalide.'], 400);
        }

        // On renvoie un succès à l'application mobile
        return response()->json([
            'message' => 'Code vérifié avec succès. Vous pouvez maintenant réinitialiser votre mot de passe.',
        ], 200);
    }


    /**
     * Étape 3 : Réinitialisation finale du mot de passe
     * Utilise le token sécurisé de l'étape 2.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login_identifier' => 'required|string',
            'token' => 'required|string', // Le token sécurisé de l'étape 2
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->login_identifier)->first();

        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(config('auth.passwords.users.expire', 60))->isPast()) {
            return response()->json(['message' => 'La demande de réinitialisation est invalide ou a expiré.'], 400);
        }
        
        // On compare le token sécurisé fourni avec la version hachée en DB
        if (!Hash::check($request->token, $resetRecord->token)) {
            return response()->json(['message' => 'Le token de réinitialisation est invalide.'], 400);
        }

        $identifier = $request->login_identifier;
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            $user = User::where('email', $identifier)->first();
        } else {
            $user = $this->findUserByPhone($identifier);
        }

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->login_identifier)->delete();

        return response()->json(['message' => 'Votre mot de passe a été réinitialisé avec succès.'], 200);
    }

    /**
     * Cherche un utilisateur par numéro de téléphone en testant toutes les variantes.
     * Gère : +2250554772283 / 2250554772283 / 0554772283 / 554772283
     */
    private function findUserByPhone(string $identifier): ?User
    {
        // Extraire uniquement les chiffres
        $digits = preg_replace('/[^0-9]/', '', $identifier);

        // Construire toutes les variantes possibles
        $variants = array_unique(array_filter([
            $identifier,            // Tel quel : +2250554772283
            $digits,               // Chiffres seuls : 2250554772283
            '+' . $digits,         // Avec + : +2250554772283
            '00' . $digits,        // Avec 00 : 002250554772283
            // Sans indicatif pays 225 (garde les 10 derniers chiffres)
            strlen($digits) > 10 ? substr($digits, -10) : null,
            // Sans indicatif 225 explicitement
            str_starts_with($digits, '225') ? substr($digits, 3) : null,
        ]));

        // Une seule requête qui teste toutes les variantes
        return User::whereIn('contact', $variants)->first();
    }
}