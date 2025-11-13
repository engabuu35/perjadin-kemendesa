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
        Schema::create('tipegeotagging', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nama_tipe', 50)->unique('nama_tipe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipegeotagging');
    }
};