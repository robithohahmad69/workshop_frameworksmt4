<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KunjunganController extends Controller
{
    const THRESHOLD_MAX = 300;

    public function index()
    {
        return view('kunjungan.index');
    }

    private function haversine($lat1, $lng1, $lat2, $lng2)
    {
        $R = 6371000;
        $dLat = ($lat2 - $lat1) * M_PI / 180;
        $dLng = ($lng2 - $lng1) * M_PI / 180;
        $a = sin($dLat / 2) ** 2 +
             cos($lat1 * M_PI / 180) * cos($lat2 * M_PI / 180) *
             sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }

    public function simpan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'toko_id' => 'required|exists:tokos,id',
            'lat_sales' => 'required|numeric|between:-90,90',
            'lng_sales' => 'required|numeric|between:-180,180',
            'accuracy_sales' => 'required|numeric|min:0'
        ], [
            'toko_id.exists' => 'Toko tidak ditemukan',
            'lat_sales.between' => 'Latitude sales tidak valid',
            'lng_sales.between' => 'Longitude sales tidak valid'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $toko = Toko::find($request->toko_id);

            $jarak_meter = $this->haversine(
                $toko->latitude,
                $toko->longitude,
                $request->lat_sales,
                $request->lng_sales
            );

            $threshold_efektif = self::THRESHOLD_MAX + $toko->accuracy + $request->accuracy_sales;
            $status = ($jarak_meter <= $threshold_efektif) ? 'diterima' : 'ditolak';

            $kunjungan = Kunjungan::create([
                'toko_id' => $request->toko_id,
                'lat_sales' => $request->lat_sales,
                'lng_sales' => $request->lng_sales,
                'accuracy_sales' => $request->accuracy_sales,
                'jarak_meter' => $jarak_meter,
                'threshold_efektif' => $threshold_efektif,
                'status' => $status
            ]);

            // Message berdasarkan status
            $message = $status === 'diterima'
                ? "KUNJUNGAN BERHASIL! Jarak Anda: {$jarak_meter}m dari toko {$toko->nama_toko}"
                : "KUNJUNGAN GAGAL! Jarak Anda: {$jarak_meter}m (melebihi batas {$threshold_efektif}m) dari toko {$toko->nama_toko}";

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'kunjungan' => $kunjungan,
                    'toko' => $toko,
                    'status' => $status,
                    'jarak_meter' => $jarak_meter,
                    'threshold_efektif' => $threshold_efektif
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses kunjungan: ' . $e->getMessage()
            ], 500);
        }
    }
}
