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
        Schema::create('laporankeuangan', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_perjadin')->index('id_perjadin');
            $table->integer('id_status')->index('id_status');
            // DIUBAH: dari integer ke string(30) untuk mencocokkan NIP
            $table->string('verified_by', 30)->nullable()->index('verified_by');
            $table->timestamp('verified_at')->nullable();
            $table->string('nomor_spm', 100)->nullable();
            $table->date('tanggal_spm')->nullable();
            $table->string('nomor_sp2d', 100)->nullable();
            $table->date('tanggal_sp2d')->nullable();
            $table->decimal('biaya_rampung', 15)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporankeuangan');
    }
};