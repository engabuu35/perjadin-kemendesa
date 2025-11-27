<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bukti_laporan', function (Blueprint $table) {
            // Kolom untuk menyimpan teks (Hotel, Maskapai, Kota, dll)
            // Tambah kolom baru (spesifik)
            $table->string('keterangan')->nullable()->after('nominal');
            
            // Kita pastikan nominal defaultnya 0 biar tidak error kalau cuma input teks
            $table->bigInteger('nominal')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('bukti_laporan', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};