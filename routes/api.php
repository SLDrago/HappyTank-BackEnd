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
use App\Http\Controllers\AiController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use Laravel\Jetstream\Rules\Role;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/', function () {
    return response()->json(['message' => 'API is working!'], 200);
});

//Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail']);
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
Route::post('/advertisement/getAdvertisementImagesByAdId', [AdvertisementController::class, 'getAdvertisementImagesByAdId']);
Route::get('/advertisement/getDiscountedAdvertisements', [AdvertisementController::class, 'getDiscountedAdvertisements']);
Route::post('/advertisement/filterAdvertisementsDiscounts', [AdvertisementController::class, 'filterAdvertisementsDiscounts']);
Route::post('/advertisement/getAllAdvertisementDetails', [AdvertisementController::class, 'getAllAdvertisementDetails']);

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

Route::post('/ai/identifyFish', [AiController::class, 'getFishNameFromImage']);
Route::post('/ai/generateFishTankImage', [AiController::class, 'generateFishTankImage']);

Route::post('/contact', [ContactUsController::class, 'store']);

Route::get('/posts', [PostController::class, 'index']);
Route::post('/posts/search', [PostController::class, 'search']);

Route::post('/report/showReportedAdvertisements', [ReportedContentController::class, 'showReportedAdvertisements']); //should be admin only
Route::post('/report/showReportedPosts', [ReportedContentController::class, 'showReportedPosts']); //should be admin only
Route::post('/analytics/getDashboardData', [DashboardController::class, 'getDashboardData']); //should be admin only

//Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logoutAll', [AuthController::class, 'logoutAll']);
    Route::post('/send-verification-email', [AuthController::class, 'sendVerificationEmail']);

    Route::get('/user', [UserController::class, 'user']);
    Route::post('/user/updateNameEmail', [UserController::class, 'updateNameEmail']);
    Route::post('/user/updatePassword', [UserController::class, 'updatePassword']);
    Route::post('/user/updateProfilePicture', [UserController::class, 'updateProfilePicture']);
    Route::post('/user/updateBannerPhoto', [UserController::class, 'updateBannerPhoto']);
    Route::post('/user/destroy', [UserController::class, 'destroy']);

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
        Route::post('/advertisement/deleteAdvertisementImage', [AdvertisementController::class, 'deleteAdvertisementImage']);
        Route::post('/advertisement/AddAdvertisementImage', [AdvertisementController::class, 'AddAdvertisementImage']);

        Route::post('/posts', [PostController::class, 'store']);
        Route::post('/posts/like', [LikeController::class, 'likePost']);
        Route::post('/posts/unlike', [LikeController::class, 'unlikePost']);

        Route::post('/posts/addComments', [CommentController::class, 'store']);
        Route::post('/comments/{comment}/like', [LikeController::class, 'likeComment']);
        Route::delete('/comments/{comment}/like', [LikeController::class, 'unlikeComment']);

        Route::post('/posts/update', [PostController::class, 'update']);
        Route::delete('/posts/{post}', [PostController::class, 'destroy']);
        Route::post('/posts/report', [PostController::class, 'report']);
        Route::get('/getPost/{post}', [PostController::class, 'getPost']);
        Route::get('/posts/getUserPosts', [PostController::class, 'getUserPosts']);
        // User and Shop routes
    });

    Route::middleware('role:admin')->group(function () {
        Route::post('/advertisement/setAdvertisementStatus', [AdvertisementController::class, 'setAdvertisementStatus']);
        Route::post('/advertisement/updateReviewStatus', [AdvertisementController::class, 'updateReviewStatus']);

        Route::post('/review/updateReviewStatus', [ReviewController::class, 'updateReviewStatus']);

        Route::post('/report/updateReport', [ReportedContentController::class, 'updateReport']);
        Route::post('/report/destroyReport', [ReportedContentController::class, 'destroyReport']);
        Route::post('/report/showReportsByType', [ReportedContentController::class, 'showReportsByType']);
        Route::post('/report/getReportedAdvertisementById', [ReportedContentController::class, 'getReportedAdvertisementById']);
        Route::post('/report/getReportedPostsById', [ReportedContentController::class, 'getReportedPostsById']);


        Route::post('/category/addCategory', [CategoryController::class, 'addCategory']);
        Route::post('/category/updateCategory', [CategoryController::class, 'updateCategory']);
        Route::post('/category/deleteCategory', [CategoryController::class, 'deleteCategory']);

        Route::post('/city/addCity', [CityController::class, 'store']);
        Route::post('/city/updateCity', [CityController::class, 'update']);
        Route::post('/city/deleteCity', [CityController::class, 'destroy']);

        Route::post('/fish/addFish', [FishController::class, 'addFish']);
        Route::post('/fish/updateFish', [FishController::class, 'updateFish']);
        Route::post('/fish/deleteFish', [FishController::class, 'removeFish']);

        Route::post('/fish/addFishImage', [FishImageController::class, 'store']);

        // Admin-specific routes
    });
});
