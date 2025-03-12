<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DeliveryOptionController;
use App\Http\Controllers\Admin\FarmController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Auth\LoginController;
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
    return redirect('/login');
});

Route::view('/dashboard', 'admin.dashboard');

Route::view('/test/index', 'admin.test.index');
Route::view('/test/create', 'admin.test.create');

Route::middleware(['auth','admin'])->group(function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::controller(CategoryController::class)->prefix('admin/category')->name('admin.category.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{category}', 'edit')->name('edit');
        Route::post('/update/{category}', 'update')->name('update');
        Route::get('/destroy/{category}', 'delete')->name('delete');
    });
    
    Route::controller(PaymentController::class)->prefix('admin/payment')->name('admin.payment.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{payment}', 'edit')->name('edit');
        Route::post('/update/{payment}', 'update')->name('update');
        Route::get('/destroy/{payment}', 'delete')->name('delete');
    });
    
    Route::controller(UserController::class)->prefix('admin/user')->name('admin.user.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{user}', 'edit')->name('edit');
        Route::post('/update/{user}', 'update')->name('update');
        Route::get('/destroy/{user}', 'delete')->name('delete');
    });
    
    Route::controller(FarmController::class)->prefix('admin/farm')->name('admin.farm.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{farm}', 'edit')->name('edit');
        Route::post('/update/{farm}', 'update')->name('update');
        Route::get('/destroy/{farm}', 'delete')->name('delete');
    });

    Route::controller(DeliveryOptionController::class)->prefix('admin/delivery-option')->name('admin.delivery-option.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{deliveryOption}', 'edit')->name('edit');
        Route::post('/update/{deliveryOption}', 'update')->name('update');
        Route::get('/destroy/{deliveryOption}', 'delete')->name('delete');
    });
    
    Route::controller(ServiceController::class)->prefix('admin/service')->name('admin.service.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{service}', 'edit')->name('edit');
        Route::post('/update/{service}', 'update')->name('update');
        Route::get('/destroy/{service}', 'delete')->name('delete');
    });
});

Route::view('/test','test');
Route::get('/test-not', [AuthController::class, 'showForm'])->name('firebase.showForm');
Route::post('/test-not', [AuthController::class, 'sendNotification'])->name('firebase.sendNotification');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
