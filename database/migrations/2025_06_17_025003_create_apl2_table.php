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
        Schema::create('apl2', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skema_id')->constrained('skema');
            $table->string('link_ujikom_asesor')->nullable();
            $table->string('link_ujikom_asesi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apl2');
    }
};
