<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    // ============================================================
    // INDEX - Tampilkan semua data (Datatables)
    // URL: GET /barang
    // ============================================================
    public function index()
    {
        $barang = Barang::all();
        return view('barang.index', compact('barang'));
    }

    // ============================================================
    // CREATE - Tampilkan form tambah
    // URL: GET /barang/create
    // ============================================================
    public function create()
    {
        return view('barang.create');
    }

    // ============================================================
    // STORE - Proses simpan data baru
    // URL: POST /barang
    // id_barang tidak dikirim → dihandle trigger database
    // ============================================================
    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ], [
            'nama.required'  => 'Nama barang wajib diisi.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric'  => 'Harga harus berupa angka.',
            'harga.min'      => 'Harga tidak boleh negatif.',
        ]);

        \DB::table('barang')->insert([
            'nama'       => $request->nama,
            'harga'      => $request->harga,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('barang.index')
                         ->with('success', 'Barang berhasil ditambahkan!');
    }

    // ============================================================
    // SHOW - Tampilkan detail satu barang
    // URL: GET /barang/{id}
    // ============================================================
    public function show(Barang $barang)
    {
        return view('barang.show', compact('barang'));
    }

    // ============================================================
    // EDIT - Tampilkan form edit
    // URL: GET /barang/{id}/edit
    // ============================================================
    public function edit(Barang $barang)
    {
        return view('barang.edit', compact('barang'));
    }

    // ============================================================
    // UPDATE - Proses simpan perubahan
    // URL: PUT /barang/{id}
    // ============================================================
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama'  => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ], [
            'nama.required'  => 'Nama barang wajib diisi.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric'  => 'Harga harus berupa angka.',
            'harga.min'      => 'Harga tidak boleh negatif.',
        ]);

        $barang->update([
            'nama'  => $request->nama,
            'harga' => $request->harga,
        ]);

        return redirect()->route('barang.index')
                         ->with('success', 'Barang berhasil diperbarui!');
    }

    // ============================================================
    // DESTROY - Hapus data
    // URL: DELETE /barang/{id}
    // ============================================================
    public function destroy(Barang $barang)
    {
        $barang->delete();

        return redirect()->route('barang.index')
                         ->with('success', 'Barang berhasil dihapus!');
    }

    // ============================================================
    // CETAK PDF - Generate label harga
    // URL: POST /barang/cetak-pdf
    // ============================================================
    public function cetakPdf(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array|min:1',
            'start_x'      => 'required|integer|min:1|max:5',
            'start_y'      => 'required|integer|min:1|max:8',
        ], [
            'selected_ids.required' => 'Pilih minimal 1 barang.',
            'selected_ids.min'      => 'Pilih minimal 1 barang.',
        ]);

        $barangs = Barang::whereIn('id_barang', $request->selected_ids)->get();

        // Rumus: (Y-1)*5 + (X-1) → hitung berapa label yang dilewati
        $startIndex = (($request->start_y - 1) * 5) + ($request->start_x - 1);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('barang.pdf', [
            'barangs'    => $barangs,
            'startIndex' => $startIndex,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('label-harga.pdf');
    }
}