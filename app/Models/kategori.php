<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database
     */
    protected $table = 'kategori';
    
    /**
     * Primary key tabel
     */
    protected $primaryKey = 'id_kategori';
    
    /**
     * Kolom yang boleh diisi mass assignment
     * Gunakan fillable ATAU guarded, jangan keduanya
     */
    protected $fillable = [
        'nama_kategori'
    ];
    
    /**
     * Kolom yang tidak boleh diisi mass assignment
     * Alternative dari fillable
     */
    // protected $guarded = [];
    
    /**
     * Relasi One to Many ke Buku
     * 1 Kategori memiliki banyak Buku
     */
    public function bukus()
    {
        return $this->hasMany(Buku::class, 'id_kategori', 'id_kategori');
    }
}