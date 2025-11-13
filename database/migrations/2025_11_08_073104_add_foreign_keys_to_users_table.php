<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// TIDAK ADA PERUBAHAN
// File ini menambahkan foreign key DARI tabel users KE tabel lain,
// jadi tidak terpengaruh oleh perubahan primary key di 'users'.
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign(['id_uke'], 'users_ibfk_1')->references(['id'])->on('unitkerja')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['pangkat_gol_id'], 'users_ibfk_2')->references(['id'])->on('pangkatgolongan')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_ibfk_1');
            $table->dropForeign('users_ibfk_2');
        });
    }
};