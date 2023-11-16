<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\UserTypeController;
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

Route::controller(AuthController::class)
->prefix("auth")
->name('auth.')->group(function(){
    Route::post('/login' , 'login')->name('login');
    Route::post('/logout' , 'logout')->name('login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::name('user_types.')->prefix('user_types')->controller(UserTypeController::class)->group(function(){
        Route::get('/','index')->name('all');
        Route::post('/','store')->name('create');
        Route::post('/show','show')->name('show');
        Route::post('/update','update')->name('update');
        Route::post('/{userType}/delete','destroy')->name('delete');
    });
    Route::name('medications.')->prefix('medications')->controller(MedicationController::class)->group(function(){
        Route::get('/','index')->name('all');
        Route::post('/','store')->name('create');
        Route::post('/show','show')->name('show');
        Route::post('/update','update')->name('update');
        Route::post('/{medication}/delete','destroy')->name('delete');
    });
});

