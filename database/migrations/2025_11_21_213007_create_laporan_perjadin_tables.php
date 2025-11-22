<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Laporan (Uraian per User)
        Schema::create('laporan_perjadin', function (Blueprint $table) {
            $table->id();
            $table->integer('id_perjadin');
            $table->string('id_user', 30); // NIP Pegawai
            $table->text('uraian')->nullable(); // Bisa null kalau cuma simpan draft
            $table->boolean('is_final')->default(false); // Penanda apakah sudah "Selesai" atau masih "Draft"
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_perjadin')->references('id')->on('perjalanandinas')->onDelete('cascade');
            $table->foreign('id_user')->references('nip')->on('users')->onDelete('cascade');
        });

        // 2. Tabel Bukti (File-file biaya/kegiatan)
        Schema::create('bukti_laporan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_laporan'); // Connect ke tabel di atas
            $table->string('nama_file');
            $table->string('path_file');
            $table->string('kategori')->nullable(); // Transport, Penginapan, dll
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