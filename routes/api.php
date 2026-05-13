<?php

use App\Http\Controllers\Api\Authenticate\ConnexionController;
use App\Http\Controllers\Api\Authenticate\RegisterController;
use App\Http\Controllers\Api\Authenticate\UserLoginController;
use App\Http\Controllers\Api\Authenticate\PasswordforgotController;
use App\Http\Controllers\Api\Utilisateurs\DemandeNaissanceController;
use App\Http\Controllers\Api\Utilisateurs\DemandeMariageController;
use App\Http\Controllers\Api\Utilisateurs\DemandeDecesController;
use App\Http\Controllers\Api\Utilisateurs\StatistiqueController;
use App\Http\Controllers\Api\Utilisateurs\RdvApiController;
use App\Http\Controllers\Api\Utilisateurs\Profil\UserProfilController;
use App\Http\Controllers\Api\Utilisateurs\NotificationController;
use App\Http\Controllers\Api\Livreur\LivreurAuthenticateController;
use App\Http\Controllers\Api\Livreur\LivraisonController;
use App\Http\Controllers\Api\Livreur\ProfilLivreurController;
use App\Http\Controllers\Api\Livreur\LivreurPasswordForgotController;
use App\Http\Controllers\Api\WaveWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ADMIN - Routes publiques
Route::post('/admin/login', [ConnexionController::class, 'handleLogin']);

// UTILISATEURS - Routes publiques
Route::prefix('utilisateurs')->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/register-email', [RegisterController::class, 'registerEmail']);
    Route::post('/login', [UserLoginController::class, 'login']);
    Route::post('/forgot-password', [PasswordforgotController::class, 'forgotPassword']);
    Route::post('/verify-reset-code', [PasswordforgotController::class, 'verifyResetCode']);
    Route::post('/reset-password', [PasswordforgotController::class, 'resetPassword']);

    // Auth Mobile (Social)
    Route::post('/send-otp', [\App\Http\Controllers\Api\Authenticate\OtpController::class, 'sendOtp']);
    Route::post('/verify-otp', [\App\Http\Controllers\Api\Authenticate\OtpController::class, 'verifyOtp']);
    Route::post('/register-minimal', [\App\Http\Controllers\Api\Authenticate\RegisterController::class, 'registerMinimal']);
    Route::post('/google', [\App\Http\Controllers\Api\Authenticate\GoogleAuthController::class, 'handleGoogleAuth']);
    Route::post('/apple', [\App\Http\Controllers\Api\Authenticate\AppleAuthController::class, 'handleAppleAuth']);

    // Finalisation de profil (publiques — pas de Sanctum, identifiées par pending_token)
    Route::post('/finalize-profile/google', [\App\Http\Controllers\Api\Authenticate\ProfileCompletionController::class, 'finalizeProfileGoogle']);
    Route::post('/finalize-profile/apple', [\App\Http\Controllers\Api\Authenticate\ProfileCompletionController::class, 'finalizeProfileApple']);
    Route::post('/finalize-profile/phone', [\App\Http\Controllers\Api\Authenticate\ProfileCompletionController::class, 'finalizeProfilePhone']);
});

// LIVREURS - Routes publiques
Route::prefix('livreur')->group(function () {
    Route::post('/login', [LivreurAuthenticateController::class, 'handleLogin']);
    Route::post('/define-access', [LivreurAuthenticateController::class, 'defineAccess']);
    Route::post('/submit-define-access', [LivreurAuthenticateController::class, 'submitDefineAccess']);
    Route::post('/forgot-password', [LivreurPasswordForgotController::class, 'forgotPassword']);
    Route::post('/verify-reset-code', [LivreurPasswordForgotController::class, 'verifyResetCode']);
    Route::post('/reset-password', [LivreurPasswordForgotController::class, 'resetPassword']);
});

Route::prefix('webhooks')->group(function () {
    Route::post('/wave/notify', [WaveWebhookController::class, 'handleWebhook'])
        ->name('api.wave.notify');
    Route::match(['get', 'post', 'put'], '/mtn/notify', [\App\Http\Controllers\Api\Webhook\MtnWebhookController::class, 'handle'])
        ->name('api.mtn.notify');
});

