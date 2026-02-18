<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\UserController;
use App\Http\Controllers\DesignerController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\HairStyleController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TimeOffController;
use App\Http\Controllers\SalonController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AuthController;


// 認証不要
Route::post('login',[AuthController::class, 'login']); // ログイン
Route::post('register',[AuthController::class, 'register']); // 会員登録

// 認証必要
Route::middleware('auth:sanctum')->group(function () {
    // ログアウト
    Route::post('logout', [AuthController::class, 'logout']);

    // ログインユーザ情報摂取
    Route::get('me', [AuthController::class, 'me']);

});

// サービスRouting
// 認証不要
Route::get(['services', ServiceController::class, 'index']);




// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
