<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Wakif/Home')->name('home');
Route::get('/login', function () {
    return redirect('/wakif/login');
})->name('login');
Route::get('/register', function () {
    return redirect('/wakif/register');
})->name('register');

Route::inertia('/wakif/login', 'Wakif/Auth/Login')->name('wakif.login');
Route::inertia('/wakif/register', 'Wakif/Auth/Register')->name('wakif.register');

Route::inertia('/nazhir/login', 'Nazhir/Auth/Login')->name('nazhir.login');
Route::inertia('/nazhir/register', 'Nazhir/Auth/Register')->name('nazhir.register');

Route::inertia('/superadmin/login', 'Superadmin/Auth/Login')->name('superadmin.login');

Route::middleware([\App\Http\Middleware\CheckSuperadmin::class])->group(function () {
    Route::inertia('/superadmin/dashboard', 'Superadmin/Dashboard')->name('superadmin.dashboard');
    Route::inertia('/superadmin/users', 'Superadmin/ManajemenUser')->name('superadmin.users');
    Route::inertia('/superadmin/programs', 'Superadmin/ManajemenProgram')->name('superadmin.programs');
    Route::inertia('/superadmin/pencairan', 'Superadmin/ManajemenPencairan')->name('superadmin.pencairan');
});

Route::inertia('/program', 'Wakif/Program')->name('program.list');
Route::get('/program/{id}', function ($id) {
    return inertia('Wakif/ProgramDetail', [
        'id' => $id
    ]);
})->name('program.detail');
Route::inertia('/laporan', 'Wakif/Laporan')->name('laporan');
Route::inertia('/tentang', 'Wakif/TentangKami')->name('tentang');
Route::middleware([\App\Http\Middleware\CheckWakif::class])->group(function () {
    Route::inertia('/profil', 'Wakif/Profile')->name('profile');
    Route::inertia('/riwayat-transaksi', 'Wakif/RiwayatTransaksi')->name('transaksi.riwayat');
    Route::get('/transaksi/{id}', function ($id) {
        return inertia('Wakif/DetailTransaksi', [
            'id' => $id
        ]);
    })->name('transaksi.detail');
});

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
    Route::inertia('/manajemen-pencairan', 'Nazhir/ManajemenPencairan')->name('nazhir.pencairan');
});

require __DIR__.'/settings.php';
