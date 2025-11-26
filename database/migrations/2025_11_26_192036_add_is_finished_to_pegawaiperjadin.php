<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawaiperjadin', function (Blueprint $table) {
            // Kolom untuk menandai apakah pegawai ini sudah selesai tugasnya
            // Kita taruh default 0 (false)
            $table->boolean('is_finished')->default(false)->after('role_perjadin');
        });
    }

    public function down(): void
    {
        Schema::table('pegawaiperjadin', function (Blueprint $table) {
            $table->dropColumn('is_finished');
        });
    }
};