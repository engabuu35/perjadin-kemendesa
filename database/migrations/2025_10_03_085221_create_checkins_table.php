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
    Schema::create('checkins', function (Blueprint $table) {
        $table->id();
        // Kolom-kolom kunci yang menghubungkan ke data lain
        $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

        // Data Geotagging Inti (dari prototipe teman Anda)
        $table->decimal('latitude', 10, 8);
        $table->decimal('longitude', 11, 8);

        // Data tambahan sesuai rencana kita
        $table->enum('type', ['check-in', 'check-out']);
        $table->boolean('in_radius')->default(false); // Hasil kalkulasi geofence
        $table->text('notes')->nullable(); // Catatan opsional dari pegawai
        $table->string('photo_path')->nullable(); // Path foto jika ada
        
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkins');
    }
};
