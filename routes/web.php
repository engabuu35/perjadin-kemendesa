<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LaporanKeuanganController;
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
Route::get('/pages/manage-pegawai', fn() => view('pages.managePegawai'));

// Tambah Pegawai
Route::get('/pages/tambah-pegawai', fn() => view('pages.tambahPegawai'));

// Edit Pegawai
Route::get('/pages/edit-pegawai', fn() => view('pages.editPegawai'));