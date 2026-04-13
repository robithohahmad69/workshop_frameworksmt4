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
          Schema::create('customers', function (Blueprint $table) {
              $table->id();
              $table->string('nama');
              $table->text('alamat')->nullable();
              $table->string('provinsi')->nullable();
              $table->string('kota')->nullable();
              $table->string('kecamatan')->nullable();
              $table->string('kodepos_kelurahan')->nullable();
              $table->binary('foto_blob')->nullable();
              $table->string('foto_path')->nullable();
              $table->timestamps();
          });
      }

      /**
       * Reverse the migrations.
       */
      public function down(): void
      {
          Schema::dropIfExists('customers');
      }
  };