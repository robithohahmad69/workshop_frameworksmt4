<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;

class WargaController extends Controller
{
    public function index()
    {
        $warga = Warga::latest()->get();
        return view('nfc.warga', compact('warga'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'       => 'required|string|max:255',
            'nik'        => 'required|digits:16|unique:warga,nik',
            'nfc_serial' => 'required|unique:warga,nfc_serial',
            'alamat'     => 'nullable|string',
        ]);

        Warga::create($request->all());

        return redirect()->route('warga.index')
                         ->with('success', 'Warga berhasil didaftarkan!');
    }
}