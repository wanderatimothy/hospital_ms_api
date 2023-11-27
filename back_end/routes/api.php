<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\InsuaranceProviderController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\VisitController;
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
    Route::name('user_types.')->prefix('user_types')
    ->controller(UserTypeController::class)
    ->group(function(){
        Route::get('/','index')->name('all');
        Route::post('/','store')->name('create');
        Route::post('/show','show')->name('show');
        Route::post('/update','update')->name('update');
        Route::post('/{userType}/delete','destroy')->name('delete');
    });
    Route::name('medications.')->prefix('medications')
    ->controller(MedicationController::class)->group(function(){
        Route::get('/','index')->name('all');
        Route::post('/','store')->name('create');
        Route::post('/show','show')->name('show');
        Route::post('/update','update')->name('update');
        Route::post('/{medication}/delete','destroy')->name('delete');
    });
    Route::name('branches.')->prefix('branches')
    ->controller(BranchController::class)
    ->group(function(){
        Route::get('/','index')->name('all');
        Route::post('/','store')->name('create');
        Route::post('/show','show')->name('show');
        Route::post('/update','update')->name('update');
        Route::post('/{branch}/delete','destroy')->name('delete');
    });

    Route::name('visits.')->prefix('visits')
    ->controller(VisitController::class)->group(function(){
        Route::get('/','index')->name('all');
        Route::post('/','store')->name('create');
        Route::post('/show','show')->name('show');
        Route::post('/update','update')->name('update');
        Route::post('/{visit}/delete','destroy')->name('delete');
    });
    Route::name('insuarance_providers.')->prefix('insuarance_providers')
    ->controller(InsuaranceProviderController::class)
    ->group(function(){
        Route::get('/','index')->name('all');
        Route::post('/','store')->name('create');
        Route::post('/show','show')->name('show');
        Route::post('/update','update')->name('update');
        Route::post('/{insuaranceProvider}/delete','destroy')->name('delete');
    });
    Route::name('patients.')->prefix('patients')
    ->controller(InsuaranceProviderController::class)
    ->group(function(){
        Route::get('/','index')->name('all');
        Route::post('/','store')->name('create');
        Route::post('/show','show')->name('show');
        Route::post('/update','update')->name('update');
        Route::post('/{patient}/delete','destroy')->name('delete');
    });
    Route::name('rooms.')->prefix('rooms')
    ->controller(RoomController::class)
    ->group(function(){
        Route::get('/','index')->name('all');
        Route::post('/','store')->name('create');
        Route::post('/show','show')->name('show');
        Route::post('/update','update')->name('update');
        Route::post('/{room}/delete','destroy')->name('delete');
    });
});

