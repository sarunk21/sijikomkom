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
        Schema::create('formulir_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Asesi
            $table->foreignId('bank_soal_id')->constrained('bank_soal')->onDelete('cascade');

            // Jawaban asesi (custom fields)
            $table->json('asesi_responses')->nullable(); // Jawaban dari custom fields

            // Penilaian asesor
            $table->json('asesor_responses')->nullable(); // Jawaban asesor untuk custom fields (role: asesor/both)
            $table->json('asesor_validations')->nullable(); // Validasi sesuai/tidak sesuai per pertanyaan
            $table->boolean('is_asesor_completed')->default(false); // Sudah diperiksa asesor atau belum

            // Status dan catatan
            $table->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft');
            $table->text('catatan_asesor')->nullable();

            // Timestamps
            $table->timestamp('submitted_at')->nullable(); // Waktu asesi submit
            $table->timestamp('reviewed_at')->nullable(); // Waktu asesor review
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['jadwal_id', 'user_id', 'bank_soal_id']);
            $table->unique(['jadwal_id', 'user_id', 'bank_soal_id'], 'unique_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulir_responses');
    }
};
