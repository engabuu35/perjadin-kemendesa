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
        Schema::create('pegawaiperjadin', function (Blueprint $table) {
            $table->integer('id_perjadin');
            // DIUBAH: dari integer ke string(30) untuk mencocokkan NIP
            $table->string('id_user', 30)->index('id_user');
            $table->string('role_perjadin', 100)->nullable();
            $table->boolean('is_lead')->default(false);
            $table->text('laporan_individu')->nullable();

            $table->primary(['id_perjadin', 'id_user']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawaiperjadin');
    }
};