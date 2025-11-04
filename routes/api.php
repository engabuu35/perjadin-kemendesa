<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController; 
use App\Http\Controllers\Api\TripController;

// Endpoint untuk login
Route::post('/login', [AuthController::class, 'login']);

// Endpoint user (contoh route terproteksi)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Route untuk membuat trip baru
    Route::post('/trips', [TripController::class, 'store']);
    
    // Route untuk Check-in
    Route::post('/checkins', [CheckinController::class, 'store']);
});