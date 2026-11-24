<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Redirect awal sementara ke login (nanti diganti Landing Page oleh Puji)
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Route Khusus Admin (sementara untuk tes middleware)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return 'Halo Admin! Login berhasil. <form method="POST" action="/logout">' . csrf_field() . '<button type="submit">Logout</button></form>';
    })->name('admin.dashboard');
});

// Route Khusus Penghuni (sementara untuk tes middleware)
Route::middleware(['auth', 'role:penghuni'])->prefix('user')->group(function () {
    Route::get('/dashboard', function () {
        return 'Halo Penghuni (' . auth()->user()->name . ')! Login berhasil. <form method="POST" action="/logout">' . csrf_field() . '<button type="submit">Logout</button></form>';
    })->name('user.dashboard');
});