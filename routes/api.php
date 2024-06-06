<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FishController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/selected-fish-compatibility', [FishController::class, 'getSelectedFishCompatibility']);
Route::post('/chat', ChatController::class);

//Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logoutAll', [AuthController::class, 'logoutAll']);
});

Route::middleware(['auth:sanctum', 'check.user.role:user'])->group(function () {
    // User-specific routes
});

Route::middleware(['auth:sanctum', 'check.user.role:shop'])->group(function () {
    // Shop-specific routes
});

Route::middleware(['auth:sanctum', 'check.user.role:admin'])->group(function () {
    // Admin-specific routes
});

