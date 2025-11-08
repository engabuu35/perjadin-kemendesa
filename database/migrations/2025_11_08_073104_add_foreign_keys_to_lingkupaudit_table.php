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
        Schema::table('lingkupaudit', function (Blueprint $table) {
            $table->foreign(['unit_kerja_id'], 'lingkupaudit_ibfk_1')->references(['id'])->on('unitkerja')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lingkupaudit', function (Blueprint $table) {
            $table->dropForeign('lingkupaudit_ibfk_1');
        });
    }
};
