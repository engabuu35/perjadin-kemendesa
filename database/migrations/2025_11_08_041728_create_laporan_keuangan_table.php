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
        Schema::create('laporan_keuangan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_pegawai');
            $table->string('nip', 20);
            $table->decimal('uang_harian', 15);
            $table->decimal('biaya_penginapan', 15);
            $table->decimal('transport', 15);
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
