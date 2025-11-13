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
        Schema::create('users', function (Blueprint $table) {
            // DIUBAH: Kolom 'id' dihapus
            // $table->integer('id', true);
            
            $table->integer('id_uke')->nullable()->index('id_uke');
            $table->integer('pangkat_gol_id')->nullable()->index('pangkat_gol_id');
            
            // DIUBAH: 'nip' sekarang menjadi primary key
            $table->string('nip', 30)->primary();
            
            $table->string('nama');
            $table->string('email')->unique('email');
            $table->string('no_telp', 20)->nullable();
            $table->string('password_hash');
            $table->boolean('is_aktif')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};