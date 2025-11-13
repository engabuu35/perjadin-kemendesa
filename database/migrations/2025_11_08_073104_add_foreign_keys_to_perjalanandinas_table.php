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
            // DIUBAH: Merujuk ke 'nip' (primary key baru) di tabel 'users'
            $table->foreign(['id_pembuat'], 'perjalanandinas_ibfk_1')->references(['nip'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            
            $table->foreign(['id_status'], 'perjalanandinas_ibfk_2')->references(['id'])->on('statusperjadin')->onUpdate('restrict')->onDelete('restrict');
            
            // DIUBAH: Merujuk ke 'nip' (primary key baru) di tabel 'users'
            $table->foreign(['approved_by'], 'perjalanandinas_ibfk_3')->references(['nip'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perjalanandinas', function (Blueprint $table) {
            $table->dropForeign('perjalanandinas_ibfk_1');
            $table->dropForeign('perjalanandinas_ibfk_2');
            $table->dropForeign('perjalanandinas_ibfk_3');
        });
    }
};