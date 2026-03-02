<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
public function index()
{
    $totalBuku = Buku::count();

    $totalKategori = Kategori::count();

    $bukuBulanIni = Buku::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    $bukuTerbaru = Buku::with('kategori')
        ->latest()
        ->limit(5)
        ->get();

    $kategoriPopuler = Kategori::withCount('bukus')
        ->orderBy('bukus_count', 'desc')
        ->limit(5)
        ->get();

    // TAMBAHKAN INI
    $totalBarang = Barang::count();

    // PASTIKAN totalBarang DIKIRIM KE VIEW
    return view('dashboard.index', compact(
        'totalBuku',
        'totalKategori',
        'bukuBulanIni',
        'bukuTerbaru',
        'kategoriPopuler',
        'totalBarang'
    ));
}}