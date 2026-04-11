<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ORDERS TABLE ===\n";
$orders = DB::table('orders')
    ->select('id', 'customer_name', 'total', 'status_bayar', 'status', 'created_at')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($orders as $order) {
    echo sprintf(
        "ID: %d | Customer: %s | Total: %s | Status Bayar: %s | Status: %s\n",
        $order->id,
        $order->customer_name,
        number_format($order->total),
        $order->status_bayar,
        $order->status ?? 'NULL'
    );
}

echo "\n=== PAYMENTS TABLE ===\n";
$payments = DB::table('payments')
    ->select('id', 'order_id', 'midtrans_order_id', 'status', 'snap_token')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($payments as $payment) {
    echo sprintf(
        "ID: %d | Order ID: %d | Midtrans Order ID: %s | Status: %s | Has Token: %s\n",
        $payment->id,
        $payment->order_id,
        $payment->midtrans_order_id,
        $payment->status,
        $payment->snap_token ? 'Yes' : 'No'
    );
}

echo "\n=== DIAGNOSIS ===\n";
$pendingOrders = DB::table('orders')
    ->where('status_bayar', 'pending')
    ->count();

echo "Jumlah order dengan status_bayar = pending: $pendingOrders\n";

$lunasOrders = DB::table('orders')
    ->where('status_bayar', 'lunas')
    ->count();

echo "Jumlah order dengan status_bayar = lunas: $lunasOrders\n";
