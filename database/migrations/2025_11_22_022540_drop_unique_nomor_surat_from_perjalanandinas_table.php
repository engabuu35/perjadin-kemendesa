<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hati-hati: pastikan nama index benar. Dari SHOW INDEX, nama index = 'nomor_surat'
        Schema::table('perjalanandinas', function (Blueprint $table) {
            // dropUnique menerima nama index (bukan kolom) di Laravel
            $table->dropUnique('nomor_surat');
        });
    }

    public function down(): void
    {
        Schema::table('perjalanandinas', function (Blueprint $table) {
            // mengembalikan unique index jika rollback
            $table->unique('nomor_surat', 'nomor_surat');
        });
    }
};
