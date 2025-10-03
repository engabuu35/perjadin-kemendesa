<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;

// Route::get('/', function () {
//     return view('beranda');
// });

// Route untuk menampilkan halaman utama
Route::get('/', [LocationController::class, 'index']);

// Route untuk menerima data lokasi dari frontend
Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
