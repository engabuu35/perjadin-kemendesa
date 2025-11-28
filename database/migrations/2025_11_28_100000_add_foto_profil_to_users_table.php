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
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom foto_profil
            // Tipe STRING (untuk menyimpan path/alamat file)
            // NULLABLE (supaya user lama yang belum punya foto tidak error)
            // Ditaruh setelah kolom 'password_hash' (opsional, biar rapi aja)
            $table->string('foto_profil', 255)->nullable()->after('password_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Perintah untuk menghapus kolom jika migrasi di-rollback
            $table->dropColumn('foto_profil');
        });
    }
};