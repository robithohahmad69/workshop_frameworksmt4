<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;  // ← TAMBAH ini
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('customer.data', compact('customers'));
    }

    public function createBlob()
    {
        return view('customer.create1');
    }

    public function createFile()
    {
        return view('customer.create2');
    }

    public function storeBlob(Request $request)
    {
        $request->validate([
            'nama'              => 'required|string|max:255',
            'alamat'            => 'nullable|string',
            'provinsi'          => 'nullable|string',
            'kota'              => 'nullable|string',
            'kecamatan'         => 'nullable|string',
            'kodepos_kelurahan' => 'nullable|string',
            'foto'              => 'required|string',
        ]);

        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->foto);

        Customer::create([
            'nama'              => $request->nama,
            'alamat'            => $request->alamat,
            'provinsi'          => $request->provinsi,
            'kota'              => $request->kota,
            'kecamatan'         => $request->kecamatan,
            'kodepos_kelurahan' => $request->kodepos_kelurahan,
            'foto_blob'         => $base64,
        ]);

        return redirect()->route('customer-data.index')
                         ->with('success', 'Customer berhasil ditambahkan (BLOB)!');
    }

    public function storeFile(Request $request)
    {
        $request->validate([
            'nama'              => 'required|string|max:255',
            'alamat'            => 'nullable|string',
            'provinsi'          => 'nullable|string',
            'kota'              => 'nullable|string',
            'kecamatan'         => 'nullable|string',
            'kodepos_kelurahan' => 'nullable|string',
            'foto'              => 'required|string',
        ]);

        $base64   = preg_replace('/^data:image\/\w+;base64,/', '', $request->foto);
        $binary   = base64_decode($base64);
        $filename = 'customer_' . time() . '.png';

        Storage::disk('public')->put('customers/' . $filename, $binary);

        Customer::create([
            'nama'              => $request->nama,
            'alamat'            => $request->alamat,
            'provinsi'          => $request->provinsi,
            'kota'              => $request->kota,
            'kecamatan'         => $request->kecamatan,
            'kodepos_kelurahan' => $request->kodepos_kelurahan,
            'foto_path'         => 'customers/' . $filename,
        ]);

        return redirect()->route('customer-data.index')
                         ->with('success', 'Customer berhasil ditambahkan (File)!');
    }
}