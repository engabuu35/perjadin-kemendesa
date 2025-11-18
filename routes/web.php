<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\PerjadinController;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\PimpinanController;

// Halaman utama geotagging
// Route::get('/', [LocationController::class, 'index']);

// Menyimpan lokasi dari frontend
// Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.store');

Route::get('/confirm-password', [AuthController::class,'showConfirmForm'])->middleware('auth')->name('password.confirm');
Route::post('/confirm-password', [AuthController::class,'confirm'])->middleware('auth');

// Halaman PIC
Route::middleware(['auth', 'role:PIC'])->group(function () {
    Route::get('/pic/beranda', fn() => view('pic.beranda'))->name('pic.beranda');
    Route::get('/pic/penugasan-perjadin', fn() => view('pic.penugasan'))->name('pic.penugasan'); 
    Route::get('/pic/tambah-pegawai', fn() => view('pic.tambahPegawai'))->name('pic.tambahPegawai');
    Route::get('/pic/manage-pegawai', fn() => view('pic.managePegawai'))->name('pic.managePegawai');
    Route::get('/pic/edit-pegawai', fn() => view('pic.editPegawai'))->name('pic.editPegawai');
    Route::get('/pic/detail-pegawai', fn() => view('pic.detailPegawai'))->name('pic.detailPegawai');
    Route::get('/pic/pelaporan-perjadin', fn() => view('pic.pelaporanPerjalanan'))->name('pic.pelaporanPerjalanan');
});

// Halaman PIMPINAN
Route::middleware(['auth', 'role:PIMPINAN'])->group(function () {
    // Dashboard/Monitoring Pimpinan - path: /pimpinan/monitoring
    Route::get('/pimpinan/monitoring', [App\Http\Controllers\PimpinanController::class, 'index'])->name('pimpinan.monitoring');
    
    // Detail Perjalanan Dinas
    Route::get('/pimpinan/detail/{id}', [App\Http\Controllers\PimpinanController::class, 'detail'])->name('pimpinan.detail');
});

// Halaman PIMPINAN (juga bisa akses halaman PEGAWAI)
Route::middleware(['auth', 'role:PIMPINAN'])->group(function () {
    Route::get('/pimpinan/beranda', fn() => view('pimpinan.beranda'))->name('pimpinan.beranda');
});

// Halaman PPK (juga bisa akses halaman PEGAWAI)
Route::middleware(['auth', 'role:PPK'])->group(function () {
    Route::get('/ppk/beranda', fn() => view('ppk.beranda'))->name('ppk.beranda');
});

// Halaman PEGAWAI
Route::middleware(['auth', 'role:PEGAWAI'])->group(function () {
    Route::get('/beranda', fn() => view('pages.beranda'))->name('pegawai.beranda');
});

Route::prefix('laporan-keuangan')->name('laporan.')
        ->controller(LaporanKeuanganController::class)
        ->group(function () {

            // Menampilkan daftar laporan keuangan
            // GET /laporan-keuangan
            Route::get('/', 'index')
                ->name('index')
                ->middleware('role:PIC,PPK,PIMPINAN'); // Hanya PIC, PPK, Pimpinan yang bisa lihat daftar

            // Mengunduh laporan Excel
            // GET /laporan-keuangan/export-excel
            Route::get('/export-excel', 'generateExcel')
                ->name('export')
                ->middleware('role:PIC,PPK,PIMPINAN'); // Hanya PIC, PPK, Pimpinan yang bisa ekspor

            // Menampilkan detail satu laporan keuangan
            // GET /laporan-keuangan/1
            Route::get('/{id}', 'show')
                ->name('show'); // Diasumsikan semua peran (termasuk Pegawai ybs) bisa melihat detail

            // Menampilkan form untuk mengedit rincian (SPM, SP2D, dll)
            // GET /laporan-keuangan/1/edit
            Route::get('/{id}/edit', 'edit')
                ->name('edit')
                ->middleware('role:PIC,PPK'); // Hanya PIC atau PPK yang bisa mengedit

            // Menyimpan perubahan dari form edit
            // PUT /laporan-keuangan/1
            Route::put('/{id}', 'update')
                ->name('update')
                ->middleware('role:PIC,PPK'); // Hanya PIC atau PPK yang bisa update

            // Memproses verifikasi (Setuju/Tolak) oleh PPK
            // POST /laporan-keuangan/1/verify
            Route::post('/{id}/verify', 'verify')
                ->name('verify')
                ->middleware('role:PPK'); // Hanya PPK yang bisa verifikasi
    });

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

//Laman Bantuan
Route::get('/laman-bantuan', fn() => view('pages.lamanBantuan'));

Route::get('/pimpinan/beranda', fn() => view('pimpinan.beranda'))->name('pimpinan.beranda');
Route::get('/ppk/beranda', fn() => view('ppk.beranda'))->name('ppk.beranda');
Route::get('/pic/beranda', fn() => view('pic.beranda'))->name('pic.beranda');

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