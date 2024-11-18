<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FarmController;
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

// Route::post('login', [RegisterController::class, 'login']);



Route::middleware('auth:api')->group( function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    //user
    Route::post('/edit-profile', [UserController::class, 'editProfile']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::post('/edit-image', [UserController::class, 'editImage']);

    //farm
    Route::post('/store-farm', [FarmController::class, 'storeFarm']);
    Route::post('/update-farm/{farm}', [FarmController::class, 'updateFarm']);
    Route::get('/get-farms', [FarmController::class, 'getFarms']);
    Route::post('/toggle-saved-farm', [FarmController::class, 'toggleSavedFarm']);
    Route::get('/get-saved-farms', [FarmController::class, 'getSavedFarms']);
});
Route::get('/get-featured-farms', [FarmController::class, 'getFeaturedFarms']);
Route::get('/get-near-by-farms', [FarmController::class, 'getNearByFarms']);
