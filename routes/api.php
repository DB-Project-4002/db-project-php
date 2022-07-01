<?php

use App\Http\Controllers\LootController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\StickerController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', [StatusController::class, 'index']);

Route::prefix('user/{user_id}/game')->middleware('token-auth')->name('user.game.')->group(function () {
    Route::prefix('loots')->name('loots')->group(function () {
        Route::get('/', [LootController::class, 'index']);
        Route::post('{loot_name}', [LootController::class, 'store']);
    });

    Route::prefix('stickers')->name('stickers')->group(function () {
        Route::get('/', [StickerController::class, 'index']);
        Route::post('{sticker_name}', [StickerController::class, 'store']);
    });

    Route::prefix('seasons')->name('seasons')->group(function () {
        Route::get('/', [SeasonController::class, 'index']);
        Route::get('{season_num}', [SeasonController::class, 'show']);
    });

    Route::prefix('matches')->name('matches')->group(function () {
        Route::get('/', [MatchController::class, 'index']);
        Route::get('{match_time}', [MatchController::class, 'show']);
    });
});
