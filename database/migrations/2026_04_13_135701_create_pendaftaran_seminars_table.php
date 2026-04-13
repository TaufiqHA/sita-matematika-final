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
        Schema::create('pendaftaran_seminars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $table->enum('jenis_seminar', ['proposal', 'hasil', 'munaqasyah']);
            $table->enum('status', ['diajukan', 'perlu_revisi', 'diverifikasi', 'dijadwalkan', 'selesai'])->default('diajukan');
            $table->text('catatan_tu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_seminars');
    }
};
