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
    Schema::create('trip_members', function (Blueprint $table) {
        $table->id();
        
        // Menghubungkan ke perjalanan dinas mana
        $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
        
        // Menghubungkan ke pegawai mana yang ikut
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        $table->string('jabatan_saat_perdin'); // e.g., 'Peserta', 'Ketua Tim'
        $table->boolean('is_lead')->default(false); // Penanda apakah dia ketua rombongan
        
        // Memastikan satu user tidak bisa didaftarkan dua kali di trip yang sama
        $table->unique(['trip_id', 'user_id']);
        
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_members');
    }
};
