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
use App\Http\Controllers\Api\Livreur\LivreurAuthenticateController;
use App\Http\Controllers\Api\Livreur\LivraisonController;
use App\Http\Controllers\Api\Livreur\ProfilLivreurController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ADMIN - Routes publiques
Route::post('/admin/login', [ConnexionController::class, 'handleLogin']);

// UTILISATEURS - Routes publiques
Route::prefix('utilisateurs')->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [UserLoginController::class, 'login']);
    Route::post('/forgot-password', [PasswordforgotController::class, 'forgotPassword']);
    Route::post('/verify-reset-code', [PasswordforgotController::class, 'verifyResetCode']);
    Route::post('/reset-password', [PasswordforgotController::class, 'resetPassword']);
});

// LIVREURS - Routes publiques
Route::prefix('livreur')->group(function () {
    Route::post('/login', [LivreurAuthenticateController::class, 'handleLogin']);
    Route::post('/define-access', [LivreurAuthenticateController::class, 'defineAccess']);
    Route::post('/submit-define-access', [LivreurAuthenticateController::class, 'submitDefineAccess']);
});
// --- AJOUT DE LA SECTION WEBHOOK ---
// Routes publiques pour les notifications de services externes (Webhooks)
// --- Routes publiques Webhook et Statut ---

Route::prefix('webhooks')->group(function () {
    Route::post('/cinetpay/notify/deces', [DemandeDecesController::class, 'handlePaymentNotification'])
        ->name('api.cinetpay.notify.deces');
    
    Route::post('/cinetpay/notify/mariage', [DemandeMariageController::class, 'handlePaymentNotification'])
        ->name('api.cinetpay.notify.mariage');

    // ✅ AJOUTÉ
    Route::post('/cinetpay/notify/naissance', [DemandeNaissanceController::class, 'handlePaymentNotification'])
        ->name('api.cinetpay.notify.naissance');
});

// Routes de polling de statut (publiques)
Route::get('/deces/payment-status/{reference}', [DemandeDecesController::class, 'getPaymentStatus']);
Route::get('/mariage/payment-status/{reference}', [DemandeMariageController::class, 'getPaymentStatus']);
// ✅ AJOUTÉ
Route::get('/naissance/payment-status/{reference}', [DemandeNaissanceController::class, 'getPaymentStatus']);


// --- FIN Routes publiques ---


Route::middleware('auth:sanctum')->group(function () {

    // ROUTES UTILISATEURS AUTHENTIFIÉS
    Route::prefix('utilisateurs')->group(function () {
        
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Routes pour les demandes de Naissance
        Route::prefix('demandes/naissance')->group(function () {
            Route::get('/', [DemandeNaissanceController::class, 'index']);
            Route::post('/', [DemandeNaissanceController::class, 'store']);
            // ✅ NOUVELLE ROUTE AJOUTÉE
            Route::post('/{naissance}/retry-payment', [DemandeNaissanceController::class, 'retryPayment']);
            Route::delete('/{naissance}', [DemandeNaissanceController::class, 'destroy']);
        });

        // Routes pour les demandes de Mariage
        Route::prefix('demandes/mariage')->group(function () {
            Route::get('/', [DemandeMariageController::class, 'index']);
            Route::post('/', [DemandeMariageController::class, 'store']);
            // ✅ NOUVELLE ROUTE AJOUTÉE
            Route::post('/{mariage}/retry-payment', [DemandeMariageController::class, 'retryPayment']);
            Route::delete('/{mariage}', [DemandeMariageController::class, 'destroy']);
        });

        // Routes pour les demandes de Décès
        Route::prefix('demandes/deces')->group(function () {
            Route::get('/', [DemandeDecesController::class, 'index']);
            Route::post('/', [DemandeDecesController::class, 'store']);
            Route::post('/{deces}/retry-payment', [DemandeDecesController::class, 'retryPayment']);
            Route::delete('/{deces}', [DemandeDecesController::class, 'destroy']);
        });

        // ... (Routes Statistiques, Profil)
        Route::prefix('demandes')->group(function () {
            Route::get('/statistiques', [StatistiqueController::class, 'statistiquesParStatut']);
            Route::get('/toutes', [StatistiqueController::class, 'listeToutesDemandes']);
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
        });
        // --- NOUVELLES ROUTES POUR LES RENDEZ-VOUS ---
        Route::prefix('demandes/rendezvous')->group(function () {
            Route::get('/', [RdvApiController::class, 'index']);
            Route::post('/', [RdvApiController::class, 'store']);
        });

    }); // Fin groupe utilisateurs

    // ... (Routes Livreur)
    Route::prefix('livreur')->middleware('auth:livreurApi')->group(function () {
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