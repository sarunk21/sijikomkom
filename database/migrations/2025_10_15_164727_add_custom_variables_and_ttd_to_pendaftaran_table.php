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
        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->json('custom_variables')->nullable()->after('status'); // Custom variables dari asesi
            $table->string('ttd_asesi_path')->nullable()->after('custom_variables'); // Path TTD digital asesi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->dropColumn(['custom_variables', 'ttd_asesi_path']);
        });
    }
};
