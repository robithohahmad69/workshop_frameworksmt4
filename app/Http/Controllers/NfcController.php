<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\RiwayatScan;
use Illuminate\Http\Request;

class NfcController extends Controller
{
    public function index()
    {
        return view('nfc.nfc');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string',
        ]);

        $serial = $request->serial_number;

        $warga = Warga::where('nfc_serial', $serial)->first();

        $status = $warga ? 'dikenal' : 'tidak_dikenal';

        RiwayatScan::create([
            'warga_id'      => $warga?->id,
            'serial_number' => $serial,
            'status'        => $status,
            'waktu_scan'    => now(),
        ]);

        if ($warga) {
            return response()->json([
                'status' => 'dikenal',
                'pesan'  => 'Selamat datang, ' . $warga->nama . '!',
                'warga'  => [
                    'nama'   => $warga->nama,
                    'nik'    => $warga->nik,
                    'alamat' => $warga->alamat,
                ],
            ]);
        }

        return response()->json([
            'status' => 'tidak_dikenal',
            'pesan'  => 'Kartu tidak terdaftar. Serial: ' . $serial,
        ], 404);
    }

    public function riwayat()
    {
        $riwayat = RiwayatScan::with('warga')
                               ->latest()
                               ->take(50)
                               ->get();
        return view('nfc.riwayat', compact('riwayat'));
    }
}