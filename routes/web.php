<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PdfController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleController;

use App\Http\Controllers\ProfileController;


// ========================================
// AUTH ROUTES (Guest - Belum Login)
// ========================================
Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', 
        [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', 
        [LoginController::class, 'login']);

    // Register
    Route::get('/register', 
        [RegisterController::class, 'showRegistrationForm'])
        ->name('register');

    Route::post('/register', 
        [RegisterController::class, 'register']);

    // Google Login
    Route::get('/auth/google', 
        [GoogleController::class, 'redirect'])
        ->name('google.redirect');
});


// ========================================
// GOOGLE CALLBACK & OTP
// ========================================
Route::get('/auth/google/callback', 
    [GoogleController::class, 'callback'])
    ->name('google.callback');

Route::get('/otp', 
    [GoogleController::class, 'showOtpForm'])
    ->name('otp.form');

Route::post('/otp/verify', 
    [GoogleController::class, 'verifyOtp'])
    ->name('otp.verify');


// ========================================
// LOGOUT
// ========================================
Route::post('/logout', 
    [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');


// ========================================
// PROTECTED ROUTES (HARUS LOGIN)
// ========================================
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', 
        [DashboardController::class, 'index'])
        ->name('dashboard');


    // Profile
    Route::get('/profile', 
        [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', 
        [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profile/password', 
        [ProfileController::class, 'updatePassword'])
        ->name('profile.password');


    // ========================================
    // KATEGORI CRUD
    // ========================================
    Route::resource('kategori', KategoriController::class);


    // ========================================
    // BUKU CRUD
    // ========================================
    Route::resource('buku', BukuController::class);


    // ========================================
    // SET PASSWORD GOOGLE USER
    // ========================================
    Route::get('/set-password', 
        [GoogleController::class, 'showSetPasswordForm'])
        ->name('set.password');

    Route::post('/set-password', 
        [GoogleController::class, 'setPassword'])
        ->name('set.password.store');


    // ========================================
    // PDF ROUTES
    // ========================================

    // Sertifikat (Landscape)
    Route::get('/pdf/sertifikat', 
        [PdfController::class, 'sertifikat'])
        ->name('pdf.sertifikat');


    // Surat / Laporan Buku (Portrait)
    Route::get('/pdf/surat', 
        [PdfController::class, 'surat'])
        ->name('pdf.surat');

});