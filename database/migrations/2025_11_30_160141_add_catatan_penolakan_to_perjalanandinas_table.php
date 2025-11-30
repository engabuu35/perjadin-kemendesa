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
        Schema::table('perjalanandinas', function (Blueprint $table) {
            // Tambahkan kolom TEXT yang boleh kosong (nullable)
            $table->text('catatan_penolakan')->nullable()->after('id_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perjalanandinas', function (Blueprint $table) {
            $table->dropColumn('catatan_penolakan');
        });
    }
};