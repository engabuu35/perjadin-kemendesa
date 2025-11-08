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
        Schema::table('laporankeuangan', function (Blueprint $table) {
            $table->foreign(['id_perjadin'], 'laporankeuangan_ibfk_1')->references(['id'])->on('perjalanandinas')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['id_status'], 'laporankeuangan_ibfk_2')->references(['id'])->on('statuslaporan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['verified_by'], 'laporankeuangan_ibfk_3')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporankeuangan', function (Blueprint $table) {
            $table->dropForeign('laporankeuangan_ibfk_1');
            $table->dropForeign('laporankeuangan_ibfk_2');
            $table->dropForeign('laporankeuangan_ibfk_3');
        });
    }
};
