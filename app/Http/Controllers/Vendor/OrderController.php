<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Vendor Order Controller
 * Mengelola pesanan dari sisi vendor (melihat, mencetak invoice, mengubah status)
 */
class OrderController extends Controller
{
    /**
     * Tampilkan halaman QR Code Scanner untuk Vendor
     */
    public function showQrScanner()
    {
        $vendor = auth()->guard('vendor')->user();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        return view('vendor.qr-scanner');
    }

    /**
     * Tampilkan daftar semua pesanan untuk vendor ini
     */
    public function index()
    {
        $vendor = auth()->guard('vendor')->user();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        $orders = Order::with(['orderItems.menu', 'payment'])
                      ->where('vendor_id', $vendor->id)
                      ->latest()
                      ->get();

        return view('vendor.orders.index', compact('orders'));
    }

    /**
     * Tampilkan detail invoice satu pesanan
     */
    public function show(Order $order)
    {
        $vendor = auth()->guard('vendor')->user();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        abort_if($order->vendor_id !== $vendor->id, 403);

        $order->load(['orderItems.menu', 'payment', 'vendor']);

        return view('vendor.orders.show', compact('order'));
    }

    /**
     * Tandai pesanan sebagai selesai (shortcut — langsung complete)
     */
    public function complete(Order $order)
    {
        $vendor = auth()->guard('vendor')->user();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        abort_if($order->vendor_id !== $vendor->id, 403);

        $order->update(['status' => 'completed']);

        return back()->with('success', "Pesanan #{$order->id} berhasil ditandai selesai!");
    }

    /**
     * Update status pesanan via dropdown
     * Hanya boleh jika status_bayar = lunas
     */
    public function updateStatus(Request $request, Order $order)
    {
        $vendor = auth()->guard('vendor')->user();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        // Pastikan order milik vendor yang login
        abort_if($order->vendor_id !== $vendor->id, 403);

        // Validasi nilai status yang boleh dipilih
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        // Hanya boleh ubah status jika sudah lunas
        if ($order->status_bayar !== 'lunas') {
            return back()->with('error', 'Tidak bisa mengubah status pesanan yang belum lunas.');
        }

        $order->update(['status' => $request->status]);

        $statusLabel = [
            'pending'    => 'Antri',
            'processing' => 'Sedang Dimasak',
            'completed'  => 'Sudah Diantar',
            'cancelled'  => 'Dibatalkan',
        ];

        return back()->with('success', "Status pesanan #{$order->id} diubah menjadi: {$statusLabel[$request->status]}");
    }

    /**
     * Filter pesanan berdasarkan status
     */
    public function filter(Request $request)
    {
        $vendor = auth()->guard('vendor')->user();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        $status = $request->get('status');

        $query = Order::with(['orderItems.menu', 'payment'])
                      ->where('vendor_id', $vendor->id);

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->get();

        return view('vendor.orders.index', compact('orders', 'status'));
    }

    /**
     * API untuk lookup pesanan via QR Code scan
     * Dipanggil oleh vendor QR scanner
     */
    public function getOrderQr(Request $request)
    {
        $vendor = auth()->guard('vendor')->user();

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor tidak terautentikasi'
            ], 401);
        }

        $request->validate([
            'order_id' => 'required|integer|exists:orders,id'
        ]);

        $order = Order::with(['orderItems.menu', 'payment', 'vendor'])
                      ->where('id', $request->order_id)
                      ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        // Verifikasi bahwa pesanan milik vendor yang login
        if ($order->vendor_id !== $vendor->id) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini bukan dari vendor Anda'
            ], 403);
        }

        // Format response dengan menu items untuk vendor ini
        $menuItems = [];
        foreach ($order->orderItems as $item) {
            $menuItems[] = [
                'menu_nama' => $item->menu->nama,
                'qty'       => $item->qty,
                'harga'     => $item->harga,
                'subtotal'  => $item->qty * $item->harga
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_id'       => $order->id,
                'customer_name'  => $order->customer_name,
                'vendor_name'    => $order->vendor->name,
                'total'          => $order->total,
                'status_bayar'   => $order->status_bayar,
                'status'         => $order->status ?? 'pending',
                'created_at'     => $order->created_at->format('d M Y H:i'),
                'menu_items'     => $menuItems
            ]
        ]);
    }
}