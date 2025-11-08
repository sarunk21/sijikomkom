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
            // Only add columns that don't exist yet
            if (!Schema::hasColumn('pendaftaran', 'ttd_asesor_path')) {
                $table->string('ttd_asesor_path')->nullable()->after('ttd_asesi_path');
            }
            if (!Schema::hasColumn('pendaftaran', 'asesor_data')) {
                $table->json('asesor_data')->nullable()->after('asesor_assessment');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table) {
            if (Schema::hasColumn('pendaftaran', 'ttd_asesor_path')) {
                $table->dropColumn('ttd_asesor_path');
            }
            if (Schema::hasColumn('pendaftaran', 'asesor_data')) {
                $table->dropColumn('asesor_data');
            }
        });
    }
};
