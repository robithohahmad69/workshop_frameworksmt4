<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatScan extends Model
{
    protected $table = 'riwayat_scan';

    protected $fillable = [
        'warga_id',
        'serial_number',
        'status',
        'waktu_scan',
    ];

    // Relasi: riwayat scan milik satu warga
    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }
}