<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FarmController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\SocialLoginController;
use App\Http\Controllers\Api\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/send-otp', [PasswordResetController::class, 'sendOtp']);
Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

// Route::post('login', [RegisterController::class, 'login']);



Route::middleware('auth:api')->group( function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/get-profile', [UserController::class, 'getProfile']);

    //user
    Route::post('/edit-profile', [UserController::class, 'editProfile']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::post('/edit-image', [UserController::class, 'editImage']);
    Route::get('/delete-account', [UserController::class, 'deleteAccount']);

    //farm
    Route::post('/store-farm', [FarmController::class, 'storeFarm']);
    Route::post('/update-farm/{farm}', [FarmController::class, 'updateFarm']);
    Route::get('/get-farms', [FarmController::class, 'getFarms']);
    Route::get('/get-featured-farms', [FarmController::class, 'getFeaturedFarms']);
    Route::get('/get-farm-related-data', [FarmController::class, 'getFarmRelatedData']);
    Route::get('/get-near-by-farms', [FarmController::class, 'getNearByFarms']);
    Route::get('/delete-farm/{farm}', [FarmController::class, 'deleteFarm']);

    Route::post('/toggle-saved-farm', [FarmController::class, 'toggleSavedFarm']);
    Route::get('/get-saved-farms', [FarmController::class, 'getSavedFarms']);

    Route::post('/chats/send', [ChatController::class, 'sendMessage']); // Send message
    Route::get('/chats/{userId}', [ChatController::class, 'fetchChats']); // Fetch chat messages
    Route::get('/chats/unread/{userId}', [ChatController::class, 'unreadCount']); // Unread messages
    Route::get('/get-my-chats', [ChatController::class, 'getMyChats']); 
    Route::get('/total-unread-count', [ChatController::class, 'totalUnreadCount']); 

    // notifications
    Route::get('/get-notifications', [NotificationController::class, 'getNotifications']); 
});
Route::post('/handle-webhook', [ChatController::class, 'handleWebhook']); 
Route::post('/send-notification', [NotificationController::class, 'sendNotification']); 
Route::post('/login/apple', [SocialLoginController::class, 'login']);
Route::post('/social/login', [SocialLoginController::class, 'socialLogin']);

