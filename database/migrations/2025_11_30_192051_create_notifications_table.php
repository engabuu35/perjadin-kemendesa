<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->foreign('user_id')->references('nip')->on('users')->onDelete('cascade');
            $table->string('type')->comment('jenis notifikasi: assignment, geotagging, report, etc');
            $table->string('category')->comment('kategori: preparation, execution, reporting, financial, monitoring, system');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable()->comment('data dinamis: lokasi, nomor ST, nominal, dll');
            $table->json('recipient_roles')->nullable()->comment('role yang menerima: pegawai, pimpinan, pic, ppk');
            $table->timestamp('read_at')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->default('blue')->comment('warna badge: blue, yellow, red, green, orange');
            $table->string('action_url')->nullable()->comment('URL untuk action button');
            $table->string('priority')->default('normal')->comment('normal, high, urgent');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['type', 'category']);
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
