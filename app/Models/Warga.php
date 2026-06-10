<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    protected $table = 'warga';

    protected $fillable = [
        'nama',
        'nik',
        'nfc_serial',
        'alamat',
    ];

    // Relasi: satu warga punya banyak riwayat scan
    public function riwayatScan()
    {
        return $this->hasMany(RiwayatScan::class);
    }
}