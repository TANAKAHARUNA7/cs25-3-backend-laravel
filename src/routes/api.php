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

/*
* Auth Routing (認証不要)
*/
// ログイン
Route::post('login',[AuthController::class, 'login']); 

// 会員登録
Route::post('register',[AuthController::class, 'register']); 

// 認証必要
Route::middleware('auth:sanctum')->group(function () {
    
    // ログアウト
    Route::post('logout', [AuthController::class, 'logout']);

    // ログインユーザ情報摂取
    Route::get('me', [AuthController::class, 'me']);
});


/**
 * Reservation Routing (認証必要、Client＆Designerのみ)
 */
Route::middleware(['auth:sanctum', 'role:client'])->group(function(){

    // 予約履歴照会 : client
    Route::get('reservation/client', [ReservationController::class, 'clientToIndex']);
    
    // 予約作成
    Route::post('reservation', [ReservationController::class, 'store']);
    
});

Route::middleware(['auth:sanctum', 'role:designer'])->group(function(){

    // 予約履歴照会 : designer
    Route::get('reservation/designer', [ReservationController::class, 'designerToIndex']);
    
});







/*
* Service Routing (認証不要)
*/
// Service照会
Route::get('services', [ServiceController::class, 'index']); 

// 特定のService照会
Route::get('services/{id}', [ServiceController::class, 'show']);   

// 認証 + role確認必要(managerだけが可能)
Route::middleware(['auth:sanctum', 'role:manager'])->group(function(){
    
    // Service新規作成
    Route::post('services', [ServiceController::class, 'store']);
    
    // 修正
    Route::put('services/{id}', [ServiceController::class, 'update']);
    
    // 削除
    Route::delete('services/{id}', [ServiceController::class, 'destroy']);
});

/**
 * TimeOff Routing (認証不要)
 */
// 全てのDesignerの休日を照会
Route::get('timeoffs', [TimeOffController::class, 'index']);

// 特定の休日を照会
Route::get('timeoffs/{id}', [TimeOffController::class, 'show']);

// 特定のdesignerの休日照会
Route::get('timeoffs/designer/{designer_id}', [TimeOffController::class, 'designer']);

// 認証 + role確認必要(managerだけが可能)
Route::middleware(['auth:sanctum', 'role:manager'])->group(function(){
    
    // Designerの休日を新規作成
    Route::post('timeoffs', [TimeOffController::class, 'store']);

    // 削除
    Route::delete('timeoffs/{timeOff}', [TimeOffController::class, 'destroy']);
});





// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
