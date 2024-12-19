<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::view('/test','test');
Route::get('/test-not', [AuthController::class, 'showForm'])->name('firebase.showForm');
Route::post('/test-not', [AuthController::class, 'sendNotification'])->name('firebase.sendNotification');