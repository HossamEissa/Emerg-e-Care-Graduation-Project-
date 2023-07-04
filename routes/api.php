<?php


use App\Http\Controllers\User\AuthUserController;
use App\Http\Controllers\User\FamilyController;
use App\Http\Controllers\User\NeedPalmerController;
use App\Http\Controllers\User\ProfileController;
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

Route::controller(AuthUserController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('logout', 'logout');
});

###################################### End Authentication ###############################

###################################### Profile Info #####################################

Route::controller(ProfileController::class)->group(function () {
    Route::get('profile', 'profileInfo');
    Route::post('update', 'updateProfile');
    Route::post('update/location', 'updateLocation');
});

###################################### End Profile Info #################################

###################################### Family ###########################################
Route::controller(FamilyController::class)->group(function () {
    Route::post('add/member', 'add_member');
    Route::get('show/member', 'show_members');
    Route::get('delete/member/{id}', 'delete_member');
});
###################################### End Family #######################################

###################################### Need Help ########################################
Route::controller(NeedPalmerController::class)->group(function () {
    Route::get('request', 'needHelp');
    Route::get('check/accept/{id}', 'checkAccept');
    Route::post('cancelOrConfirm', 'confirmOrCancel');
});
###################################### End Need Help #####################################
