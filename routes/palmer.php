<?php

use App\Http\Controllers\Palmer\AuthPalmerController;
use App\Http\Controllers\Palmer\PalmerRequest;
use App\Http\Controllers\Palmer\ProfilePalmerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
###################################### Authentication ##################################
Route::controller(AuthpalmerController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('logout', 'logout');
});
###################################### End Authentication ###############################

###################################### Profile Info #####################################
Route::controller(ProfilePalmerController::class)->group(function () {
    Route::get('profile', 'profileInfo');
    Route::post('update', 'updateProfile');
    Route::post('update/location', 'updateLocation');
});
###################################### End Profile Info ##################################

###################################### Request ########################################
Route::controller(PalmerRequest::class)->group(function () {
    Route::get('show/request', 'showRequest');
    Route::get('request/accept/{id}', 'acceptRequest');
    Route::post('cancelOrConfirm', 'confirmOrCancel');
});
###################################### End Requests #####################################
