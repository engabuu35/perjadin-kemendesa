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
        Schema::table('geotagging', function (Blueprint $table) {
            $table->foreign(['id_perjadin'], 'geotagging_ibfk_1')->references(['id'])->on('perjalanandinas')->onUpdate('restrict')->onDelete('cascade');
            
            // DIUBAH: Merujuk ke 'nip' (primary key baru) di tabel 'users'
            $table->foreign(['id_user'], 'geotagging_ibfk_2')->references(['nip'])->on('users')->onUpdate('restrict')->onDelete('cascade');
            
            $table->foreign(['id_tipe'], 'geotagging_ibfk_3')->references(['id'])->on('tipegeotagging')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('geotagging', function (Blueprint $table) {
            $table->dropForeign('geotagging_ibfk_1');
            $table->dropForeign('geotagging_ibfk_2');
            $table->dropForeign('geotagging_ibfk_3');
        });
    }
};