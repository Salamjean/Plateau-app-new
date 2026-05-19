<?php

namespace App\Http\Controllers\Api\Authenticate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Services\YellikaSmsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    /**
     * Envoie un code OTP par SMS.
     * POST /api/utilisateurs/send-otp
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'indicatif' => 'required|string',
            'contact'   => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $indicatif = $request->indicatif;
            $contact   = $request->contact;
            $phone     = preg_replace('/[^0-9]/', '', $indicatif . $contact);

            // Bloquer si le numéro est déjà attaché à un compte complet (finalisé)
            $existingUser = User::where('contact', $contact)->first();
            if ($existingUser && $existingUser->password !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce numéro de téléphone est déjà associé à un compte existant.',
                ], 403);
            }

            $otp = rand(100000, 999999);
            Cache::put('otp_' . $phone, $otp, now()->addMinutes(10));

            $smsService = app(YellikaSmsService::class);
            $result     = $smsService->sendSms($phone, "Votre code de vérification Plateau App est : " . $otp);

            if ($result && isset($result['success']) && $result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Code de vérification envoyé avec succès.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du SMS.',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Erreur sendOtp : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Vérifie le code OTP.
     * Si valide → stocke les données en Cache (pending_token) SANS créer de compte en base.
     * Le compte sera créé uniquement lors de /finalize-profile/phone.
     *
     * POST /api/utilisateurs/verify-otp
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'indicatif' => 'required|string',
            'contact'   => 'required|string',
            'otp'       => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        $indicatif = $request->indicatif;
        $contact   = $request->contact;
        $phone     = preg_replace('/[^0-9]/', '', $indicatif . $contact);

        $cachedOtp = Cache::get('otp_' . $phone);

        if (!$cachedOtp || (string) $cachedOtp !== (string) $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Le code de vérification est invalide ou a expiré.',
            ], 400);
        }

        Cache::forget('otp_' . $phone);

        try {
            // Vérifier si le numéro appartient déjà à un compte existant et finalisé
            $existingUser = User::where('contact', $contact)
                               ->where('indicatif', $indicatif)
                               ->whereNotNull('name')
                               ->whereNotNull('password')
                               ->first();

            if ($existingUser) {
                // Connexion directe (compte déjà finalisé)
                $token = $existingUser->createToken('user-api-token')->plainTextToken;
                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie.',
                    'data'    => [
                        'is_new_user' => false,
                        'user'        => [
                            'id'        => $existingUser->id,
                            'name'      => $existingUser->name,
                            'prenom'    => $existingUser->prenom,
                            'indicatif' => $existingUser->indicatif,
                            'contact'   => $existingUser->contact,
                        ],
                        'token'      => $token,
                        'token_type' => 'Bearer',
                    ]
                ]);
            }

            // Nouveau numéro → marquer le numéro comme OTP-vérifié (60 min)
            // ⚠ AUCUNE création de User en base à ce stade.
            //   Le User ne sera créé qu'à l'étape finalize-profile/phone.
            //   Plus de pending_token : on identifie le numéro par indicatif+contact.
            Cache::put('otp_verified_' . $phone, [
                'indicatif'         => $indicatif,
                'contact'           => $contact,
                'phone_verified_at' => now()->toDateTimeString(),
            ], now()->addHours(1));

            Log::info('OTP vérifié - Numéro autorisé pour finalisation', [
                'indicatif' => $indicatif,
                'contact'   => $contact,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Numéro vérifié. Veuillez finaliser votre inscription.',
                'data'    => [
                    'is_new_user' => true,
                    'user'        => [
                        'indicatif' => $indicatif,
                        'contact'   => $contact,
                    ],
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur verifyOtp : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
