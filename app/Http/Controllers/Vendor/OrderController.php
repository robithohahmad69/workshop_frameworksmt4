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
}