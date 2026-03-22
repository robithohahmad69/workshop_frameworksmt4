<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id('id_penjualan'); // primary key
            $table->timestamp('created_at')->useCurrent();
            $table->integer('total');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};