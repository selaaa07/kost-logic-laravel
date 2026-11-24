<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\KwitansiController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PenghuniController;
use App\Http\Controllers\KomplainController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\KamarController;

Route::get('/', [LandingController::class, 'index']);

Route::get('/kwitansi/{id}', [KwitansiController::class, 'cetak'])
    ->middleware('auth')
    ->name('kwitansi.cetak');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Route Khusus Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('kamar', KamarController::class);
    Route::resource('penghuni', PenghuniController::class)->names('admin.penghuni');

    Route::get('/komplain', [KomplainController::class, 'indexAdmin'])->name('admin.komplain.index');
    Route::patch('/komplain/{id}/selesai', [KomplainController::class, 'selesai'])->name('admin.komplain.selesai');
});

// Route Khusus Penghuni
Route::middleware(['auth', 'role:penghuni'])->prefix('user')->group(function () {
    Route::get('/dashboard', function () {
        return 'Halo Penghuni (' . auth()->user()->name . ')! Login berhasil. <form method="POST" action="/logout">' . csrf_field() . '<button type="submit">Logout</button></form>';
    })->name('user.dashboard');

    Route::get('/ubah-password', [PasswordController::class, 'form'])->name('user.ubah-password');
    Route::post('/ubah-password', [PasswordController::class, 'update'])->name('user.ubah-password.update');

    // Komplain - USER
    Route::get('/komplain', [KomplainController::class, 'indexUser'])->name('user.komplain');
    Route::post('/komplain', [KomplainController::class, 'store'])->name('user.komplain.store');
});