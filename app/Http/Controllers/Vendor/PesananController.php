<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    public function index()
    {
        $vendor = Auth::guard('vendor')->user();

        $pesanan = Order::with(['orderItems.menu'])
                    ->where('vendor_id', $vendor->id)
                    ->where('status_bayar', 'lunas')
                    ->latest()
                    ->get();

        return view('vendor.pesanan.index', compact('pesanan'));
    }
}