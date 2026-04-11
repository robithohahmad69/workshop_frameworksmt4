<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VendorMenuSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'vendor' => [
                    'name'     => 'Kantin Bu Sari',
                    'email'    => 'busari@kantin.com',
                    'password' => Hash::make('gedongombo'),
                ],
                'menus' => [
                    ['nama' => 'Nasi Goreng Spesial',  'kategori' => 'Makanan', 'harga' => 15000],
                    ['nama' => 'Nasi Ayam Geprek',      'kategori' => 'Makanan', 'harga' => 18000],
                    ['nama' => 'Nasi Uduk Komplit',     'kategori' => 'Makanan', 'harga' => 13000],
                    ['nama' => 'Mie Goreng Seafood',    'kategori' => 'Makanan', 'harga' => 16000],
                    ['nama' => 'Es Teh Manis',          'kategori' => 'Minuman', 'harga' => 5000],
                    ['nama' => 'Es Jeruk Peras',        'kategori' => 'Minuman', 'harga' => 7000],
                    ['nama' => 'Jus Alpukat',           'kategori' => 'Minuman', 'harga' => 12000],
                    ['nama' => 'Gorengan (3pcs)',        'kategori' => 'Snack',   'harga' => 5000],
                    ['nama' => 'Risoles Mayo',          'kategori' => 'Snack',   'harga' => 4000],
                ],
            ],
            [
                'vendor' => [
                    'name'     => 'Warung Pak Budi',
                    'email'    => 'pakbudi@kantin.com',
                    'password' => Hash::make('gedongombo'),
                ],
                'menus' => [
                    ['nama' => 'Nasi Rawon',            'kategori' => 'Makanan', 'harga' => 20000],
                    ['nama' => 'Soto Ayam',             'kategori' => 'Makanan', 'harga' => 15000],
                    ['nama' => 'Lontong Sayur',         'kategori' => 'Makanan', 'harga' => 12000],
                    ['nama' => 'Pecel Lele',            'kategori' => 'Makanan', 'harga' => 18000],
                    ['nama' => 'Nasi Pecel',            'kategori' => 'Makanan', 'harga' => 13000],
                    ['nama' => 'Es Dawet',              'kategori' => 'Minuman', 'harga' => 6000],
                    ['nama' => 'Wedang Jahe',           'kategori' => 'Minuman', 'harga' => 8000],
                    ['nama' => 'Kopi Tubruk',           'kategori' => 'Minuman', 'harga' => 5000],
                    ['nama' => 'Tempe Mendoan (2pcs)',  'kategori' => 'Snack',   'harga' => 5000],
                    ['nama' => 'Bakwan Jagung (3pcs)',  'kategori' => 'Snack',   'harga' => 6000],
                ],
            ],
            [
                'vendor' => [
                    'name'     => 'Kantin Pojok',
                    'email'    => 'kantinpojok@kantin.com',
                    'password' => Hash::make('gedongombo'),
                ],
                'menus' => [
                    ['nama' => 'Burger Crispy',         'kategori' => 'Makanan', 'harga' => 20000],
                    ['nama' => 'Hotdog Keju',           'kategori' => 'Makanan', 'harga' => 18000],
                    ['nama' => 'Kentang Goreng',        'kategori' => 'Makanan', 'harga' => 12000],
                    ['nama' => 'Roti Bakar Coklat',     'kategori' => 'Snack',   'harga' => 10000],
                    ['nama' => 'Roti Bakar Keju',       'kategori' => 'Snack',   'harga' => 12000],
                    ['nama' => 'Pisang Nugget',         'kategori' => 'Snack',   'harga' => 13000],
                    ['nama' => 'Milkshake Coklat',      'kategori' => 'Minuman', 'harga' => 15000],
                    ['nama' => 'Milkshake Strawberry',  'kategori' => 'Minuman', 'harga' => 15000],
                    ['nama' => 'Lemon Tea',             'kategori' => 'Minuman', 'harga' => 8000],
                    ['nama' => 'Air Mineral',           'kategori' => 'Minuman', 'harga' => 3000],
                ],
            ],
        ];

        foreach ($data as $item) {
            $vendor = Vendor::firstOrCreate(
                ['email' => $item['vendor']['email']],
                $item['vendor']
            );

            Menu::where('vendor_id', $vendor->id)->delete();

            foreach ($item['menus'] as $menu) {
                Menu::create([
                    'vendor_id' => $vendor->id,
                    'nama'      => $menu['nama'],
                    'kategori'  => $menu['kategori'],
                    'harga'     => $menu['harga'],
                ]);
            }

            $this->command->info("✅ Vendor '{$vendor->name}' → {$vendor->menus()->count()} menu dibuat.");
        }
    }
}