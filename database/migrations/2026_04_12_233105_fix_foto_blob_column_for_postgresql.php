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
        // Drop kolom foto_blob yang bermasalah
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('foto_blob');
        });

        // Buat ulang dengan tipe text untuk menyimpan base64
        Schema::table('customers', function (Blueprint $table) {
            $table->text('foto_blob')->nullable()->comment('Base64 encoded image data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('foto_blob');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->binary('foto_blob')->nullable();
        });
    }
};
