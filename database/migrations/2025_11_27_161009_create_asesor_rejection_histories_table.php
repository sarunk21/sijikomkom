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
        Schema::create('asesor_rejection_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pendaftaran_id');
            $table->unsignedBigInteger('jadwal_id');
            $table->unsignedBigInteger('asesor_id');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('pendaftaran_id')->references('id')->on('pendaftaran')->onDelete('cascade');
            $table->foreign('jadwal_id')->references('id')->on('jadwal')->onDelete('cascade');
            $table->foreign('asesor_id')->references('id')->on('users')->onDelete('cascade');

            // Index for faster queries
            $table->index(['pendaftaran_id', 'asesor_id']);
            $table->index('jadwal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asesor_rejection_histories');
    }
};
