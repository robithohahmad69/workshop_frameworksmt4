<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua data buku dengan relasi kategori
        $bukus = Buku::with('kategori')->get();
        
        return view('buku.index', compact('bukus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil semua kategori untuk dropdown
        $kategoris = Kategori::all();
        
        return view('buku.create', compact('kategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:buku,kode',
            'judul' => 'required|string|max:500',
            'pengarang' => 'required|string|max:200',
            'id_kategori' => 'required|exists:kategori,id_kategori'
        ], [
            'kode.required' => 'Kode buku wajib diisi',
            'kode.max' => 'Kode buku maksimal 20 karakter',
            'kode.unique' => 'Kode buku sudah ada',
            'judul.required' => 'Judul buku wajib diisi',
            'judul.max' => 'Judul buku maksimal 500 karakter',
            'pengarang.required' => 'Pengarang wajib diisi',
            'pengarang.max' => 'Pengarang maksimal 200 karakter',
            'id_kategori.required' => 'Kategori wajib dipilih',
            'id_kategori.exists' => 'Kategori tidak valid'
        ]);

        // Simpan ke database
        Buku::create($validated);

        // Redirect dengan pesan sukses
        return redirect('/buku')->with('success', 'Buku berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Ambil data buku dengan kategorinya
        $buku = Buku::with('kategori')->findOrFail($id);
        
        return view('buku.show', compact('buku'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $buku = Buku::findOrFail($id);
        $kategoris = Kategori::all();
        
        return view('buku.edit', compact('buku', 'kategoris'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $buku = Buku::findOrFail($id);

        // Validasi input (ignore unique untuk data sendiri)
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:buku,kode,' . $id . ',id_buku',
            'judul' => 'required|string|max:500',
            'pengarang' => 'required|string|max:200',
            'id_kategori' => 'required|exists:kategori,id_kategori'
        ], [
            'kode.required' => 'Kode buku wajib diisi',
            'kode.max' => 'Kode buku maksimal 20 karakter',
            'kode.unique' => 'Kode buku sudah ada',
            'judul.required' => 'Judul buku wajib diisi',
            'judul.max' => 'Judul buku maksimal 500 karakter',
            'pengarang.required' => 'Pengarang wajib diisi',
            'pengarang.max' => 'Pengarang maksimal 200 karakter',
            'id_kategori.required' => 'Kategori wajib dipilih',
            'id_kategori.exists' => 'Kategori tidak valid'
        ]);

        // Update data
        $buku->update($validated);

        // Redirect dengan pesan sukses
        return redirect('/buku')->with('success', 'Buku berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $buku = Buku::findOrFail($id);
        
        // Hapus buku
        $buku->delete();

        // Redirect dengan pesan sukses
        return redirect('/buku')->with('success', 'Buku berhasil dihapus!');
    }
}