<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProfileController;

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

Route::controller(UserController::class)->group(function () {

    Route::prefix('users')->group(function () {
        Route::post('/', 'register');
        Route::post('/login', 'login');
    });

    Route::prefix('user')->group(function () {
        Route::get('/', 'getCurrentUser');
        Route::put('/', 'updateCurrentUser');
    });

});

Route::controller(ProfileController::class)->group(function () {

    Route::prefix('profiles/{username}')->group(function () {
        Route::get('/', 'getProfile');
        Route::post('/follow', 'followUser');
        Route::delete('/follow', 'unfollowUser');
    });

});
