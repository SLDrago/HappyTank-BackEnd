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
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\FishImageController;
use App\Http\Controllers\UserController;

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
Route::post('/chat/getFishTankRecommendations', [ChatController::class, 'getFishTankRecommendations'])->middleware('throttle:10,1');

Route::post('/advertisement/getTopRatedAdvertisements', [AdvertisementController::class, 'getTopRatedAdvertisements']);
Route::post('/advertisement/loadAdvertisementsByCategory', [AdvertisementController::class, 'loadAdvertisementsByCategory']);
Route::post('/advertisement/filterAdvertisements', [AdvertisementController::class, 'filterAdvertisements']);
Route::post('/advertisement/searchRelatedAdvertisements', [AdvertisementController::class, 'searchRelatedAdvertisements']);
Route::post('/advertisement/searchRelatedFishAdvertisements', [AdvertisementController::class, 'searchRelatedFishAdvertisements']);
Route::post('/advertisement/getAdvertisementById', [AdvertisementController::class, 'getAdvertisementById']);

Route::post('/review/getRatingCounts', [ReviewController::class, 'getRatingCounts']);
Route::post('/review/showReviewByID', [ReviewController::class, 'showReviewByID']);
Route::post('/review/getReviewSummary', [ReviewController::class, 'getReviewSummary']);
Route::post('/review/getReviewByAdvertisementId', [ReviewController::class, 'getReviewByAdvertisementId']);

Route::get('/getCategories', [CategoryController::class, 'getCategories']); //

Route::get('/getAllCities', [CityController::class, 'index']); //
Route::get('/getCityByID', [CityController::class, 'show']); //

Route::get('/getFishNames', [FishController::class, 'getFishNames']); //
Route::get('/getFishById', [FishController::class, 'getFishById']); //

Route::post('/getSellerCardDetails', [UserController::class, 'getSellerCardDetails']); //

Route::post('/fish/getFishByIdWithImages', [FishController::class, 'getFishByIdWithImages']);


//Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logoutAll', [AuthController::class, 'logoutAll']);
    Route::post('/send-verification-email', [AuthController::class, 'sendVerificationEmail']);

    Route::get('/user', [UserController::class, 'user']);
    Route::post('/updatePassword', [UserController::class, 'updatePassword']);
    Route::post('/updateBannerPhoto', [UserController::class, 'updateBannerPhoto']);
    Route::post('/updateProfilePicture', [UserController::class, 'updateProfilePicture']);
    Route::post('/removeProfile', [UserController::class, 'destroy']);
    Route::post('/updateProfileNameEmail', [UserController::class, 'update']);

    Route::post('/report/addReport', [ReportedContentController::class, 'addReport']);



    Route::middleware('role:user')->group(function () {
        Route::get('/user-info/exists', [InformationController::class, 'hasUserInfo']); // Check if user information exists (true/false)
        Route::get('/user-info/get', [InformationController::class, 'getUserInfo']);
        Route::post('/user-info/update', [InformationController::class, 'updateUserInfo']);

        Route::get('/advertisement/getUsersAdvertisementCount', [AdvertisementController::class, 'getUsersAdvertisementCount']);

        Route::post('/review/addReview', [ReviewController::class, 'addReview']);
        Route::post('/review/updateReview', [ReviewController::class, 'updateReview']);
        Route::post('/review/destroyReview', [ReviewController::class, 'destroyReview']);
        // User-specific routes
    });

    Route::middleware('role:shop')->group(function () {
        Route::get('/shop-info/exists', [InformationController::class, 'hasShopInfo']); // Check if shop information exists (true/false)
        Route::get('/shop-info/get', [InformationController::class, 'getShopInfo']);
        Route::post('/shop-info/update', [InformationController::class, 'updateShopInfo']);
        // Shop-specific routes
    });

    Route::middleware('role:user,shop')->group(function () {
        Route::post('/advertisement/addAdvertisement', [AdvertisementController::class, 'addAdvertisement']);
        Route::post('/advertisement/deleteAdvertisement', [AdvertisementController::class, 'deleteAdvertisement']);
        Route::post('/advertisement/updateAdvertisement', [AdvertisementController::class, 'updateAdvertisement']);
        Route::post('/advertisement/getUserAdvertisements', [AdvertisementController::class, 'getUserAdvertisements']);
        // User and Shop routes
    });

    Route::middleware('role:admin')->group(function () {
        Route::post('/advertisement/setAdvertisementStatus', [AdvertisementController::class, 'setAdvertisementStatus']);
        Route::post('/advertisement/updateReviewStatus', [AdvertisementController::class, 'updateReviewStatus']);

        Route::post('/review/updateReviewStatus', [ReviewController::class, 'updateReviewStatus']);

        Route::post('/report/updateReport', [ReportedContentController::class, 'updateReport']);
        Route::post('/report/destroyReport', [ReportedContentController::class, 'destroyReport']);
        Route::post('/report/showReportsByType', [ReportedContentController::class, 'showReportsByType']);

        Route::post('/category/addCategory', [CategoryController::class, 'addCategory']);
        Route::post('/category/updateCategory', [CategoryController::class, 'updateCategory']);
        Route::post('/category/deleteCategory', [CategoryController::class, 'deleteCategory']);

        Route::post('/city/addCity', [CityController::class, 'store']);
        Route::post('/city/updateCity', [CityController::class, 'update']);
        Route::post('/city/deleteCity', [CityController::class, 'destroy']);

        Route::post('/fish/addFish', [FishController::class, 'addFish']);
        Route::post('/fish/updateFish', [FishController::class, 'updateFish']);
        Route::post('/fish/deleteFish', [FishController::class, 'deleteFish']);

        Route::post('/fish/addFishImage', [FishImageController::class, 'store']);
    });
    // Admin-specific routes
});
