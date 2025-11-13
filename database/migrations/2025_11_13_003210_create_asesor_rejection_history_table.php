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
        Schema::create('asesor_rejection_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->comment('ID pendaftaran yang ditolak');
            $table->foreignId('jadwal_id')->constrained('jadwal')->comment('ID jadwal');
            $table->foreignId('asesor_id')->constrained('users')->comment('ID asesor yang menolak');
            $table->text('notes')->nullable()->comment('Alasan penolakan');
            $table->timestamps();

            // Index untuk performa query
            $table->index(['pendaftaran_id', 'asesor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asesor_rejection_history');
    }
};
