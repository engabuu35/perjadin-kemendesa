<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// TIDAK ADA PERUBAHAN
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rinciananggaran', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_laporan')->index('id_laporan');
            $table->integer('id_kategori')->index('id_kategori');
            $table->date('tanggal_biaya');
            $table->string('deskripsi_biaya')->nullable();
            $table->decimal('jumlah_biaya', 15);
            $table->string('path_bukti')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rinciananggaran');
    }
};