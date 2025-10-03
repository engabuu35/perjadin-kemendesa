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
    Schema::create('trips', function (Blueprint $table) {
        // Ini adalah cetak biru untuk setiap kolom di tabel trips
        $table->id(); // Kolom ID otomatis
        
        // Kunci asing ke tabel users
        $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
        
        $table->string('nomor_surat')->unique(); // Kolom teks untuk nomor surat, harus unik
        $table->text('tujuan'); // Kolom teks panjang untuk tujuan
        $table->decimal('lat', 10, 8); // Kolom angka desimal untuk Latitude
        $table->decimal('lon', 11, 8); // Kolom angka desimal untuk Longitude
        $table->dateTime('start_date'); // Kolom tanggal dan waktu mulai
        $table->dateTime('end_date'); // Kolom tanggal dan waktu selesai
        
        // Status perjalanan, defaultnya 'draft'
        $table->enum('status', ['draft', 'berjalan', 'selesai', 'dibatalkan'])->default('draft');
        
        $table->timestamps(); // Otomatis membuat kolom created_at dan updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
