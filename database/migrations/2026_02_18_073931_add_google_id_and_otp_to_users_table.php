<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // kolom id_google
            $table->string('id_google', 256)->nullable()->after('email');

            // kolom otp
            $table->string('otp', 6)->nullable()->after('password');

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn('id_google');
            $table->dropColumn('otp');

        });
    }
};
