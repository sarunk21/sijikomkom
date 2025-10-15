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
        Schema::create('template_master', function (Blueprint $table) {
            $table->id();
            $table->string('nama_template');
            $table->string('tipe_template'); // APL1, APL2, dll
            $table->unsignedBigInteger('skema_id');
            $table->text('deskripsi')->nullable();
            $table->string('file_path'); // Path ke file .docx
            $table->json('variables'); // JSON untuk menyimpan variable yang bisa diubah
            $table->string('ttd_path')->nullable(); // Path ke file TTD digital
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('skema_id')->references('id')->on('skema')->onDelete('cascade');
            $table->unique(['tipe_template', 'skema_id']); // Satu template per tipe per skema
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_master');
    }
};
