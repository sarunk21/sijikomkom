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
        // Add custom_data field to apl2 table
        Schema::table('apl2', function (Blueprint $table) {
            $table->text('custom_data')->nullable()->after('urutan');
        });

        // Add custom_response field to responses table
        Schema::table('responses', function (Blueprint $table) {
            $table->text('custom_response')->nullable()->after('asesor_signature_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove custom_data field from apl2 table
        Schema::table('apl2', function (Blueprint $table) {
            $table->dropColumn('custom_data');
        });

        // Remove custom_response field from responses table
        Schema::table('responses', function (Blueprint $table) {
            $table->dropColumn('custom_response');
        });
    }
};
