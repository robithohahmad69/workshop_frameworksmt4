<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Something wonderful!
|
*/



// ========================================
// TEST ROUTE (HAPUS SETELAH SELESAI)
// ========================================
Route::get('/test-order-status', function() {
    $orderId = request()->get('order_id', 1);

    $order = DB::table('orders')
        ->where('id', $orderId)
        ->first();

    $payment = DB::table('payments')
        ->where('order_id', $orderId)
        ->first();

    echo "<h1>Test Order Status</h1>";
    echo "<pre>";
    echo "Order ID: " . $order->id . "\n";
    echo "Status Bayar: " . $order->status_bayar . "\n";
    echo "Status Order: " . ($order->status ?? 'null') . "\n";
    echo "\n";
    echo "Payment ID: " . $payment->id . "\n";
    echo "Midtrans Order ID: " . $payment->midtrans_order_id . "\n";
    echo "Payment Status: " . $payment->status . "\n";
    echo "Snap Token: " . ($payment->snap_token ? 'ADA' : 'KOSONG') . "\n";
    echo "</pre>";

    echo "<hr>";
    echo "<h2>Update Test</h2>";
    echo "<form method='POST' action='/vendor/orders/{$orderId}/status'>";
    echo "<input type='hidden' name='_token' value='" . csrf_token() . "'>";
    echo "<select name='status'>";
    echo "<option value='pending'>Pending (Antri)</option>";
    echo "<option value='processing'>Processing (Sedang Dimasak)</option>";
    echo "<option value='completed'>Completed (Sudah Diantar)</option>";
    echo "<option value='cancelled'>Cancelled (Batal)</option>";
    echo "</select> ";
    echo "<button type='submit'>Update Status</button>";
    echo "</form>";

    echo "<hr>";
    echo "<p><a href='/vendor/orders'>← Kembali ke Pesanan Masuk</a></p>";
});
