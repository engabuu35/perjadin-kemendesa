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
        Schema::create('pangkatgolongan', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('kode_golongan', 10)->unique('kode_golongan');
            $table->string('nama_pangkat', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pangkatgolongan');
    }
};
