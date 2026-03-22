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
use App\Http\Controllers\BarangController;
use App\Http\Controllers\LabelController;


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
    Route::get('/pdf/landscape', 
        [PdfController::class, 'landscape'])
        ->name('pdf.landscape');
    // Sertifikat (Portrait)
    Route::get('/pdf/portrait',
        [PdfController::class, 'portrait'])
        ->name('pdf.portrait');


        Route::post('/barang/pdf', [BarangController::class, 'cetakPdf'])->name('barang.pdf');

        // ✅ Dynamic routes belakangan (umum)
      
        Route::resource('barang', BarangController::class);

        Route::get('/tabel',      fn() => view('datatables.tabel'));
Route::get('/datatables', fn() => view('datatables.datatables'));

     Route::get('/select',      fn() => view('select.select'));
Route::get('/select2', fn() => view('select.select2'));

Route::get('/ajax/wilayahajax',  fn() => view('ajax.wilayahajax'));
Route::get('/axios/wilayahaxios', fn() => view('axios.wilayahaxios'));

// API Wilayah

// provinsi
Route::get('/api/provinsi', function () {$response = Http::get('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json');return $response->json();});

// kota
Route::get('/api/kota/{id}', function ($id) {$response = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/regencies/$id.json");return $response->json();});

// kecamatan
Route::get('/api/kecamatan/{id}', function ($id) {$response = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/districts/$id.json");return $response->json();});

// kelurahan
Route::get('/api/kelurahan/{id}', function ($id) {$response = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/villages/$id.json");return $response->json();});




// View kasir
Route::get('/ajax/kasirajax',  fn() => view('ajax.kasirajax'));
Route::get('/axios/kasiraxios', fn() => view('axios.kasiraxios'));
 
// Simpan transaksi (dipakai oleh keduanya)
Route::post('/kasir/simpan', [App\Http\Controllers\KasirController::class, 'simpan']);
 
 
// ================================================================
// routes/web.php — route API barang per kode (tambahkan juga)
// ================================================================
Route::get('/api/barang/{kode}', function ($kode) {
    $barang = DB::table('barang')->where('id_barang', $kode)->first();
    if (!$barang) {
        return response()->json(['message' => 'Barang tidak ditemukan'], 404);
    }
    return response()->json($barang);
});
});