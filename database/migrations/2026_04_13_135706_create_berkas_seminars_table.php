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
        Schema::create('berkas_seminars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran_seminars')->cascadeOnDelete();
            $table->string('nama_berkas'); // Contoh: 'KRS', 'Kwitansi', 'Lembar Persetujuan'
            $table->string('file_path');   // Path file
            $table->string('status_verifikasi')->default('pending'); // pending, valid, tidak_valid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berkas_seminars');
    }
};
