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

// Beranda
Route::get('/beranda', fn() => view('pages.beranda'));

// Riwayat
Route::get('/riwayat', fn() => view('pages.riwayat'));

// Manage Pegawai
Route::get('/pic/manage-pegawai', fn() => view('pic.managePegawai'));

// Tambah Pegawai
Route::get('/pic/tambah-pegawai', fn() => view('pic.tambahPegawai'));

// Edit Pegawai
Route::get('/pic/edit-pegawai', fn() => view('pic.editPegawai'));

//detail pegawai
Route::get('/pic/detail-pegawai', fn() => view('pic.detailPegawai'));

//pelaporan perjadin
Route::get('/pic/pelaporan-perjadin', fn() => view('pic.pelaporanPerjalanan'));

//penugasan perjadin
Route::get('/pic/penugasan-perjadin', fn() => view('pic.penugasan'));

//Laman Profile
Route::get('/laman-profile', fn() => view('pages.lamanprofile'));



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