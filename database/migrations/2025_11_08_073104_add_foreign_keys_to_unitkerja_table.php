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
        Schema::table('unitkerja', function (Blueprint $table) {
            $table->foreign(['id_induk'], 'unitkerja_ibfk_1')->references(['id'])->on('unitkerja')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unitkerja', function (Blueprint $table) {
            $table->dropForeign('unitkerja_ibfk_1');
        });
    }
};
