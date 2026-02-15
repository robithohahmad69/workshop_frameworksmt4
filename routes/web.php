<?php

use App\Http\Controllers\bukuController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\kategoriController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\profileController; 


// ========================================
// AUTH ROUTES (Guest - Belum Login)
// ========================================
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Register
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// ========================================
// LOGOUT ROUTE
// ========================================
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ========================================
// PROTECTED ROUTES (Auth - Harus Login)
// ========================================
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
// Profile
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Kategori CRUD
    Route::resource('kategori', KategoriController::class);
    
    // Buku CRUD
    Route::resource('buku', BukuController::class);
});
