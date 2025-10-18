<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResumeSuratController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PengajuanSuratController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\SuratBebasNarkobaController;
use App\Http\Controllers\SuratKesehatanController;
use App\Http\Controllers\SuratKeteranganKejiwaanController;

// ========== HALAMAN UTAMA ==========
Route::get('/', [PengajuanSuratController::class, 'daftarDokter'])->name('home');
Route::get('/status-pembayaran', [PengajuanSuratController::class, 'statusPembayaran'])
    ->name('status.pembayaran');

// ========== AJAX WILAYAH ==========
Route::prefix('wilayah')->group(function () {
    Route::get('/provinsi', [WilayahController::class, 'provinsi'])->name('wilayah.provinsi');
    Route::get('/kabupaten/{provinsi_id}', [WilayahController::class, 'kabupaten'])->name('wilayah.kabupaten');
    Route::get('/kecamatan/{kabkot_id}', [WilayahController::class, 'kecamatan'])->name('wilayah.kecamatan');
    Route::get('/kelurahan/{kecamatan_id}', [WilayahController::class, 'kelurahan'])->name('wilayah.kelurahan');
});

// ========== PENGAJUAN SURAT ==========
Route::prefix('pengajuan')->group(function () {
    Route::get('/', [PengajuanSuratController::class, 'index'])->name('pengajuan.index');
    Route::post('/store', [PengajuanSuratController::class, 'store'])->name('ajukan.surat');
});

// ========== AUTH DAN DASHBOARD ==========
Route::middleware(['auth'])->group(function () {

    // DASHBOARD
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // ✅ Data Pengajuan Surat
    Route::get('/admin/data-pengajuan', [AdminDashboardController::class, 'dataPengajuan'])->name('admin.data-pengajuan');

    // ========== RESUME SURAT (ADMIN) ==========
    Route::prefix('admin/resume')->group(function () {
        Route::get('/', [ResumeSuratController::class, 'index'])->name('resume.index');
        Route::get('/{nik}', [ResumeSuratController::class, 'show'])->name('resume.show');
        Route::get('/generate-pdf/{jenis}/{id}', [ResumeSuratController::class, 'generatePdf'])->name('resume.generate-pdf');
        Route::post('/send-wa/{id}', [ResumeSuratController::class, 'sendSuratViaWA'])->name('resume.send-wa');
    });

    // ========== SURAT KHUSUS ==========
    Route::prefix('surat')->group(function () {
        // Kejiwaan
        Route::get('/kejiwaan/create/{id}', [SuratKeteranganKejiwaanController::class, 'create'])->name('surat-jiwa.create');
        Route::get('/kejiwaan/preview/{id}', [SuratKeteranganKejiwaanController::class, 'preview'])->name('surat-kejiwaan.preview');
        Route::post('/kejiwaan/store', [SuratKeteranganKejiwaanController::class, 'store'])->name('surat-jiwa.store');

        // Keterangan Sehat
        Route::get('/kesehatan/create/{id}', [SuratKesehatanController::class, 'create'])->name('surat-kesehatan.create');
        Route::get('/kesehatan/preview/{id}', [SuratKesehatanController::class, 'preview'])->name('surat-kesehatan.preview');
         Route::post('/kesehatan/store', [SuratKesehatanController::class, 'store'])->name('surat-kesehatan.store');

        // Bebas Narkoba
        Route::get('/narkoba/create/{id}', [SuratBebasNarkobaController::class, 'create'])->name('surat-narkoba.create');
        Route::get('/narkoba/preview/{id}', [SuratBebasNarkobaController::class, 'preview'])->name('surat-bebas-narkoba.preview');
        Route::post('/narkoba/store', [SuratBebasNarkobaController::class, 'store'])->name('surat-narkoba.store');
    });
});

require __DIR__.'/auth.php';