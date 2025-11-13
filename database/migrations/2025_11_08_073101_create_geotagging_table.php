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
        Schema::create('geotagging', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_perjadin')->index('id_perjadin');
            // DIUBAH: dari integer ke string(30) untuk mencocokkan NIP
            $table->string('id_user', 30)->index('id_user');
            $table->integer('id_tipe')->index('id_tipe');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geotagging');
    }
};