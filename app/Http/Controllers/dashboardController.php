<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik
        $totalBuku = Buku::count();
        $totalKategori = Kategori::count();
        $bukuBulanIni = Buku::whereMonth('created_at', Carbon::now()->month)
                            ->whereYear('created_at', Carbon::now()->year)
                            ->count();
        
        // Buku terbaru (5 buku terakhir)
        $bukuTerbaru = Buku::with('kategori')
                           ->orderBy('created_at', 'desc')
                           ->limit(5)
                           ->get();
        
        // Kategori populer (kategori dengan buku terbanyak)
        $kategoriPopuler = Kategori::withCount('bukus')
                                   ->orderBy('bukus_count', 'desc')
                                   ->limit(5)
                                   ->get();
        
        return view('dashboard.index', compact(
            'totalBuku',
            'totalKategori',
            'bukuBulanIni',
            'bukuTerbaru',
            'kategoriPopuler'
        ));
    }
}