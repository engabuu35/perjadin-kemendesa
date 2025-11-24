<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Laporan
        Schema::create('laporan_perjadin', function (Blueprint $table) {
            $table->id();
            $table->integer('id_perjadin');
            $table->string('id_user', 30); 
            $table->text('uraian')->nullable(); 
            $table->boolean('is_final')->default(false); 
            $table->timestamps();

            $table->foreign('id_perjadin')->references('id')->on('perjalanandinas')->onDelete('cascade');
            $table->foreign('id_user')->references('nip')->on('users')->onDelete('cascade');
        });

        // 2. Tabel Bukti
        Schema::create('bukti_laporan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_laporan'); 
            
            // --- PERBAIKAN DI SINI ---
            // Tambahkan ->nullable() agar bisa simpan tanpa upload file
            $table->string('nama_file')->nullable(); 
            $table->string('path_file')->nullable();
            // -------------------------
            
            $table->string('kategori')->nullable();
            
            // Tambahkan nominal langsung disini biar ga perlu migrasi tambahan
            $table->bigInteger('nominal')->default(0); 
            
            $table->timestamps();

            $table->foreign('id_laporan')->references('id')->on('laporan_perjadin')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_laporan');
        Schema::dropIfExists('laporan_perjadin');
    }
};