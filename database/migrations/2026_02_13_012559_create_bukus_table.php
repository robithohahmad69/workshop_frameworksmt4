<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->id('id_buku');
            $table->string('kode', 20)->nullable(false);
            $table->string('judul', 500)->nullable(false);
            $table->string('pengarang', 200)->nullable(false);
            $table->unsignedBigInteger('id_kategori');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_kategori', 'fk_kategori_memiliki_buku')
                  ->references('id_kategori')
                  ->on('kategori')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};