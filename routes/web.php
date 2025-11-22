<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\PerjadinController;
use App\Http\Controllers\PimpinanController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\PPKController;


/*
|--------------------------------------------------------------------------
| Web Routes (Cleaned & Unified Beranda)
|--------------------------------------------------------------------------
*/

// Default: redirect ke login
Route::get('/', fn() => redirect()->route('login'));

// Auth (login / logout)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Password reset
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.store');

// Confirm password (protected)
Route::get('/confirm-password', [AuthController::class,'showConfirmForm'])->middleware('auth')->name('password.confirm');
Route::post('/confirm-password', [AuthController::class,'confirm'])->middleware('auth')->name('password.confirm.post');

/*
|--------------------------------------------------------------------------
| Routes untuk user yang sudah login
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Common dashboard (opsional)
    Route::view('/dashboard', 'pages.dashboard')->name('dashboard');

    Route::get('/beranda', [BerandaController::class, 'index'])->middleware('auth')->name('pages.beranda');

    // Profile & Bantuan
    Route::view('/profile', 'pages.lamanprofile')->name('profile');
    Route::view('/laman-bantuan', 'pages.lamanBantuan')->name('bantuan');

    // Riwayat 
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');

    // Perjalanan (Perjadin)
    Route::get('/perjalanan/{id}', [PerjadinController::class, 'show'])->name('perjalanan.detail');
    Route::post('/perjalanan/laporan/{id}', [PerjadinController::class, 'storeLaporan'])->name('perjalanan.storeLaporan');
    Route::post('/perjalanan/hadir/{id}', [PerjadinController::class, 'tandaiKehadiran'])->name('perjalanan.hadir');

    // Laporan Keuangan (group)
    Route::prefix('laporan-keuangan')->name('laporan.')->controller(LaporanKeuanganController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('role:PIC,PPK,PIMPINAN'); // daftar laporan
        Route::get('/export-excel', 'generateExcel')->name('export')->middleware('role:PIC,PPK,PIMPINAN'); // export
        Route::get('/{id}', 'show')->name('show'); // detail
        Route::get('/{id}/edit', 'edit')->name('edit')->middleware('role:PIC,PPK'); // edit form
        Route::put('/{id}', 'update')->name('update')->middleware('role:PIC,PPK'); // update
        Route::post('/{id}/verify', 'verify')->name('verify')->middleware('role:PPK'); // verifikasi oleh PPK
    });

    // Tambahan contoh route yang sering dipakai (uji coba)
    Route::view('/nyoba', 'nyoba')->name('nyoba');
});


// PIMPINAN
Route::middleware(['auth', 'role:PIMPINAN'])->prefix('pimpinan')->name('pimpinan.')->group(function () {
    // Beranda dihapus dari sini — global
    // Riwayat dihapus dari sini — global
    Route::get('/monitoring', [PimpinanController::class, 'index'])->name('monitoring');
    Route::get('/detail/{id}', [PimpinanController::class, 'detail'])->name('detail');
});

// PIC (Person In Charge)
Route::middleware(['auth','role:PIC'])->prefix('pic')->name('pic.')->group(function () {
    // Beranda dihapus dari sini — global
    // Riwayat dihapus dari sini — global
    Route::get('/penugasan-perjadin', [\App\Http\Controllers\PerjadinTambahController::class, 'create'])->name('penugasan');
    Route::post('/penugasan-perjadin', [\App\Http\Controllers\PerjadinTambahController::class, 'store'])->name('penugasan.store');
    Route::get('/pelaporan-perjadin', fn() => view('pic.pelaporanPerjalanan'))->name('pelaporan');
    Route::get('/lsrampung', fn() => view('pic.lsrampung'))->name('lsrampung');


    // Pegawai management (list, tambah, edit, detail)
    Route::get('/pegawai', fn() => view('pic.managePegawai'))->name('pegawai.index');
    Route::get('/pegawai/tambah', fn() => view('pic.tambahPegawai'))->name('pegawai.create');
    Route::get('/pegawai/{id}/edit', fn() => view('pic.editPegawai'))->name('pegawai.edit');
    Route::get('/pegawai/{id}', fn() => view('pic.detailPegawai'))->name('pegawai.show');
});

// PPK
Route::middleware(['auth','role:PPK'])->prefix('ppk')->name('ppk.')->group(function () {
    // Beranda dihapus dari sini — global
    // Riwayat dihapus dari sini — global
    Route::get('/pelaporan', fn() => view('ppk.pelaporan'))->name('pelaporan');
    Route::get('/pelaporan/{id}', [PPKController::class, 'detailPelaporan'])->name('detailPelaporan');
    Route::get('/tabelrekap', fn() => view('ppk.tabelRekap'))->name('tabelrekap');
});

// PEGAWAI
// Route::middleware(['auth','role:PEGAWAI'])->prefix('pegawai')->name('pegawai.')->group(function () {
//     // Beranda dihapus dari sini — global
//     // Riwayat dihapus dari sini — global
// });