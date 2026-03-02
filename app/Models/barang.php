<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    // Nama tabel
    protected $table = 'barang';

    // Primary key
    protected $primaryKey = 'id_barang';

    // Karena bukan auto increment
    public $incrementing = false;

    // Karena tipe char/string
    protected $keyType = 'string';

    // Kolom yang boleh diisi
    protected $fillable = [
        'id_barang',
        'nama',
        'harga'
    ];

    // Karena pakai timestamps() di migration
    public $timestamps = true;
}