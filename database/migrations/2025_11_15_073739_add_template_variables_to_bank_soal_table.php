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
        Schema::table('bank_soal', function (Blueprint $table) {
            $table->json('variables')->nullable()->after('keterangan');
            $table->json('field_configurations')->nullable()->after('variables');
            $table->json('field_mappings')->nullable()->after('field_configurations');
            $table->json('custom_variables')->nullable()->after('field_mappings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_soal', function (Blueprint $table) {
            $table->dropColumn(['variables', 'field_configurations', 'field_mappings', 'custom_variables']);
        });
    }
};
