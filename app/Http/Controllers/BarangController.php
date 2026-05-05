<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::all();
        return view('barang.index', compact('barang'));
    }

    public function create()
    {
        return view('barang.create');
    }

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

    public function show(Barang $barang)
    {
        return view('barang.show', compact('barang'));
    }

    public function edit(Barang $barang)
    {
        return view('barang.edit', compact('barang'));
    }

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

    public function destroy(Barang $barang)
    {
        $barang->delete();

        return redirect()->route('barang.index')
                         ->with('success', 'Barang berhasil dihapus!');
    }

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

        $startIndex = (($request->start_y - 1) * 5) + ($request->start_x - 1);

        // Generate barcode untuk setiap barang
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcodes  = [];
        foreach ($barangs as $b) {
            $png = $generator->getBarcode(
                (string) $b->id_barang,
                $generator::TYPE_CODE_128
            );
            $barcodes[$b->id_barang] = 'data:image/png;base64,' . base64_encode($png);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('barang.pdf', [
            'barangs'    => $barangs,
            'startIndex' => $startIndex,
            'barcodes'   => $barcodes,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('label-harga.pdf');
    }

    /**
     * API untuk mendapatkan data barang berdasarkan barcode scan
     * Dipanggil oleh modal scanner via AJAX/Fetch
     * Praktikum 1 - Barcode Scanner
     */
    public function getBarangByBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

        $barang = Barang::where('id_barang', $request->barcode)->first();

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang dengan barcode tersebut tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id_barang' => $barang->id_barang,
                'nama'      => $barang->nama,
                'harga'     => $barang->harga
            ]
        ]);
    }
}