// Routes de polling de statut (publiques)
Route::get('/deces/payment-status/{reference}', [DemandeDecesController::class, 'getPaymentStatus']);
Route::get('/mariage/payment-status/{reference}', [DemandeMariageController::class, 'getPaymentStatus']);
// ✅ AJOUTÉ
Route::get('/naissance/payment-status/{reference}', [DemandeNaissanceController::class, 'getPaymentStatus']);

// Route publique pour rechercher une demande par référence (etat + statut_livraison)
Route::get('/demandes/recherche', [StatistiqueController::class, 'rechercherParReference']);

Route::middleware(['apiMaintenance', 'auth:sanctum'])->group(function () {

    // ROUTES UTILISATEURS AUTHENTIFIÉS
    Route::prefix('utilisateurs')->group(function () {
        Route::post('/logout', [UserLoginController::class, 'logout']);
        Route::post('/deactivate', [UserLoginController::class, 'deactivateAccount']);
        Route::post('/toggle-push-notification', [UserLoginController::class, 'togglePushnotification']);

        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Routes pour les notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
            Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead']);
            Route::post('/test-push', [NotificationController::class, 'testPushNotification']); // Endpoint de test
        });

        // Routes pour les demandes de Naissance
        Route::prefix('demandes/naissance')->group(function () {
            Route::get('/', [DemandeNaissanceController::class, 'index']);
            Route::post('/', [DemandeNaissanceController::class, 'store']);
            Route::post('/{naissance}/retry-payment', [DemandeNaissanceController::class, 'retryPayment']);
            Route::post('/{naissance}/relancer', [DemandeNaissanceController::class, 'relancerDemande']);
            Route::post('/{naissance}/modifier', [DemandeNaissanceController::class, 'modifierDemande']);
            Route::get('/{naissance}/champs-a-modifier', [DemandeNaissanceController::class, 'getChampsAModifier']);
            Route::delete('/{naissance}', [DemandeNaissanceController::class, 'destroy']);
        });

        // Routes pour les demandes de Mariage
        Route::prefix('demandes/mariage')->group(function () {
            Route::get('/', [DemandeMariageController::class, 'index']);
            Route::post('/', [DemandeMariageController::class, 'store']);
            Route::post('/{mariage}/retry-payment', [DemandeMariageController::class, 'retryPayment']);
            Route::post('/{mariage}/relancer', [DemandeMariageController::class, 'relancerDemande']);
            Route::post('/{mariage}/modifier', [DemandeMariageController::class, 'modifierDemande']);
            Route::get('/{mariage}/champs-a-modifier', [DemandeMariageController::class, 'getChampsAModifier']);
            Route::delete('/{mariage}', [DemandeMariageController::class, 'destroy']);
        });

        // Routes pour les demandes de Décès
        Route::prefix('demandes/deces')->group(function () {
            Route::get('/', [DemandeDecesController::class, 'index']);
            Route::post('/', [DemandeDecesController::class, 'store']);
            Route::post('/{deces}/retry-payment', [DemandeDecesController::class, 'retryPayment']);
            Route::post('/{deces}/relancer', [DemandeDecesController::class, 'relancerDemande']);
            Route::post('/{deces}/modifier', [DemandeDecesController::class, 'modifierDemande']);
            Route::get('/{deces}/champs-a-modifier', [DemandeDecesController::class, 'getChampsAModifier']);
            Route::delete('/{deces}', [DemandeDecesController::class, 'destroy']);
        });

        // ... (Routes Statistiques, Profil)
        Route::prefix('demandes')->group(function () {
            Route::get('/statistiques', [StatistiqueController::class, 'statistiquesParStatut']);
            Route::get('/toutes', [StatistiqueController::class, 'listeToutesDemandes']);
            Route::get('/paiements', [StatistiqueController::class, 'historiquePaiements']);
            Route::get('/paiements/detail/{transactionId}', [StatistiqueController::class, 'getPaiementByTransaction']);
            Route::post('/suivi-reference', [StatistiqueController::class, 'suiviDemandeParReference']);
            Route::get('/{type}/{id}', [StatistiqueController::class, 'getDemandeSpecifique']);
            Route::get('/{type}/{id}/suivi', [StatistiqueController::class, 'suiviDemande']);
        });
        
        Route::prefix('profil')->group(function () {
            Route::get('/', [UserProfilController::class, 'getProfil']);
            Route::post('/photo', [UserProfilController::class, 'updateProfilePicture']);
            Route::delete('/photo', [UserProfilController::class, 'deleteProfilePicture']);
            Route::put('/informations', [UserProfilController::class, 'updateInformations']);
            Route::put('/password', [UserProfilController::class, 'updatePassword']);
            Route::post('/request-email-otp', [UserProfilController::class, 'requestEmailOtp']);
            Route::put('/update-email', [UserProfilController::class, 'updateEmail']);
            Route::post('/request-phone-otp', [UserProfilController::class, 'requestPhoneOtp']);
            Route::post('/social/request-phone-otp', [UserProfilController::class, 'requestPhoneOtpSocial']);
            Route::put('/verify-email-otp', [UserProfilController::class, 'verifyEmailOtp']);
            Route::put('/update-phone', [UserProfilController::class, 'updatePhone']);
        });
        // --- NOUVELLES ROUTES POUR LES RENDEZ-VOUS ---
        Route::prefix('demandes/rendezvous')->group(function () {
            Route::get('/', [RdvApiController::class, 'index']);
            Route::post('/', [RdvApiController::class, 'store']);
            Route::put('/{id}/cancel', [RdvApiController::class, 'cancel']);
            Route::get('/statistiques/{id}', [StatistiqueController::class, 'statistiquesRendezvousParId']);
        Route::get('/statistiques', [StatistiqueController::class, 'statistiquesRendezvousComplet']);
        Route::get('/{id}/details', [StatistiqueController::class, 'getRendezvousDetails']);
        });

    }); // Fin groupe utilisateurs

    // ... (Routes Livreur)
    Route::prefix('livreur')->middleware('auth:livreurApi')->group(function () {
        // Route de déconnexion
        Route::post('/logout', [LivreurAuthenticateController::class, 'logout']);
        
        Route::prefix('livraisons')->group(function () {
            Route::get('/', [LivraisonController::class, 'listeLivraisons']);
            Route::get('/historique', [LivraisonController::class, 'historiqueLivraisons']);
            Route::get('/statistiques/general', [LivraisonController::class, 'statistiques']);
            Route::get('/{type}/{id}', [LivraisonController::class, 'getLivraison']);
            Route::post('/valider', [LivraisonController::class, 'validerLivraison']);
            Route::post('/verifier-reference', [LivraisonController::class, 'checkReference']);
            
        });
        Route::prefix('profil')->group(function () {
            Route::get('/', [ProfilLivreurController::class, 'getProfil']);
            Route::post('/photo', [ProfilLivreurController::class, 'updateProfilePicture']);
            Route::delete('/photo', [ProfilLivreurController::class, 'deleteProfilePicture']);
            Route::put('/informations', [ProfilLivreurController::class, 'updateInformations']);
            Route::put('/password', [ProfilLivreurController::class, 'updatePassword']);
            Route::put('/disponibilite', [ProfilLivreurController::class, 'updateDisponibilite']);
        });
    }); // Fin groupe livreur

}); // Fin groupe auth:sanctum

// Route de test d'envoi de mail
Route::get('/test-email', function (\Illuminate\Http\Request $request) {
    $email = $request->query('email', 'redfieldluise@gmail.com');
    try {
        \Illuminate\Support\Facades\Mail::raw("Bonjour ! Ceci est un test direct depuis Laravel.\nSi vous recevez ce message, votre configuration SMTP Hostinger fonctionne parfaitement !", function ($message) use ($email) {
            $message->to($email)
                    ->subject('Test SMTP Plateau App');
        });
        return response()->json([
            'success' => true, 
            'message' => "L'e-mail a été envoyé au serveur SMTP avec succès pour l'adresse : $email. Vérifiez votre boîte de réception et vos spams."
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => "Erreur lors de l'envoi de l'e-mail.",
            'error' => $e->getMessage()
        ], 500);
    }
});