<?php

use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\WalletController;
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
Route::prefix('v1')->group(function () {

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

    Route::prefix('articles')->group(function () {
        Route::get('/', [ArticleController::class, 'index']);
        Route::get('/feed', [ArticleController::class, 'feed']);
        Route::get('{slug}', [ArticleController::class, 'show']);
        Route::post('/', [ArticleController::class, 'store']);
        Route::put('{slug}', [ArticleController::class, 'update']);
        Route::delete('{slug}', [ArticleController::class, 'destroy']);
    });

    Route::prefix('wallet')->group(function () {

        Route::post('/increase-balance', [WalletController::class, 'increaseBalance']);
        Route::post('/transfer', [WalletController::class, 'transfer']);

    });
});
