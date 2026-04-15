<?php

namespace App\Http\Controllers\Api\Authenticate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\YellikaSmsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    /**
     * Envoie un code OTP par SMS.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'indicatif' => 'required|string',
            'contact' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $indicatif = preg_replace('/[^0-9]/', '', $request->indicatif);
            $contact = preg_replace('/[^0-9]/', '', $request->contact);
            $phone = $indicatif . $contact;

            // Générer un code à 6 chiffres
            $otp = rand(100000, 999999);

            // Stocker en cache pour 10 minutes
            Cache::put('otp_' . $phone, $otp, now()->addMinutes(10));

            // Envoi du SMS
            $message = "Votre code de vérification est : " . $otp;
            $smsService = app(YellikaSmsService::class);
            $result = $smsService->sendSms($phone, $message);

            if ($result && $result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Code de vérification envoyé avec succès.',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de l\'envoi du SMS.',
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Erreur sendOtp : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Vérifie le code OTP envoyé.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'indicatif' => 'required|string',
            'contact' => 'required|string',
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $indicatif = preg_replace('/[^0-9]/', '', $request->indicatif);
        $contact = preg_replace('/[^0-9]/', '', $request->contact);
        $phone = $indicatif . $contact;

        $cachedOtp = Cache::get('otp_' . $phone);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Le code de vérification est invalide ou a expiré.',
            ], 400);
        }

        // Si valide, on le supprime et on marque le numéro comme vérifié temporellement
        Cache::forget('otp_' . $phone);
        Cache::put('otp_verified_' . $phone, true, now()->addMinutes(30));

        return response()->json([
            'success' => true,
            'message' => 'Numéro de téléphone vérifié avec succès.',
        ]);
    }
}
