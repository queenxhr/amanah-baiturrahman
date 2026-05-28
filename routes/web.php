<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Wakif/Home')->name('home');
Route::inertia('/login', 'Wakif/Auth/Login')->name('login');
Route::inertia('/register', 'Wakif/Auth/Register')->name('register');
Route::inertia('/program', 'Wakif/Program')->name('program.list');
Route::get('/program/{id}', function ($id) {
    return inertia('Wakif/ProgramDetail', [
        'id' => $id
    ]);
})->name('program.detail');
Route::inertia('/laporan', 'Wakif/Laporan')->name('laporan');
Route::inertia('/tentang', 'Wakif/TentangKami')->name('tentang');
Route::inertia('/profil', 'Wakif/Profile')->name('profile');
Route::inertia('/riwayat-transaksi', 'Wakif/RiwayatTransaksi')->name('transaksi.riwayat');
Route::get('/transaksi/{id}', function ($id) {
    return inertia('Wakif/DetailTransaksi', [
        'id' => $id
    ]);
})->name('transaksi.detail');

Route::middleware([\App\Http\Middleware\CheckNazhir::class])->group(function () {
    Route::inertia('/dashboard', 'Nazhir/Dashboard')->name('dashboard');
    Route::inertia('/manajemen-user', 'Nazhir/ManajemenUser')->name('nazhir.user');
    Route::inertia('/manajemen-program', 'Nazhir/ManajemenProgram')->name('nazhir.program');
    Route::inertia('/manajemen-program/tambah', 'Nazhir/TambahProgram')->name('nazhir.program.tambah');
    Route::get('/program/{id}/edit', function ($id) {
        return inertia('Nazhir/EditProgram', [
            'id' => $id
        ]);
    })->name('nazhir.program.edit');
    Route::get('/laporan/tambah/{programId}', function ($programId) {
        return inertia('Nazhir/TambahLaporan', [
            'programId' => $programId
        ]);
    })->name('nazhir.laporan.tambah');
    Route::get('/laporan/{id}/edit', function ($id) {
        return inertia('Nazhir/EditLaporan', [
            'id' => $id
        ]);
    })->name('nazhir.laporan.edit');
});

require __DIR__.'/settings.php';
