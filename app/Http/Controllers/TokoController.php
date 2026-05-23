<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TokoController extends Controller
{
    public function index()
    {
        $tokos = Toko::orderBy('created_at', 'desc')->get();
        return view('toko.index', compact('tokos'));
    }

    public function generateBarcode()
    {
        $lastToko = Toko::orderBy('id', 'desc')->first();

        if ($lastToko) {
            $lastNumber = intval(substr($lastToko->barcode, -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $barcode = 'TOKO' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'barcode' => $barcode
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barcode' => 'required|string|unique:tokos,barcode',
            'nama_toko' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'required|numeric|min:0'
        ], [
            'barcode.required' => 'Barcode wajib diisi',
            'barcode.unique' => 'Barcode sudah terdaftar',
            'latitude.between' => 'Latitude harus antara -90 dan 90',
            'longitude.between' => 'Longitude harus antara -180 dan 180',
            'accuracy.min' => 'Accuracy tidak boleh negatif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $toko = Toko::create([
                'barcode' => $request->barcode,
                'nama_toko' => $request->nama_toko,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Toko berhasil ditambahkan',
                'data' => $toko
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan toko: ' . $e->getMessage()
            ], 500);
        }
    }

    public function scanBarcode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barcode' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode wajib diisi'
            ], 422);
        }

        $toko = Toko::where('barcode', $request->barcode)->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko dengan barcode tersebut tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $toko
        ]);
    }
}
