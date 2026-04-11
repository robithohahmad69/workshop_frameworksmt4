<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;


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

use App\Http\Controllers\Vendor\AuthController;
use App\Http\Controllers\Vendor\DashboardvendorController;
use App\Http\Middleware\VendorAuth;
use App\Http\Controllers\Vendor\MenuController;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Vendor\PesananController;


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

    // KATEGORI CRUD
    Route::resource('kategori', KategoriController::class);

    // BUKU CRUD
    Route::resource('buku', BukuController::class);

    // SET PASSWORD GOOGLE USER
    Route::get('/set-password',
        [GoogleController::class, 'showSetPasswordForm'])
        ->name('set.password');

    Route::post('/set-password',
        [GoogleController::class, 'setPassword'])
        ->name('set.password.store');

    // PDF ROUTES
    Route::get('/pdf/landscape',
        [PdfController::class, 'landscape'])
        ->name('pdf.landscape');

    Route::get('/pdf/portrait',
        [PdfController::class, 'portrait'])
        ->name('pdf.portrait');

    Route::post('/barang/pdf', [BarangController::class, 'cetakPdf'])->name('barang.pdf');
    Route::resource('barang', BarangController::class);

    Route::get('/tabel',       fn() => view('datatables.tabel'));
    Route::get('/datatables',  fn() => view('datatables.datatables'));
    Route::get('/select',      fn() => view('select.select'));
    Route::get('/select2',     fn() => view('select.select2'));
    Route::get('/ajax/wilayahajax',   fn() => view('ajax.wilayahajax'));
    Route::get('/axios/wilayahaxios', fn() => view('axios.wilayahaxios'));

    // API Wilayah
    Route::get('/api/provinsi', function () {
        $response = Http::get('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json');
        return $response->json();
    });
    Route::get('/api/kota/{id}', function ($id) {
        $response = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/regencies/$id.json");
        return $response->json();
    });
    Route::get('/api/kecamatan/{id}', function ($id) {
        $response = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/districts/$id.json");
        return $response->json();
    });
    Route::get('/api/kelurahan/{id}', function ($id) {
        $response = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/villages/$id.json");
        return $response->json();
    });

    // Kasir
    Route::get('/ajax/kasirajax',    fn() => view('ajax.kasirajax'));
    Route::get('/axios/kasiraxios',  fn() => view('axios.kasiraxios'));
    Route::post('/kasir/simpan', [App\Http\Controllers\KasirController::class, 'simpan']);

    // API Barang
    Route::get('/api/barang/{kode}', function ($kode) {
        $barang = DB::table('barang')->where('id_barang', $kode)->first();
        if (!$barang) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }
        return response()->json($barang);
    });
});


// ========================================
// VENDOR ROUTES
// ========================================
Route::prefix('vendor')->name('vendor.')->group(function () {

    // Tanpa login
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');

    // Butuh login vendor
    Route::middleware(VendorAuth::class)->group(function () {
        Route::post('/logout',    [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard',  [DashboardvendorController::class, 'index'])->name('dashboard');
        Route::get('/profile',    [AuthController::class, 'showProfile'])->name('profile');
        Route::put('/profile',    [AuthController::class, 'updateProfile'])->name('profile.update');
        Route::resource('menu', MenuController::class);
        Route::get('/pesanan',    [PesananController::class, 'index'])->name('pesanan.index');

        // Vendor Orders Management
        // PENTING: route /{order} yang pakai wildcard harus paling bawah
        // supaya tidak "menelan" route /filter
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/',               [VendorOrderController::class, 'index'])->name('index');
            Route::get('/filter',         [VendorOrderController::class, 'filter'])->name('filter');
            Route::post('/{order}/status',[VendorOrderController::class, 'updateStatus'])->name('updateStatus');
            Route::post('/{order}/complete', [VendorOrderController::class, 'complete'])->name('complete');
            Route::get('/{order}',        [VendorOrderController::class, 'show'])->name('show');
        });
    });
});


// ========================================
// CUSTOMER ROUTES
// ========================================
Route::prefix('order')->name('customer.')->group(function () {
    Route::get('/',                      [OrderController::class, 'index'])->name('index');
    Route::get('/menu/{vendorId}',       [OrderController::class, 'menu'])->name('menu');
    Route::post('/checkout/{vendorId}',  [OrderController::class, 'checkout'])->name('checkout');
    Route::get('/payment/{orderId}',     [OrderController::class, 'payment'])->name('payment');
    Route::get('/success/{orderId}',     [OrderController::class, 'success'])->name('success');
});


// ========================================
// WEBHOOK MIDTRANS (tanpa CSRF)
// ========================================
Route::post('/midtrans/webhook', [OrderController::class, 'webhook'])
    ->name('midtrans.webhook')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);


// ========================================
// DEBUG ROUTES (hapus saat production!)
// ========================================
Route::get('/debug/orders', function () {
    $orders = DB::table('orders')
        ->select('id', 'customer_name', 'total', 'status_bayar', 'status', 'created_at')
        ->orderBy('id', 'desc')
        ->limit(10)
        ->get();

    $payments = DB::table('payments')
        ->select('id', 'order_id', 'midtrans_order_id', 'status', 'snap_token')
        ->orderBy('id', 'desc')
        ->limit(10)
        ->get();

    return view('debug.orders', compact('orders', 'payments'));
})->name('debug.orders');

Route::post('/debug/simulate-webhook/{orderId}', function ($orderId) {
    $payment = \App\Models\Payment::where('order_id', $orderId)->first();

    if (!$payment) {
        return response()->json(['error' => 'Payment not found'], 404);
    }

    $payment->update(['status' => 'settlement']);
    $payment->order->update(['status_bayar' => 'lunas']);

    return response()->json([
        'message'          => 'Webhook simulated successfully',
        'order_id'         => $orderId,
        'payment_status'   => 'settlement',
        'status_bayar'     => 'lunas',
    ]);
})->name('debug.simulate-webhook');