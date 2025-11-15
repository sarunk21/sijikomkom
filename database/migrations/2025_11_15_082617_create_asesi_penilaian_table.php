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
        Schema::create('asesi_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Asesi
            $table->foreignId('asesor_id')->nullable()->constrained('users')->onDelete('set null'); // Asesor yang menilai

            // Penilaian per formulir
            $table->json('formulir_status')->nullable(); // Status setiap formulir {bank_soal_id: {is_checked, is_valid}}

            // Penilaian FR AI 07 (wajib diisi asesor)
            $table->boolean('fr_ai_07_completed')->default(false);
            $table->json('fr_ai_07_data')->nullable();

            // Hasil akhir
            $table->enum('hasil_akhir', ['belum_dinilai', 'kompeten', 'belum_kompeten'])->default('belum_dinilai');
            $table->text('catatan_asesor')->nullable();

            // Timestamps
            $table->timestamp('penilaian_at')->nullable(); // Waktu asesor memberikan penilaian
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint
            $table->unique(['jadwal_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asesi_penilaian');
    }
};
