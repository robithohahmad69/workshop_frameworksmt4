<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database
     */
    protected $table = 'buku';
    
    /**
     * Primary key tabel
     */
    protected $primaryKey = 'id_buku';
    
    /**
     * Kolom yang boleh diisi mass assignment
     */
    protected $fillable = [
        'kode',
        'judul',
        'pengarang',
        'id_kategori'
    ];
    
    /**
     * Relasi Many to One ke Kategori
     * Banyak Buku milik 1 Kategori
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }
}