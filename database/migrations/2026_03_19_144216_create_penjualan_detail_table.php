<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->id('idpenjualan_detail'); // primary key

            $table->unsignedBigInteger('id_penjualan');
            $table->string('id_barang', 8);

            $table->integer('jumlah');
            $table->integer('subtotal');

            // foreign key ke penjualan
            $table->foreign('id_penjualan')
                  ->references('id_penjualan')
                  ->on('penjualan')
                  ->onDelete('cascade');

            // foreign key ke barang
            $table->foreign('id_barang')
                  ->references('id_barang')
                  ->on('barang')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_detail');
    }
};