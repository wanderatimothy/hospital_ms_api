<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::name('user_types.')->prefix('user_types')->controller(UserTypeController::class)->group(function(){
    Route::get('/','index')->name('all');
    Route::post('/','store')->name('show');
    Route::post('/show','show')->name('show');
    Route::post('/update','update')->name('update');
    Route::post('/{userType}/delete','destroy')->name('delete');
});