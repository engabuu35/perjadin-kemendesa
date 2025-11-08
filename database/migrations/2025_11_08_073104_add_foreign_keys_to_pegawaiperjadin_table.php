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
        Schema::table('pegawaiperjadin', function (Blueprint $table) {
            $table->foreign(['id_perjadin'], 'pegawaiperjadin_ibfk_1')->references(['id'])->on('perjalanandinas')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['id_user'], 'pegawaiperjadin_ibfk_2')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawaiperjadin', function (Blueprint $table) {
            $table->dropForeign('pegawaiperjadin_ibfk_1');
            $table->dropForeign('pegawaiperjadin_ibfk_2');
        });
    }
};
