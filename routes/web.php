<?php

use App\Http\Controllers\LootController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\StickerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


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
