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
            // Ganti nama kolom hasil_perjadin menjadi uraian
            if (Schema::hasColumn('perjalanandinas', 'hasil_perjadin')) {
                $table->renameColumn('hasil_perjadin', 'uraian');
            }

            // Hapus kolom yang tidak dipakai
            if (Schema::hasColumn('perjalanandinas', 'laporan_akhir')) {
                $table->dropColumn('laporan_akhir');
            }
            if (Schema::hasColumn('perjalanandinas', 'file_laporan_kegiatan')) {
                $table->dropColumn('file_laporan_kegiatan');
            }
            if (Schema::hasColumn('perjalanandinas', 'file_bukti_penginapan')) {
                $table->dropColumn('file_bukti_penginapan');
            }

            // Ganti nama kolom file_bukti_transport menjadi pdf_keuangan
            if (Schema::hasColumn('perjalanandinas', 'file_bukti_transport')) {
                $table->renameColumn('file_bukti_transport', 'pdf_keuangan');
            }

            // Tambah kolom baru
            $table->string('surat_tugas', 255)->nullable()->after('uraian'); 
            $table->unsignedBigInteger('id_atasan')->nullable()->after('surat_tugas')->index();
            $table->date('tgl_acc')->nullable()->after('id_atasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perjalanandinas', function (Blueprint $table) {
            // Balikkan perubahan
            if (Schema::hasColumn('perjalanandinas', 'uraian')) {
                $table->renameColumn('uraian', 'hasil_perjadin');
            }
            if (!Schema::hasColumn('perjalanandinas', 'laporan_akhir')) {
                $table->text('laporan_akhir')->nullable();
            }
            if (!Schema::hasColumn('perjalanandinas', 'file_laporan_kegiatan')) {
                $table->string('file_laporan_kegiatan', 255)->nullable();
            }
            if (!Schema::hasColumn('perjalanandinas', 'file_bukti_penginapan')) {
                $table->string('file_bukti_penginapan', 255)->nullable();
            }
            if (Schema::hasColumn('perjalanandinas', 'pdf_keuangan')) {
                $table->renameColumn('pdf_keuangan', 'file_bukti_transport');
            }

            // Hapus kolom baru
            if (Schema::hasColumn('perjalanandinas', 'surat_tugas')) {
                $table->dropColumn('surat_tugas');
            }
            if (Schema::hasColumn('perjalanandinas', 'id_atasan')) {
                $table->dropColumn('id_atasan');
            }
            if (Schema::hasColumn('perjalanandinas', 'tgl_acc')) {
                $table->dropColumn('tgl_acc');
            }
        });
    }
};
