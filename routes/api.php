<?php

use App\Http\Controllers\Api\Authenticate\ConnexionController;
use App\Http\Controllers\Api\Authenticate\RegisterController;
use App\Http\Controllers\Api\Authenticate\UserLoginController;
use App\Http\Controllers\Api\Utilisateurs\DemandeNaissanceController;
use App\Http\Controllers\Api\Utilisateurs\DemandeMariageController;
use App\Http\Controllers\Api\Utilisateurs\DemandeDecesController;
use App\Http\Controllers\Api\Utilisateurs\StatistiqueController;
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
});

// LIVREURS - Routes publiques
Route::prefix('livreur')->group(function () {
    Route::post('/login', [LivreurAuthenticateController::class, 'handleLogin']);
    Route::post('/define-access', [LivreurAuthenticateController::class, 'defineAccess']);
    Route::post('/submit-define-access', [LivreurAuthenticateController::class, 'submitDefineAccess']);
});
// --- AJOUT DE LA SECTION WEBHOOK ---
// Routes publiques pour les notifications de services externes (Webhooks)
Route::prefix('webhooks')->group(function () {
    /**
     * C'est l'URL que CinetPay appellera après un paiement (notify_url).
     * Le nom 'api.cinetpay.notify.deces' est celui que nous avons défini dans le contrôleur.
     */
    Route::post('/cinetpay/notify/deces', [DemandeDecesController::class, 'handlePaymentNotification'])
         ->name('api.cinetpay.notify.deces');
});
// --- FIN DE L'AJOUT ---
Route::middleware('auth:sanctum')->group(function () {

    // ROUTES UTILISATEURS AUTHENTIFIÉS
    Route::prefix('utilisateurs')->group(function () {
        
        // Route simple pour vérifier si le token est bon
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Routes pour les demandes de Naissance
        Route::prefix('demandes/naissance')->group(function () {
            Route::get('/', [DemandeNaissanceController::class, 'index']);
            Route::post('/', [DemandeNaissanceController::class, 'store']);
        });

        // Routes pour les demandes de Mariage
        Route::prefix('demandes/mariage')->group(function () {
            Route::get('/', [DemandeMariageController::class, 'index']);
            Route::post('/', [DemandeMariageController::class, 'store']);
        });

        // Routes pour les demandes de Décès
        Route::prefix('demandes/deces')->group(function () {
            Route::get('/', [DemandeDecesController::class, 'index']);
            Route::post('/', [DemandeDecesController::class, 'store']);
            Route::get('/{id}/check-payment', [DemandeDecesController::class, 'checkPaymentStatus']);
            Route::delete('/{deces}', [DemandeDecesController::class, 'destroy']);
        });

        // Routes pour les statistiques et suivis
        Route::prefix('demandes')->group(function () {
            Route::get('/statistiques', [StatistiqueController::class, 'statistiquesParStatut']);
            Route::get('/toutes', [StatistiqueController::class, 'listeToutesDemandes']);
            Route::post('/suivi-reference', [StatistiqueController::class, 'suiviDemandeParReference']);
            Route::get('/{type}/{id}', [StatistiqueController::class, 'getDemandeSpecifique']);
            Route::get('/{type}/{id}/suivi', [StatistiqueController::class, 'suiviDemande']);
        });
        
        // PROFIL UTILISATEUR
        Route::prefix('profil')->group(function () {
            Route::get('/', [UserProfilController::class, 'getProfil']);
            Route::post('/photo', [UserProfilController::class, 'updateProfilePicture']);
            Route::delete('/photo', [UserProfilController::class, 'deleteProfilePicture']);
            Route::put('/informations', [UserProfilController::class, 'updateInformations']);
            Route::put('/password', [UserProfilController::class, 'updatePassword']);
        });

    }); // Fin du groupe utilisateurs

     // ROUTES LIVREUR AUTHENTIFIÉ (guard spécifique = livreur)
     Route::prefix('livreur')->middleware('auth:livreurApi')->group(function () {
        
        // Routes pour les livraisons
        Route::prefix('livraisons')->group(function () {
            Route::get('/', [LivraisonController::class, 'listeLivraisons']);
            Route::get('/{type}/{id}', [LivraisonController::class, 'getLivraison']);
            Route::post('/valider', [LivraisonController::class, 'validerLivraison']);
            Route::post('/verifier-reference', [LivraisonController::class, 'checkReference']);
            Route::get('/statistiques/general', [LivraisonController::class, 'statistiques']);
        });

        // Routes pour le profil du livreur
        Route::prefix('profil')->group(function () {
            Route::get('/', [ProfilLivreurController::class, 'getProfil']);
            Route::post('/photo', [ProfilLivreurController::class, 'updateProfilePicture']);
            Route::delete('/photo', [ProfilLivreurController::class, 'deleteProfilePicture']);
            Route::put('/informations', [ProfilLivreurController::class, 'updateInformations']);
            Route::put('/password', [ProfilLivreurController::class, 'updatePassword']);
            Route::put('/disponibilite', [ProfilLivreurController::class, 'updateDisponibilite']);
        });
        
    }); // Fin du groupe livreur avec guard spécifique

}); // Fin du groupe auth:sanctum

