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
        // 1. Tambah kolom 'foto' ke tabel geotagging
        Schema::table('geotagging', function (Blueprint $table) {
            // Menyimpan path file foto (nullable karena mungkin tidak wajib atau gagal upload)
            $table->string('foto', 255)->nullable()->after('created_at');
        });

        // 2. Tambah kolom 'selesaikan_manual' ke tabel perjalanandinas
        Schema::table('perjalanandinas', function (Blueprint $table) {
            // Boolean flag (0 = Normal, 1 = Diselesaikan Manual oleh Admin/PIC)
            $table->string('selesaikan_manual, 255')->nullable()->after('id_status');
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::table('geotagging', function (Blueprint $table) {
            $table->dropColumn('foto');
            });

            Schema::table('perjalanandinas', function (Blueprint $table) {
                $table->dropColumn('selesaikan_manual');
            });
    }
};