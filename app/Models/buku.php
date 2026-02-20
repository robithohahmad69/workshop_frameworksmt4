<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;


    protected $table = 'buku';
    
    
    protected $primaryKey = 'id_buku';
    
  
    protected $fillable = [
        'kode',
        'judul',
        'pengarang',
        'id_kategori'
    ];
    
  
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }
}