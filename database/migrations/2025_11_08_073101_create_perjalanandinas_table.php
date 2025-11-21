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
        Schema::create('perjalanandinas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('id_pembuat', 30)->index('id_pembuat');
            $table->integer('id_status')->index('id_status');
            $table->string('approved_by', 30)->nullable()->index('approved_by');
            $table->timestamp('approved_at')->nullable();
            
            $table->string('nomor_surat', 100)->unique('nomor_surat');
            $table->date('tanggal_surat');
            $table->string('tujuan');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->text('hasil_perjadin')->nullable();
            
            // --- KOLOM BARU (PINDAHAN DARI TABEL PEGAWAI) ---
            // Tempat menyimpan file PDF gabungan untuk satu tim
            $table->text('laporan_akhir')->nullable(); 
            $table->string('file_laporan_kegiatan', 255)->nullable();
            $table->string('file_bukti_transport', 255)->nullable();
            $table->string('file_bukti_penginapan', 255)->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perjalanandinas');
    }
};