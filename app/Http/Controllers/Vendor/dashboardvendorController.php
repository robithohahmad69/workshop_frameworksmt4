<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DashboardvendorController extends Controller
{
    public function index()
    {
        $vendor = Auth::guard('vendor')->user();

        $totalMenu    = Menu::where('vendor_id', $vendor->id)->count();
        $totalPesanan = Order::where('vendor_id', $vendor->id)->count();
        $pesananLunas = Order::where('vendor_id', $vendor->id)
                            ->where('status_bayar', 'lunas')->count();
        $pendapatan   = Order::where('vendor_id', $vendor->id)
                            ->where('status_bayar', 'lunas')->sum('total');

        $pesananTerbaru = Order::where('vendor_id', $vendor->id)
                            ->latest()->take(5)->get();

        return view('vendor.dashboard', compact(
            'totalMenu', 'totalPesanan', 'pesananLunas', 'pendapatan', 'pesananTerbaru'
        ));
    }
}