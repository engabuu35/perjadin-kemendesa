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
use App\Http\Controllers\PelaporanController;
use App\Http\Controllers\ManagePegawaiController;
use App\Http\Controllers\ProfileController;

// hapus klo ga kepake ini untuk testing aja
Route::get('/preview-email-tailwind', function () {
    $perjalanan = \App\Models\PerjalananDinas::first();

    return view('emails.perjadin-email', compact('perjalanan'));
});


/*
|--------------------------------------------------------------------------
| Web Routes (FULL)
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
    // Common dashboard
    Route::view('/dashboard', 'pages.dashboard')->name('dashboard');
    Route::get('/beranda', [BerandaController::class, 'index'])->middleware('auth')->name('pages.beranda');

    // Profile & Bantuan
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::view('/laman-bantuan', 'pages.lamanBantuan')->name('bantuan');

    // Riwayat 
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');

    // =================================================================
    // ROUTE PERJALANAN DINAS & LAPORAN (PEGAWAI)
    // =================================================================
    Route::prefix('perjalanan')->name('perjalanan.')->group(function () {
        Route::get('/{id}', [PerjadinController::class, 'show'])->name('detail');
        Route::post('/{id}/hadir', [PerjadinController::class, 'tandaiKehadiran'])->name('hadir');
        Route::post('/{id}/uraian', [PerjadinController::class, 'storeUraian'])->name('storeUraian');
        Route::post('/{id}/bukti', [PerjadinController::class, 'storeBukti'])->name('storeBukti');
        Route::post('/{id}/foto-geotag', [PerjadinController::class, 'storeFotoGeotagging'])->name('fotoGeotag');
        
        // Route Selesaikan (Finalisasi)
        Route::post('/{id}/selesaikan', [PerjadinController::class, 'selesaikanTugasSaya'])->name('selesaikan');
    });

    Route::get('/bukti/delete/{id}', [PerjadinController::class, 'deleteBukti'])->name('bukti.delete');
    // =================================================================

    // Laporan Keuangan Lama (Jika masih dipakai)
    Route::prefix('laporan-keuangan')->name('laporan.')->controller(LaporanKeuanganController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('role:PIC,PPK,PIMPINAN'); 
        Route::get('/export-excel', 'generateExcel')->name('export')->middleware('role:PIC,PPK,PIMPINAN'); 
        Route::get('/{id}', 'show')->name('show'); 
        Route::get('/{id}/edit', 'edit')->name('edit')->middleware('role:PIC,PPK'); 
        Route::put('/{id}', 'update')->name('update')->middleware('role:PIC,PPK'); 
        Route::post('/{id}/verify', 'verify')->name('verify')->middleware('role:PPK'); 
    });

    Route::view('/nyoba', 'nyoba')->name('nyoba');
});


// PIMPINAN
Route::middleware(['auth', 'role:PIMPINAN'])->prefix('pimpinan')->name('pimpinan.')->group(function () {
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::get('/monitoring', [PimpinanController::class, 'index'])->name('monitoring');
    Route::get('/detail/{id}', [PimpinanController::class, 'detail'])->name('detail');
});

// PIC (Person In Charge)
Route::middleware(['auth','role:PIC'])->prefix('pic')->name('pic.')->group(function () {
    Route::get('/manajemen-perjadin', [\App\Http\Controllers\PerjadinController::class, 'index'])->name('penugasan');
    Route::get('/penugasan-perjadin/create', [\App\Http\Controllers\PerjadinTambahController::class, 'create'])->name('penugasan.create');
    Route::post('/penugasan-perjadin', [\App\Http\Controllers\PerjadinTambahController::class, 'store'])->name('penugasan.store');
    Route::get('/penugasan-perjadin/{id}/edit', [\App\Http\Controllers\PerjadinTambahController::class, 'edit'])->name('penugasan.edit');
    Route::patch('/penugasan-perjadin/{id}', [\App\Http\Controllers\PerjadinTambahController::class, 'update'])->name('penugasan.update');
    Route::patch('/penugasan-perjadin/{id}/status', [\App\Http\Controllers\PerjadinTambahController::class, 'updateStatus'])->name('penugasan.updateStatus');
    Route::get('/pelaporan-perjadin', fn() => view('pic.pelaporanPerjalanan'))->name('pelaporan');
    Route::get('/lsrampung', [\App\Http\Controllers\LSRampungController::class, 'index'])->name('lsrampung');
    

    // PELAPORAN KEUANGAN PIC (MANUAL)
    Route::get('/pelaporan-keuangan', [PelaporanController::class, 'index'])->name('pelaporan.index');
    Route::get('/pelaporan-keuangan/{id}', [PelaporanController::class, 'show'])->name('pelaporan.detail');

    // Route untuk menyimpan data keuangan manual oleh PIC
    Route::post('penugasan-perjadin/{id}/simpan-manual', [App\Http\Controllers\PerjadinTambahController::class, 'simpanKeuanganManual'])->name('penugasan.simpanManual');

    // View routes (server-rendered)
    Route::get('/pegawai', [\App\Http\Controllers\ManagePegawaiController::class, 'index'])->name('pegawai.index');
    Route::get('/pegawai/tambah', [\App\Http\Controllers\ManagePegawaiController::class, 'create'])->name('pegawai.create');
    Route::post('/pegawai', [\App\Http\Controllers\ManagePegawaiController::class, 'store'])->name('pegawai.store');
    Route::get('/pegawai/{nip}/edit', [\App\Http\Controllers\ManagePegawaiController::class, 'edit'])->name('pegawai.edit');
    Route::patch('/pegawai/{nip}', [\App\Http\Controllers\ManagePegawaiController::class, 'update'])->name('pegawai.update');
    Route::delete('/pegawai/{nip}', [\App\Http\Controllers\ManagePegawaiController::class, 'destroy'])->name('pegawai.destroy');

    // Bulk delete (form submit)
    Route::post('/pegawai/bulk-delete', [\App\Http\Controllers\ManagePegawaiController::class, 'bulkDelete'])->name('pegawai.bulkDelete');
    // [ROUTE BARU] Aksi Simpan & Hapus Bukti oleh PIC
    Route::post('/pelaporan-keuangan/{id}/store', [PelaporanController::class, 'storeBukti'])->name('pelaporan.storeBukti');
    Route::get('/pelaporan-keuangan/delete/{id}', [PelaporanController::class, 'deleteBukti'])->name('pelaporan.deleteBukti');

    Route::post('/pelaporan-keuangan/{id}/submit', [PelaporanController::class, 'submitToPPK'])->name('pelaporan.submit');

    Route::post('/pelaporan-keuangan/{id}/store-bulk', [App\Http\Controllers\PelaporanController::class, 'storeBulk'])->name('pelaporan.storeBulk');
});

// PPK
Route::middleware(['auth','role:PPK'])->prefix('ppk')->name('ppk.')->group(function () {

    // VERIFIKASI & INPUT SPM/SP2D
    Route::get('/verifikasi', [PPKController::class, 'index'])->name('verifikasi.index');
    Route::get('/verifikasi/{id}', [PPKController::class, 'show'])->name('verifikasi.detail');
    Route::post('/verifikasi/{id}/store', [PPKController::class, 'storeVerifikasi'])->name('verifikasi.store');
    Route::post('/verifikasi/{id}/tolak', [PPKController::class, 'tolakVerifikasi'])->name('verifikasi.tolak');
    Route::post('/verifikasi/{id}/spm', [PPKController::class, 'inputSPM'])->name('verifikasi.inputSPM');
    Route::post('/verifikasi/{id}/sp2d', [PPKController::class, 'inputSP2D'])->name('verifikasi.inputSP2D');
    Route::post('/verifikasi/{id}/revisi', [PPKController::class, 'mintaRevisi'])->name('verifikasi.revisi');
    Route::get('/verifikasi/{id}/cetak', [PPKController::class, 'cetakLaporan'])->name('verifikasi.cetak');
    Route::get('/verifikasi/{id}/export-excel', [PPKController::class, 'exportLaporanExcel'])->name('verifikasi.exportExcel');
    Route::get('/verifikasi/{id}/export-pdf', [PPKController::class, 'exportLaporanPDF'])->name('verifikasi.exportPDF');
    Route::get('/verifikasi/{id}/rekapitulasi', [PPKController::class, 'rekapitulasiLaporan'])->name('verifikasi.rekapitulasi');
    Route::post('/verifikasi/{id}/rekapitulasi/store', [PPKController::class, 'storeRekapitulasi'])->name('verifikasi.rekapitulasi.store');
    // [ROUTE BARU - DITAMBAHKAN] Approve & Reject
    Route::post('/verifikasi/{id}/approve', [PPKController::class, 'approve'])->name('verifikasi.approve');
    Route::post('/verifikasi/{id}/reject', [PPKController::class, 'reject'])->name('verifikasi.reject');
    
    Route::get('/pelaporan', [PPKController::class, 'index'])->name('pelaporan');
    Route::get('/pelaporan/{id}', [PPKController::class, 'detailPelaporan'])->name('detailPelaporan');
    Route::get('/tabelrekap', [PPKController::class, 'tabelRekap'])->name('tabelrekap');
    Route::get('/tabelrekap/export', [PPKController::class, 'exportRekap'])->name('tabelrekap.export');

});


