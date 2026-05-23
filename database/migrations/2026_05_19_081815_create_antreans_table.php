<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antreans', function (Blueprint $table) {
            $table->id();
            $table->integer('nomor');         // nomor antrian (1, 2, 3, ...)
            $table->string('nama');           // nama tamu
            // status antrian:
            // 'menunggu'  = belum dipanggil
            // 'dipanggil' = sedang dipanggil
            // 'selesai'   = sudah dilayani
            // 'terlambat' = tidak hadir saat dipanggil
            $table->enum('status', ['menunggu', 'dipanggil', 'selesai', 'terlambat'])
                  ->default('menunggu');
            $table->timestamps();             // created_at & updated_at otomatis
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antreans');
    }
};