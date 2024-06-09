<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FishController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InformationController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::get('auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/selected-fish-compatibility', [FishController::class, 'getSelectedFishCompatibility']);
Route::post('/chat', ChatController::class);
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

//Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logoutAll', [AuthController::class, 'logoutAll']);
    Route::post('/send-verification-email', [AuthController::class, 'sendVerificationEmail']);


    Route::middleware('role:user')->group(function () {
        Route::get('/user-info/exists', [InformationController::class, 'hasUserInfo']); // Check if user information exists (true/false)
        Route::get('/user-info/get', [InformationController::class, 'getUserInfo']);
        Route::post('/user-info/update', [InformationController::class, 'updateUserInfo']);
        // User-specific routes
    });

    Route::middleware('role:shop')->group(function () {
        Route::get('/shop-info/exists', [InformationController::class, 'hasShopInfo']); // Check if shop information exists (true/false)
        Route::get('/shop-info/get', [InformationController::class, 'getShopInfo']);
        Route::post('/shop-info/update', [InformationController::class, 'updateShopInfo']);
        // Shop-specific routes
    });

    Route::middleware('role:admin')->group(function () {
        // Admin-specific routes
    });
});
