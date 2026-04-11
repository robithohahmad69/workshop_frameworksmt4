<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Vendor;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Halaman utama — pilih vendor
    public function index()
    {
        $vendors = Vendor::all();

        return view('customer.index', compact('vendors'));
    }

    // Halaman menu per vendor
    public function menu($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $menus  = Menu::where('vendor_id', $vendorId)->get()->groupBy('kategori');
        return view('customer.menu', compact('vendor', 'menus'));
    }

    // Proses checkout
    public function checkout(Request $request, $vendorId)
    {
        $request->validate([
            'items'       => 'required|array|min:1',
            'items.*.id'  => 'required|exists:menus,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $vendor = Vendor::findOrFail($vendorId);

        // Auto generate customer name
        $lastOrder    = Order::orderBy('id', 'desc')->first();
        $nextNumber   = $lastOrder ? $lastOrder->id + 1 : 1;
        $customerName = 'Guest_' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);

        // Hitung total
        $total     = 0;
        $itemsData = [];

        foreach ($request->items as $item) {
            $menu = Menu::findOrFail($item['id']);

            // Pastikan menu milik vendor yang dipilih
            abort_if($menu->vendor_id != $vendorId, 403);

            $subtotal = $menu->harga * $item['qty'];
            $total   += $subtotal;

            $itemsData[] = [
                'menu'  => $menu,
                'qty'   => $item['qty'],
                'harga' => $menu->harga,
            ];
        }

        // Simpan order
        $order = Order::create([
            'customer_name' => $customerName,
            'vendor_id'     => $vendorId,
            'total'         => $total,
            'status_bayar'  => 'pending',
        ]);

        // Simpan order items
        foreach ($itemsData as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id'  => $item['menu']->id,
                'qty'      => $item['qty'],
                'harga'    => $item['harga'],
            ]);
        }

        // Buat payment record
        $midtransOrderId = 'ORDER-' . $order->id . '-' . time();
        Payment::create([
            'order_id'          => $order->id,
            'midtrans_order_id' => $midtransOrderId,
            'snap_token'        => null,
            'status'            => 'pending',
        ]);

        return redirect()->route('customer.payment', $order->id);
    }

    // Halaman pembayaran
    public function payment($orderId)
    {
        $order     = Order::with(['orderItems.menu', 'payment'])->findOrFail($orderId);
        $snapToken = $this->getMidtransToken($order);

        // Simpan snap token
        $order->payment->update(['snap_token' => $snapToken]);

        return view('customer.payment', compact('order', 'snapToken'));
    }

    // Ambil token dari Midtrans
    private function getMidtransToken(Order $order)
    {
        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        $itemDetails = [];
        foreach ($order->orderItems as $item) {
            $itemDetails[] = [
                'id'       => $item->menu_id,
                'price'    => $item->harga,
                'quantity' => $item->qty,
                'name'     => $item->menu->nama,
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $order->payment->midtrans_order_id,
                'gross_amount' => $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
            ],
            'item_details' => $itemDetails,
        ];

        return \Midtrans\Snap::getSnapToken($params);
    }

    // Webhook dari Midtrans
    public function webhook(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashedKey = hash('sha512',
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            $serverKey
        );

        // Validasi signature
        if ($hashedKey !== $request->signature_key) {
            \Log::error('Midtrans webhook: Invalid signature', [
                'order_id'   => $request->order_id,
                'received'   => $request->signature_key,
                'calculated' => $hashedKey,
            ]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $payment = Payment::where('midtrans_order_id', $request->order_id)->first();

        if (!$payment) {
            \Log::error('Midtrans webhook: Payment not found', ['order_id' => $request->order_id]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        \Log::info('Midtrans webhook received', [
            'order_id'           => $request->order_id,
            'transaction_status' => $request->transaction_status,
            'status_code'        => $request->status_code,
        ]);

        // Update status payment
        $payment->update(['status' => $request->transaction_status]);

        // Kalau sudah bayar, update order jadi lunas
        if (in_array($request->transaction_status, ['settlement', 'capture'])) {
            $payment->order->update(['status_bayar' => 'lunas']);

            \Log::info('Order marked as paid', [
                'order_id'    => $payment->order->id,
                'status_bayar' => 'lunas',
            ]);
        }

        return response()->json(['message' => 'OK']);
    }

    // Halaman sukses — dengan fix cek langsung ke Midtrans API
    public function success($orderId)
    {
        $order = Order::with(['orderItems.menu', 'payment'])->findOrFail($orderId);

        // Jika status masih pending, cek langsung ke Midtrans API
        if ($order->status_bayar === 'pending' && $order->payment) {

            // Setup Midtrans config
            \Midtrans\Config::$serverKey    = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');

            try {
                // Tanya status transaksi langsung ke Midtrans
                $status = \Midtrans\Transaction::status($order->payment->midtrans_order_id);

                // Kalau sudah settlement atau capture = sudah bayar
                if (in_array($status->transaction_status, ['settlement', 'capture'])) {
                    $order->update(['status_bayar' => 'lunas']);
                    $order->payment->update(['status' => $status->transaction_status]);

                    \Log::info('Status diupdate dari halaman sukses via Midtrans API', [
                        'order_id'           => $order->id,
                        'transaction_status' => $status->transaction_status,
                    ]);

                    // Refresh data order setelah update
                    $order->refresh();
                }
            } catch (\Exception $e) {
                // Fallback: cek dari kolom payment->status yang ada di DB
                if (in_array($order->payment->status, ['settlement', 'capture'])) {
                    $order->update(['status_bayar' => 'lunas']);
                    $order->refresh();
                }

                \Log::warning('Gagal cek status Midtrans API: ' . $e->getMessage(), [
                    'order_id' => $order->id,
                ]);
            }
        }

        return view('customer.success', compact('order'));
    }
}