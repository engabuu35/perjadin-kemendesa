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
        // Perintah untuk membuat tabel 'laporan_keuangan'
        Schema::create('laporan_keuangan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pegawai');
            $table->string('nip', 20); // NIP seringkali butuh tipe string
            $table->decimal('uang_harian', 15, 2); // Menggunakan decimal untuk presisi mata uang
            $table->decimal('biaya_penginapan', 15, 2);
            $table->decimal('transport', 15, 2);
            $table->string('nama_hotel');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_keuangan');
    }
};
