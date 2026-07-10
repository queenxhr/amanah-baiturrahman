<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Wakif\WakifAuthController;
use App\Http\Controllers\Wakif\WakifDashboardController;
use App\Http\Controllers\Wakif\WakifTransaksiController;
use App\Http\Controllers\Wakif\WakifUserController;

Route::prefix('wakif')->middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
])->group(function () {
    Route::post('/login', [WakifAuthController::class, 'login']);
    Route::post('/signup', [WakifAuthController::class, 'register']);
    
    // Dashboard public
    Route::get('/counter', [WakifDashboardController::class, 'getCounters']);
    Route::get('/program-wakaf', [WakifDashboardController::class, 'getProgramWakafList']);
    Route::get('/program-wakaf/{id}', [WakifDashboardController::class, 'getProgramById']);
    Route::get('/program-wakaf/{id}/deskripsi', [WakifDashboardController::class, 'getProgramDeskripsi']);
    Route::get('/program-wakaf/{id}/countdown', [WakifDashboardController::class, 'getProgramCountdown']);
    Route::get('/counter-donatur', [WakifDashboardController::class, 'getCounterDonatur']);
    Route::get('/program-wakaf/{id}/donatur', [WakifDashboardController::class, 'getDonaturByProgram']);
    Route::get('/berita-laporan', [WakifDashboardController::class, 'getBeritaLaporan']);
    Route::get('/penyebaran-program', [WakifDashboardController::class, 'getPenyebaranProgram']);
    Route::get('/trend-wakaf', [WakifDashboardController::class, 'getTrendWakafPerTahun']);

    // Transaksi guest
    Route::post('/transaksi/guest', [WakifTransaksiController::class, 'createTransaksiGuest']);
    Route::patch('/transaksi/{id}/bukti', [WakifTransaksiController::class, 'uploadBuktiPembayaran']);

    Route::middleware([\App\Http\Middleware\CheckWakif::class])->group(function () {
        Route::put('/ubah-password', [WakifAuthController::class, 'updatePassword']);
        Route::post('/logout', [WakifAuthController::class, 'logout']);
        
        // Profile
        Route::get('/user', [WakifUserController::class, 'getProfile']);
        Route::put('/user', [WakifUserController::class, 'updateProfile']);
        
        // Transaksi user
        Route::post('/transaksi/user', [WakifTransaksiController::class, 'createTransaksiUser']);
        Route::get('/transaksi/riwayat', [WakifTransaksiController::class, 'getRiwayatTransaksi']);
        Route::get('/transaksi/{id}', [WakifTransaksiController::class, 'getTransaksiById']);
        Route::get('/detail-pembayaran/{id}', [WakifTransaksiController::class, 'getDetailPembayaran']);
    });
});

// Nazhir routes
use App\Http\Controllers\Nazhir\NazhirAuthController;
use App\Http\Controllers\Nazhir\NazhirDashboardController;
use App\Http\Controllers\Nazhir\NazhirTransaksiController;
use App\Http\Controllers\Nazhir\NazhirProgramController;
use App\Http\Controllers\Nazhir\NazhirLaporanController;
use App\Http\Controllers\Nazhir\NazhirUserController;
use App\Http\Controllers\Nazhir\NazhirPencairanController;

use App\Http\Controllers\Superadmin\SuperadminAuthController;
use App\Http\Controllers\Superadmin\SuperadminUserController;
use App\Http\Controllers\Superadmin\SuperadminProgramController;
use App\Http\Controllers\Superadmin\SuperadminPencairanController;

Route::prefix('nazhir')->middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
])->group(function () {
    Route::post('/login', [NazhirAuthController::class, 'login']);
    Route::post('/signup', [NazhirAuthController::class, 'register']);

    Route::middleware([\App\Http\Middleware\CheckNazhir::class])->group(function () {
        Route::post('/logout', [NazhirAuthController::class, 'logout']);

        // Users
        Route::get('/users', [NazhirUserController::class, 'getListUser']);

        // Dashboard
        Route::get('/counter', [NazhirDashboardController::class, 'getCounters']);
        Route::get('/penyebaran-program', [NazhirDashboardController::class, 'getPenyebaranProgram']);
        Route::get('/trend-wakaf', [NazhirDashboardController::class, 'getTrendWakafPerTahun']);

        // Transaksi wakif
        Route::get('/transaksi', [NazhirTransaksiController::class, 'getListTransaksi']);
        Route::get('/transaksi/export-csv', [NazhirTransaksiController::class, 'exportCsv']);
        Route::get('/transaksi/{id}/bukti', [NazhirTransaksiController::class, 'getBuktiPembayaran']);
        Route::put('/transaksi/{id}/approve', [NazhirTransaksiController::class, 'approve']);

        // Program
        Route::get('/program', [NazhirProgramController::class, 'getListProgram']);
        Route::post('/program', [NazhirProgramController::class, 'createProgram']);
        Route::put('/program/{id}', [NazhirProgramController::class, 'updateProgram']);
        Route::delete('/program/{id}', [NazhirProgramController::class, 'deleteProgram']);
        Route::post('/upload-image', [NazhirProgramController::class, 'uploadEditorImage']);

        // Laporan
        Route::get('/laporan/status/{programId}', [NazhirLaporanController::class, 'getStatusLaporan']);
        Route::get('/laporan/{programId}', [NazhirLaporanController::class, 'getLaporanByProgram']);
        Route::get('/laporan/detail/{id}', [NazhirLaporanController::class, 'getLaporanById']);
        Route::post('/laporan', [NazhirLaporanController::class, 'createLaporan']);
        Route::put('/laporan/{id}', [NazhirLaporanController::class, 'updateLaporan']);
        Route::delete('/laporan/{id}', [NazhirLaporanController::class, 'deleteLaporan']);

        // Pencairan Dana
        Route::get('/pencairan', [NazhirPencairanController::class, 'index']);
        Route::post('/pencairan', [NazhirPencairanController::class, 'store']);
    });
});

// Superadmin routes
Route::prefix('superadmin')->middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
])->group(function () {
    Route::post('/login', [SuperadminAuthController::class, 'login']);
    Route::post('/logout', [SuperadminAuthController::class, 'logout']);

    Route::middleware([\App\Http\Middleware\CheckSuperadmin::class])->group(function () {
        // Users Management
        Route::get('/users', [SuperadminUserController::class, 'index']);
        Route::put('/users/{id}/approve', [SuperadminUserController::class, 'approve']);
        Route::put('/users/{id}/reject', [SuperadminUserController::class, 'reject']);
        Route::put('/users/{id}/block', [SuperadminUserController::class, 'block']);
        Route::put('/users/{id}/unblock', [SuperadminUserController::class, 'unblock']);
        Route::delete('/users/{id}', [SuperadminUserController::class, 'destroy']);

        // Program Verification
        Route::get('/programs', [SuperadminProgramController::class, 'index']);
        Route::put('/programs/{id}/approve', [SuperadminProgramController::class, 'approve']);
        Route::put('/programs/{id}/reject', [SuperadminProgramController::class, 'reject']);

        // Disbursement Verification
        Route::get('/pencairan', [SuperadminPencairanController::class, 'index']);
        Route::put('/pencairan/{id}/approve', [SuperadminPencairanController::class, 'approve']);
        Route::put('/pencairan/{id}/reject', [SuperadminPencairanController::class, 'reject']);
        Route::get('/pencairan/{id}/download-surat', [SuperadminPencairanController::class, 'downloadSurat']);
        Route::post('/pencairan/{id}/upload-surat', [SuperadminPencairanController::class, 'uploadSurat']);
    });
});
