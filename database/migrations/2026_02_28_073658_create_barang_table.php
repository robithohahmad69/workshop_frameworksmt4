<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->char('id_barang', 8);
            $table->string('nama', 50);
            $table->integer('harga');
            $table->timestamps();

            $table->primary('id_barang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};