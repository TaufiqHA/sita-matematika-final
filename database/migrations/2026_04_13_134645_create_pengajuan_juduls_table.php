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
        Schema::create('pengajuan_juduls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->string('topik');
            $table->string('bidang');
            $table->text('latar_belakang');
            $table->string('file_proposal')->nullable(); // Path file PDF
            $table->enum('status', ['draft', 'diajukan', 'perlu_revisi', 'disetujui', 'ditolak'])->default('diajukan');
            $table->text('catatan_dpa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_juduls');
    }
};
