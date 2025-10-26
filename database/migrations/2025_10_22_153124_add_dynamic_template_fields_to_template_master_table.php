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
            // Field untuk menyimpan konfigurasi field dinamis
            $table->json('field_configurations')->nullable()->after('apl2_checkbox_config');

            // Field untuk menyimpan mapping field ke database
            $table->json('field_mappings')->nullable()->after('field_configurations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_master', function (Blueprint $table) {
            $table->dropColumn(['field_configurations', 'field_mappings']);
        });
    }
};
