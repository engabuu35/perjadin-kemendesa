<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bukti_laporan', function (Blueprint $table) {
            // Menambah kolom nominal (Big Integer biar muat angka besar)
            // Default 0 biar tidak error kalkulasi
            $table->bigInteger('nominal')->default(0)->after('kategori');
        });
    }

    public function down(): void
    {
        Schema::table('bukti_laporan', function (Blueprint $table) {
            $table->dropColumn('nominal');
        });
    }
};