<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\InsuaranceProviderController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\WardController;
use App\Models\DocumentType;
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
    Route::post('/logout' , 'logout')->name('logout');
    Route::post('/switch_active_branch' , 'switch_active_branch')->name('switch_active_branch');
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
        Route::post('/get_branches_user_can_access','get_branches_user_can_access')->name('user_can_access');
        Route::post('/add_user_to_branch','add_user_to_branch')->name('add_user_to_branch');
        Route::post('/remove_user_from_branch','remove_user_from_branch')->name('remove_user_from_branch');
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
    ->controller(PatientController::class)
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
    Route::name('beds.')->prefix('beds')
    ->controller(BedController::class)
    ->group(function(){
        Route::get('/','index')->name('all');
        Route::post('/','store')->name('create');
        Route::post('/show','show')->name('show');
        Route::post('/update','update')->name('update');
        Route::post('/{bed}/delete','destroy')->name('delete');
    });
    Route::name('wards.')->prefix('wards')
    ->controller(WardController::class)
    ->group(function(){
        Route::get('/','index')->name('all');
        Route::post('/','store')->name('create');
        Route::post('/show','show')->name('show');
        Route::post('/update','update')->name('update');
        Route::post('/{ward}/delete','destroy')->name('delete');
    });
    Route::name('document_types.')->prefix('document_types')
    ->controller(DocumentType::class)
    ->group(function(){
        Route::get('/','index')->name('all');
        Route::post('/','store')->name('create');
        Route::post('/show','show')->name('show');
        Route::post('/update','update')->name('update');
        Route::post('/{document_type}/delete','destroy')->name('delete');
    });
    Route::name('document.')->prefix('document')
    ->controller(DocumentType::class)
    ->group(function(){
        Route::get('/','index')->name('all');
        Route::post('/','store')->name('create');
        Route::post('/show','show')->name('show');
        Route::post('/update','update')->name('update');
        Route::post('/{document}/delete','destroy')->name('delete');
    });
});

