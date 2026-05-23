<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $fillable = [
        'toko_id',
        'lat_sales',
        'lng_sales',
        'accuracy_sales',
        'jarak_meter',
        'threshold_efektif',
        'status'
    ];

    protected $casts = [
        'lat_sales' => 'decimal:8',
        'lng_sales' => 'decimal:8',
        'accuracy_sales' => 'float',
        'jarak_meter' => 'float',
        'threshold_efektif' => 'float'
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }
}
