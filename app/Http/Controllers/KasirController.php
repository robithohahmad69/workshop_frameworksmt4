<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function simpan(Request $request)
    {
        $request->validate([
            'total'               => 'required|integer|min:0',
            'detail'              => 'required|array|min:1',
            'detail.*.id_barang'  => 'required|string|exists:barang,id_barang',
            'detail.*.jumlah'     => 'required|integer|min:1',
            'detail.*.subtotal'   => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {

            // insertGetId() parameter ke-2 = nama primary key
            // Wajib diisi karena primary key kita 'id_penjualan', bukan 'id'
            $idPenjualan = DB::table('penjualan')->insertGetId([
                'total'      => $request->total,
                'created_at' => now(),
            ], 'id_penjualan');

            $detailData = array_map(function ($item) use ($idPenjualan) {
                return [
                    'id_penjualan' => $idPenjualan,
                    'id_barang'    => $item['id_barang'],
                    'jumlah'       => $item['jumlah'],
                    'subtotal'     => $item['subtotal'],
                ];
            }, $request->detail);

            DB::table('penjualan_detail')->insert($detailData);
        });

        return response()->json([
            'message' => 'Transaksi berhasil disimpan'
        ], 200);
    }
}