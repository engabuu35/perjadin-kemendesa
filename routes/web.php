<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LaporanKeuanganController;

// Route::get('/', function () {
//     return view('beranda');
// });

// Route untuk menampilkan halaman utama
// Route::get('/', [LocationController::class, 'index']);

// Route untuk menerima data lokasi dari frontend
// Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');

Route::get('/laporan', [LaporanKeuanganController::class, 'index'])->name('laporan.index');

// Rute ini akan menangani permintaan untuk membuat file PDF
// Route::get('/laporan/pdf', [LaporanKeuanganController::class, 'generatePDF'])->name('laporan.pdf');

Route::get('/laporan/excel', [LaporanKeuanganController::class, 'generateExcel'])->name('laporan.excel');