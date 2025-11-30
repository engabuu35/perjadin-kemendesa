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
        Schema::create('pegawaiperjadin', function (Blueprint $table) {
            // Foreign Key ke tabel surat (perjalanandinas)
            $table->integer('id_perjadin');
            
            // Foreign Key ke user (NIP)
            $table->string('id_user', 30)->index('id_user');
            
            // HANYA PERLU JABATAN DALAM TIM
            // Kolom status_laporan, bukti_transport, dll SUDAH DIHAPUS
            // Karena file sekarang disimpan di tabel induk (perjalanandinas)
            $table->string('role_perjadin', 100)->default('Anggota');
            
            // Primary Key Gabungan (Satu pegawai hanya bisa masuk 1x di surat yang sama)
            $table->primary(['id_perjadin', 'id_user']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawaiperjadin');
    }
};