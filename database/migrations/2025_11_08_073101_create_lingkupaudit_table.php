<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// TIDAK ADA PERUBAHAN
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lingkupaudit', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('unit_kerja_id')->index('unit_kerja_id');
            $table->string('nama_auditi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lingkupaudit');
    }
};