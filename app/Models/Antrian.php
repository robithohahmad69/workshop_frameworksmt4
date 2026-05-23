<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    // Nama tabel di database (karena migration pakai 'antreans' bukan 'antrians')
    protected $table = 'antreans';

    // Kolom yang boleh diisi lewat mass assignment (create/fill)
    protected $fillable = ['nomor', 'nama', 'status'];
}