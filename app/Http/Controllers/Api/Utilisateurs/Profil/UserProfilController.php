<?php

namespace App\Http\Controllers\Api\Utilisateurs\Profil;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSetting;
use App\Models\User;
use App\Traits\HandlesFreeRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\YellikaSmsService;

class UserProfilController extends Controller
{
    use HandlesFreeRequests;
    /**
     * Récupérer les informations du profil utilisateur
     * GET /api/utilisateurs/profil
     */
    public function getProfil(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $freeRequestsModeActive = MaintenanceSetting::isFreeRequestsModeActive();
            $freeRequestsRemaining = $this->getRemainingFreeRequests($user);

            return response()->json([
                'success' => true,
                'message' => 'Profil récupéré avec succès',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'prenom' => $user->prenom,
                        'email' => $user->email,
                        'indicatif' => $user->indicatif,
                        'contact' => $user->contact,
                        'commune' => $user->commune,
                        'CMU' => $user->CMU,
                        'profile_picture' => $user->profile_picture ? (Str::startsWith($user->profile_picture, ['http://', 'https://']) ? $user->profile_picture : '/storage/' . $user->profile_picture) : null,
                        'diaspora' => (bool) $user->diaspora,
                        'pays_residence' => $user->pays_residence,
                        'ville_residence' => $user->ville_residence,
                        'adresse_etrangere' => $user->adresse_etrangere,
                        'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
                        'free_requests_mode_active' => $freeRequestsModeActive,
                        'free_requests_used' => (int) $user->free_requests_used,
                        'free_requests_remaining' => $freeRequestsRemaining,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur getProfil: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du profil'
            ], 500);
        }
    }

    /**
     * Mise à jour de la photo de profil
     * POST /api/utilisateurs/profil/photo
     */
    public function updateProfilePicture(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $profilePicture = $request->input('profile_picture');
            $file = $request->file('profile_picture');

            // Supprimer l'ancienne photo
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = null;

            // CAS 1: Base64 (mobile, caméra)
            if (is_string($profilePicture)) {
                // Nettoyer et décoder
                $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $profilePicture);
                $imageData = base64_decode($base64Data);

                if ($imageData !== false) {
                    $fileName = 'profile_' . time() . '_' . Str::random(10) . '.jpg';
                    $path = 'profile_pictures/' . $fileName;
                    Storage::disk('public')->put($path, $imageData);
                }
            }
            // CAS 2: Fichier uploadé (tous formats acceptés)
            elseif ($file && $file->isValid()) {
                $extension = $file->getClientOriginalExtension();
                $fileName = 'profile_' . time() . '_' . Str::random(10) . '.' . $extension;
                $path = $file->storeAs('profile_pictures', $fileName, 'public');
            }

            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de traiter l\'image'
                ], 400);
            }

            $user->update(['profile_picture' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Photo mise à jour avec succès',
                'data' => [
                    'profile_picture' => '/storage/' . $path,
                    'profile_picture_path' => $path
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur updateProfilePicture: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer la photo de profil
     * DELETE /api/utilisateurs/profil/photo
     */
    public function deleteProfilePicture(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $user->update(['profile_picture' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur deleteProfilePicture: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la photo de profil'
            ], 500);
        }
    }
    /**
     * Mise à jour des informations personnelles
     * PUT /api/utilisateurs/profil/informations
     */
    public function updateInformations(Request $request): JsonResponse
    {
        $user = $request->user();

        $isSocial = !empty($user->google_id) || !empty($user->apple_id);

        $rules = [
            'name'              => 'sometimes|required|string|max:255',
            'prenom'            => 'sometimes|required|string|max:255',
            'indicatif'         => 'sometimes|required|string|max:10',
            'contact'           => 'sometimes|required|string|max:20',
            'commune'           => 'sometimes|required|string|max:255',
            'CMU'               => 'sometimes|nullable|string|max:255',
            'diaspora'          => 'sometimes|boolean',
            'pays_residence'    => 'nullable|required_if:diaspora,true|string|max:255',
            'ville_residence'   => 'nullable|required_if:diaspora,true|string|max:255',
            'adresse_etrangere' => 'nullable|required_if:diaspora,true|string|max:500',
        ];

        if (!$isSocial) {
            $rules['current_password'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules, [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // Vérifier le mot de passe pour les utilisateurs normaux uniquement
            if (!$isSocial && !Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe incorrect. Veuillez vérifier votre mot de passe actuel.'
                ], 422);
            }

            // Préparer les données de mise à jour
            $updateData = [];
            $champs = [
                'name',
                'prenom',
                'email',
                'indicatif',
                'contact',
                'commune',
                'CMU',
                'diaspora',
                'pays_residence',
                'ville_residence',
                'adresse_etrangere'
            ];

            foreach ($champs as $champ) {
                if ($request->has($champ)) {
                    $updateData[$champ] = $request->$champ;
                }
            }

            // Gestion spéciale pour la diaspora
            if (isset($updateData['diaspora']) && !$updateData['diaspora']) {
                $updateData['pays_residence'] = null;
                $updateData['ville_residence'] = null;
                $updateData['adresse_etrangere'] = null;
            }

            // Mise à jour de l'utilisateur
            $user->update($updateData);
            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Informations mises à jour avec succès',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'prenom' => $user->prenom,
                        'email' => $user->email,
                        'indicatif' => $user->indicatif,
                        'contact' => $user->contact,
                        'commune' => $user->commune,
                        'CMU' => $user->CMU,
                        'diaspora' => (bool) $user->diaspora,
                        'pays_residence' => $user->pays_residence,
                        'ville_residence' => $user->ville_residence,
                        'adresse_etrangere' => $user->adresse_etrangere,
                        'profile_picture' => $user->profile_picture ? (Str::startsWith($user->profile_picture, ['http://', 'https://']) ? $user->profile_picture : '/storage/' . $user->profile_picture) : null,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur updateInformations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour des informations'
            ], 500);
        }
    }

    /**
     * Envoyer un OTP pour confirmer le changement d'email
     * POST /api/utilisateurs/profil/request-email-otp
     */
    public function requestEmailOtp(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $user->id,
        ], [
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre compte.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $otp = rand(100000, 999999);

            Cache::put('email_change_otp_' . $user->id, [
                'otp'       => $otp,
                'new_email' => $request->email,
            ], now()->addMinutes(10));

            Log::info('EmailOTP Tentative envoi', [
                'user_id'   => $user->id,
                'new_email' => $request->email,
            ]);

            Mail::raw(
                "Bonjour " . ($user->prenom ?? $user->name) . ",\n\n"
                . "Votre code de confirmation pour changer votre adresse email sur Plateau App est :\n\n"
                . "    " . $otp . "\n\n"
                . "Ce code est valable 10 minutes.\n"
                . "Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.",
                function ($message) use ($request) {
                    $message->to($request->email)
                            ->subject('Code de confirmation - Changement d\'email Plateau App');
                }
            );

            Log::info('EmailOTP Envoyé avec succès', [
                'user_id'   => $user->id,
                'new_email' => $request->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Un code de confirmation a été envoyé à ' . $request->email . '. Valable 10 minutes.'
            ]);

        } catch (\Exception $e) {
            Log::error('EmailOTP Échec envoi', [
                'user_id'   => $user->id,
                'new_email' => $request->email,
                'error'     => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'envoi du code.'], 500);
        }
    }

    /**
     * Vérifier l'OTP et mettre à jour l'email
     * PUT /api/utilisateurs/profil/update-email
     */
    public function updateEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        $cached = Cache::get('email_change_otp_' . $user->id);

        if (!$cached) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun code en attente. Veuillez d\'abord faire une demande.'
            ], 400);
        }

        if ((string) $cached['otp'] !== (string) $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Code incorrect ou expiré.'
            ], 422);
        }

        try {
            Cache::forget('email_change_otp_' . $user->id);

            $ancienEmail = $user->email;
            $user->update(['email' => $cached['new_email']]);

            Log::info('EmailOTP Email mis à jour', [
                'user_id'     => $user->id,
                'ancien_email' => $ancienEmail,
                'nouveau_email' => $cached['new_email'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Adresse email mise à jour avec succès.',
                'data'    => ['email' => $user->email]
            ]);

        } catch (\Exception $e) {
            Log::error('EmailOTP Échec mise à jour email', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    /**
     * Vérifier le mot de passe puis envoyer un SMS ou Email OTP
     * POST /api/utilisateurs/profil/request-phone-otp
     */
    public function requestPhoneOtp(Request $request): JsonResponse
    {
        $user        = $request->user();
        $isSocial    = !empty($user->google_id) || !empty($user->apple_id);

        // Règles de validation — le mot de passe est requis sauf pour les comptes sociaux
        $rules = [
            'indicatif' => 'required|string|max:10',
            'contact'   => 'required|string|max:20|unique:users,contact,' . $user->id,
        ];
        if (!$isSocial) {
            $rules['current_password'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules, [
            'contact.unique'           => 'Ce numéro de téléphone est déjà utilisé par un autre compte.',
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        // --- Utilisateurs normaux : vérifier le mot de passe puis envoyer le SMS OTP ---
        if (!$isSocial) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe incorrect.'
                ], 422);
            }

            try {
                $phone = preg_replace('/[^0-9]/', '', $request->indicatif . $request->contact);
                $otp   = rand(100000, 999999);

                Cache::put('phone_change_otp_' . $user->id, [
                    'otp'       => $otp,
                    'indicatif' => $request->indicatif,
                    'contact'   => $request->contact,
                    'phone'     => $phone,
                ], now()->addMinutes(10));

                Log::info('PhoneOTP [SMS] Tentative envoi', [
                    'user_id' => $user->id,
                    'phone'   => $phone,
                ]);

                $smsService = app(YellikaSmsService::class);
                $result = $smsService->sendSms($phone, "Votre code de confirmation pour changer votre numéro Plateau App : " . $otp);

                if (!$result || (isset($result['success']) && !$result['success'])) {
                    Log::error('PhoneOTP [SMS] Échec envoi', [
                        'user_id' => $user->id,
                        'phone'   => $phone,
                        'result'  => $result,
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Erreur lors de l\'envoi du SMS. Vérifiez le numéro et réessayez.'
                    ], 500);
                }

                Log::info('PhoneOTP [SMS] Envoyé avec succès', [
                    'user_id' => $user->id,
                    'phone'   => $phone,
                ]);

                return response()->json([
                    'success'              => true,
                    'requires_email_otp'   => false,
                    'message'              => 'Code SMS envoyé au ' . $request->indicatif . ' ' . $request->contact . '. Valable 10 minutes.'
                ]);

            } catch (\Exception $e) {
                Log::error('PhoneOTP [SMS] Exception', [
                    'user_id' => $user->id,
                    'error'   => $e->getMessage(),
                ]);
                return response()->json(['success' => false, 'message' => 'Erreur serveur lors de l\'envoi du code.'], 500);
            }
        }

        // --- Utilisateurs Google/Apple : envoyer un OTP par email pour confirmer l'identité ---
        if (empty($user->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune adresse email associée à votre compte. Contactez le support.'
            ], 422);
        }

        try {
            $emailOtp = rand(100000, 999999);

            Cache::put('phone_change_email_otp_' . $user->id, [
                'otp'       => $emailOtp,
                'indicatif' => $request->indicatif,
                'contact'   => $request->contact,
            ], now()->addMinutes(10));

            Log::info('PhoneOTP [EMAIL] Tentative envoi', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'nouveau_contact' => $request->indicatif . $request->contact,
            ]);

            Mail::raw(
                "Bonjour " . ($user->prenom ?? $user->name) . ",\n\n"
                . "Votre code de confirmation pour changer votre numéro de téléphone sur Plateau App est :\n\n"
                . "    " . $emailOtp . "\n\n"
                . "Ce code est valable 10 minutes.\n"
                . "Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Code de confirmation - Changement de numéro Plateau App');
                }
            );

            Log::info('PhoneOTP [EMAIL] Envoyé avec succès', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);

            return response()->json([
                'success'            => true,
                'requires_email_otp' => true,
                'message'            => 'Un code de confirmation a été envoyé à votre adresse email ' . $user->email . '. Valable 10 minutes.'
            ]);

        } catch (\Exception $e) {
            Log::error('PhoneOTP [EMAIL] Échec envoi', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'envoi du code email.'], 500);
        }
    }

    /**
     * [Utilisateurs Google/Apple uniquement]
     * Envoyer un OTP par email pour confirmer le changement de numéro (sans mot de passe)
     * POST /api/utilisateurs/profil/social/request-phone-otp
     */
    public function requestPhoneOtpSocial(Request $request): JsonResponse
    {
        $user = $request->user();

        // Bloquer les comptes normaux sur cet endpoint
        if (empty($user->google_id) && empty($user->apple_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Cet endpoint est réservé aux comptes Google/Apple. Les comptes normaux doivent utiliser /request-phone-otp.'
            ], 403);
        }

        if (empty($user->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune adresse email associée à votre compte. Contactez le support.'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'indicatif' => 'required|string|max:10',
            'contact'   => 'required|string|max:20|unique:users,contact,' . $user->id,
        ], [
            'contact.unique' => 'Ce numéro de téléphone est déjà utilisé par un autre compte.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $emailOtp = rand(100000, 999999);

            Cache::put('phone_change_email_otp_' . $user->id, [
                'otp'       => $emailOtp,
                'indicatif' => $request->indicatif,
                'contact'   => $request->contact,
            ], now()->addMinutes(10));

            Log::info('PhoneOTP [EMAIL/SOCIAL] Tentative envoi', [
                'user_id'         => $user->id,
                'email'           => $user->email,
                'nouveau_contact' => $request->indicatif . $request->contact,
            ]);

            Mail::raw(
                "Bonjour " . ($user->prenom ?? $user->name) . ",\n\n"
                . "Votre code de confirmation pour changer votre numéro de téléphone sur Plateau App est :\n\n"
                . "    " . $emailOtp . "\n\n"
                . "Ce code est valable 10 minutes.\n"
                . "Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Code de confirmation - Changement de numéro Plateau App');
                }
            );

            Log::info('PhoneOTP [EMAIL/SOCIAL] Envoyé avec succès', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Un code de confirmation a été envoyé à ' . $user->email . '. Valable 10 minutes.'
            ]);

        } catch (\Exception $e) {
            Log::error('PhoneOTP [EMAIL/SOCIAL] Échec envoi', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'envoi du code email.'], 500);
        }
    }

    /**
     * Vérifier l'OTP email (Google/Apple) puis envoyer le SMS OTP vers le nouveau numéro
     * POST /api/utilisateurs/profil/verify-email-otp
     */
    public function verifyEmailOtp(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        $cached = Cache::get('phone_change_email_otp_' . $user->id);

        if (!$cached) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun code email en attente. Veuillez d\'abord faire une demande.'
            ], 400);
        }

        if ((string) $cached['otp'] !== (string) $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Code email incorrect ou expiré.'
            ], 422);
        }

        // Email OTP validé → mettre à jour le numéro directement
        try {
            Cache::forget('phone_change_email_otp_' . $user->id);

            $ancienContact = $user->indicatif . $user->contact;

            $user->update([
                'indicatif' => $cached['indicatif'],
                'contact'   => $cached['contact'],
            ]);

            Log::info('PhoneOTP [EMAIL] Numéro mis à jour', [
                'user_id'        => $user->id,
                'ancien_contact' => $ancienContact,
                'nouveau_contact' => $cached['indicatif'] . $cached['contact'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Numéro de téléphone mis à jour avec succès.',
                'data'    => [
                    'indicatif' => $user->indicatif,
                    'contact'   => $user->contact,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('PhoneOTP [EMAIL] Échec mise à jour numéro', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    /**
     * Vérifier l'OTP et mettre à jour le numéro de téléphone
     * PUT /api/utilisateurs/profil/update-phone
     */
    public function updatePhone(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $cached = Cache::get('phone_change_otp_' . $user->id);

            if (!$cached) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun code OTP en attente. Veuillez d\'abord demander un code.'
                ], 400);
            }

            if ((string) $cached['otp'] !== (string) $request->otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code OTP incorrect ou expiré.'
                ], 422);
            }

            Cache::forget('phone_change_otp_' . $user->id);

            $ancienContact = $user->indicatif . $user->contact;

            $user->update([
                'indicatif' => $cached['indicatif'],
                'contact'   => $cached['contact'],
            ]);

            Log::info('PhoneOTP [SMS] Numéro mis à jour', [
                'user_id'         => $user->id,
                'ancien_contact'  => $ancienContact,
                'nouveau_contact' => $cached['indicatif'] . $cached['contact'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Numéro de téléphone mis à jour avec succès.',
                'data'    => [
                    'indicatif' => $user->indicatif,
                    'contact'   => $user->contact,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('PhoneOTP [SMS] Échec mise à jour numéro', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur lors de la mise à jour du numéro.'
            ], 500);
        }
    }

    /**
     * Mise à jour du mot de passe
     * PUT /api/utilisateurs/profil/password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();

            // Vérifier le mot de passe actuel
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le mot de passe actuel est incorrect'
                ], 422);
            }

            // Mettre à jour le mot de passe
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe mis à jour avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur updatePassword: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du mot de passe'
            ], 500);
        }
    }
}
