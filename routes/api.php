<?php

use App\Http\Controllers\AdvertisementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FishController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReportedContentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::post('/selected-fish-compatibility', [FishController::class, 'getSelectedFishCompatibility']);

Route::post('/chat', ChatController::class);

Route::post('/advertisement/getTopRatedAdvertisements', [AdvertisementController::class, 'getTopRatedAdvertisements']);
Route::post('/advertisement/loadAdvertisementsByCategory', [AdvertisementController::class, 'loadAdvertisementsByCategory']);
Route::post('/advertisement/filterAdvertisements', [AdvertisementController::class, 'filterAdvertisements']);
Route::post('/advertisement/searchRelatedAdvertisements', [AdvertisementController::class, 'searchRelatedAdvertisements']);

Route::post('/review/getRatingCounts', [ReviewController::class, 'getRatingCounts']);
Route::post('/review/showReviewByID', [ReviewController::class, 'showReviewByID']);
Route::post('/review/getReviewSummary', [ReviewController::class, 'getReviewSummary']);

//Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logoutAll', [AuthController::class, 'logoutAll']);
    Route::post('/send-verification-email', [AuthController::class, 'sendVerificationEmail']);

    Route::post('/advertisement/getAdvertisementById', [AdvertisementController::class, 'getAdvertisementById']);

    Route::post('/report/addReport', [ReportedContentController::class, 'addReport']);

    Route::middleware('role:user')->group(function () {
        Route::get('/user-info/exists', [InformationController::class, 'hasUserInfo']); // Check if user information exists (true/false)
        Route::get('/user-info/get', [InformationController::class, 'getUserInfo']);
        Route::post('/user-info/update', [InformationController::class, 'updateUserInfo']);

        Route::post('/advertisement/addAdvertisement', [AdvertisementController::class, 'addAdvertisement']);
        Route::post('/advertisement/deleteAdvertisement', [AdvertisementController::class, 'deleteAdvertisement']);
        Route::post('/advertisement/updateAdvertisement', [AdvertisementController::class, 'updateAdvertisement']);
        Route::post('/advertisement/getUserAdvertisements', [AdvertisementController::class, 'getUserAdvertisements']);

        Route::post('/review/addReview', [ReviewController::class, 'addReview']);
        Route::post('/review/updateReview', [ReviewController::class, 'updateReview']);
        Route::post('/review/destroyReview', [ReviewController::class, 'destroyReview']);
        // User-specific routes
    });

    Route::middleware('role:shop')->group(function () {
        Route::get('/shop-info/exists', [InformationController::class, 'hasShopInfo']); // Check if shop information exists (true/false)
        Route::get('/shop-info/get', [InformationController::class, 'getShopInfo']);
        Route::post('/shop-info/update', [InformationController::class, 'updateShopInfo']);

        Route::post('/advertisement/addAdvertisement', [AdvertisementController::class, 'addAdvertisement']);
        Route::post('/advertisement/deleteAdvertisement', [AdvertisementController::class, 'deleteAdvertisement']);
        Route::post('/advertisement/updateAdvertisement', [AdvertisementController::class, 'updateAdvertisement']);
        Route::post('/advertisement/getUserAdvertisements', [AdvertisementController::class, 'getUserAdvertisements']);
        // Shop-specific routes
    });

    Route::middleware('role:admin')->group(function () {
        Route::post('/advertisement/setAdvertisementStatus', [AdvertisementController::class, 'setAdvertisementStatus']);
        Route::post('/advertisement/updateReviewStatus', [AdvertisementController::class, 'updateReviewStatus']);

        Route::post('/review/updateReviewStatus', [ReviewController::class, 'updateReviewStatus']);

        Route::post('/report/updateReport', [ReportedContentController::class, 'updateReport']);
        Route::post('/report/destroyReport', [ReportedContentController::class, 'destroyReport']);
        Route::post('/report/showReportsByType', [ReportedContentController::class, 'showReportsByType']);
        // Admin-specific routes
    });
});
