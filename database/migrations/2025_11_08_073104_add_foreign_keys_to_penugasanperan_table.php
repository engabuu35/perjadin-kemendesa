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
        Schema::table('penugasanperan', function (Blueprint $table) {
            // DIUBAH: Merujuk ke 'nip' (primary key baru) di tabel 'users'
            $table->foreign(['user_id'], 'penugasanperan_ibfk_1')->references(['nip'])->on('users')->onUpdate('restrict')->onDelete('cascade');
            
            $table->foreign(['role_id'], 'penugasanperan_ibfk_2')->references(['id'])->on('roles')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penugasanperan', function (Blueprint $table) {
            $table->dropForeign('penugasanperan_ibfk_1');
            $table->dropForeign('penugasanperan_ibfk_2');
        });
    }
};