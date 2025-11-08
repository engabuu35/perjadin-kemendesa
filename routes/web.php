<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\PerjadinController;
use Maatwebsite\Excel\Facades\Excel;

// Halaman utama geotagging
// Route::get('/', [LocationController::class, 'index']);

// Menyimpan lokasi dari frontend
// Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');

// Halaman laporan
Route::get('/laporan', [LaporanKeuanganController::class, 'index'])->name('laporan.index');

// Generate Excel
Route::get('/laporan/excel', [LaporanKeuanganController::class, 'generateExcel'])->name('laporan.excel');

// Halaman tambahan (opsional)
Route::view('/nyoba', 'nyoba')->name('nyoba');

// Dashboard
Route::view('/dashboard', 'pages.dashboard')->name('dashboard');

// Manage Pegawai
Route::get('/ppk/manage-pegawai', fn() => view('ppk.managePegawai'));

// Tambah Pegawai
Route::get('/ppk/tambah-pegawai', fn() => view('ppk.tambahPegawai'));

// Edit Pegawai
Route::get('/ppk/edit-pegawai', fn() => view('ppk.editPegawai'));

// Rute untuk MENAMPILKAN halaman detail perjalanan
    // URL-nya akan menjadi: /perjalanan/1 (contoh jika id-nya 1)
    Route::get('/perjalanan/{id}', [PerjadinController::class, 'show'])
         ->name('perjalanan.detail');

    // Rute untuk MEMPROSES form "Kirim" (Uraian & Biaya)
    Route::post('/perjalanan/laporan/{id}', [PerjadinController::class, 'storeLaporan'])
         ->name('perjalanan.storeLaporan');
    
    // Rute untuk Tombol "Tandai Kehadiran" (via AJAX/JavaScript)
    Route::post('/perjalanan/hadir/{id}', [PerjadinController::class, 'tandaiKehadiran'])
         ->name('perjalanan.hadir');