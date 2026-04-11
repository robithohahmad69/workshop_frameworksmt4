<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    private function vendor()
    {
        return Auth::guard('vendor')->user();
    }

    // Tampilkan semua menu milik vendor ini
    public function index()
    {
        $menus = Menu::where('vendor_id', $this->vendor()->id)
                    ->latest()->get();

        return view('vendor.menu.index', compact('menus'));
    }

    // Form tambah menu
    public function create()
    {
        return view('vendor.menu.create');
    }

    // Simpan menu baru
    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'harga'    => 'required|integer|min:500',
        ]);

        Menu::create([
            'vendor_id' => $this->vendor()->id,
            'nama'      => $request->nama,
            'kategori'  => $request->kategori,
            'harga'     => $request->harga,
        ]);

        return redirect()->route('vendor.menu.index')
                        ->with('success', 'Menu berhasil ditambahkan!');
    }

    // Form edit menu
    public function edit(Menu $menu)
    {
        // Pastikan menu ini milik vendor yang login
        abort_if($menu->vendor_id !== $this->vendor()->id, 403);

        return view('vendor.menu.edit', compact('menu'));
    }

    // Update menu
    public function update(Request $request, Menu $menu)
    {
        abort_if($menu->vendor_id !== $this->vendor()->id, 403);

        $request->validate([
            'nama'     => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'harga'    => 'required|integer|min:500',
        ]);

        $menu->update([
            'nama'     => $request->nama,
            'kategori' => $request->kategori,
            'harga'    => $request->harga,
        ]);

        return redirect()->route('vendor.menu.index')
                        ->with('success', 'Menu berhasil diupdate!');
    }

    // Hapus menu
    public function destroy(Menu $menu)
    {
        abort_if($menu->vendor_id !== $this->vendor()->id, 403);

        $menu->delete();

        return redirect()->route('vendor.menu.index')
                        ->with('success', 'Menu berhasil dihapus!');
    }
}