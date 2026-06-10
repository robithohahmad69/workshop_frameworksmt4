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
    Schema::create('riwayat_scan', function (Blueprint $table) {
        $table->id();
        $table->foreignId('warga_id')
              ->nullable()                          // nullable: kalau kartu tidak dikenal
              ->constrained('warga')
              ->nullOnDelete();
        $table->string('serial_number');            // serial yang di-scan
        $table->string('status');                   // 'dikenal' atau 'tidak_dikenal'
        $table->timestamp('waktu_scan');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_scan');
    }
};
