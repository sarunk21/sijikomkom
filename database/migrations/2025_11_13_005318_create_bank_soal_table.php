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
        Schema::create('bank_soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skema_id')->constrained('skema')->comment('ID skema sertifikasi');
            $table->string('nama')->comment('Nama bank soal / formulir');
            $table->enum('tipe', ['FR AI 03', 'FR AI 06', 'FR AI 07'])->comment('Tipe formulir: FR AI 03, FR AI 06 (untuk asesi), FR AI 07 (untuk asesor)');
            $table->enum('target', ['asesi', 'asesor'])->comment('Target penggunaan: asesi atau asesor');
            $table->string('file_path')->comment('Path file yang diupload');
            $table->string('original_filename')->comment('Nama file asli');
            $table->boolean('is_active')->default(true)->comment('Status aktif/nonaktif');
            $table->text('keterangan')->nullable()->comment('Keterangan tambahan');
            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa query
            $table->index(['skema_id', 'tipe', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_soal');
    }
};
