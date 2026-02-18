<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DesignerController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\HairStyleController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\TimeOffController;
use App\Http\Controllers\Api\SalonController;
use App\Http\Controllers\Api\ServiceController;

Route::get('/service', [ServiceController::class, 'index']);




// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
