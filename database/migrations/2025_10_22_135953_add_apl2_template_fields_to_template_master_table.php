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
        Schema::table('template_master', function (Blueprint $table) {
            // Field untuk menyimpan konfigurasi APL2
            $table->json('apl2_config')->nullable()->after('variables');
            // Field untuk menyimpan template soal APL2
            $table->json('apl2_questions')->nullable()->after('apl2_config');
            // Field untuk menyimpan konfigurasi checkbox BK/K
            $table->json('apl2_checkbox_config')->nullable()->after('apl2_questions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_master', function (Blueprint $table) {
            $table->dropColumn(['apl2_config', 'apl2_questions', 'apl2_checkbox_config']);
        });
    }
};
