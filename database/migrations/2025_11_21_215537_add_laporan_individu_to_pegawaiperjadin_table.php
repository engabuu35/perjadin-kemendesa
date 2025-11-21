<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawaiperjadin', function (Blueprint $table) {
            // Tambahkan kolom laporan_individu
            $table->text('laporan_individu')->nullable()->after('is_lead');
        });
    }

    public function down(): void
    {
        Schema::table('pegawaiperjadin', function (Blueprint $table) {
            $table->dropColumn('laporan_individu');
        });
    }
};
