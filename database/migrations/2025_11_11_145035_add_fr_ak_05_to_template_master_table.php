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
            $table->string('fr_ak_05_file_path')->nullable()->after('file_path');
            $table->json('fr_ak_05_variables')->nullable()->after('fr_ak_05_file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_master', function (Blueprint $table) {
            $table->dropColumn(['fr_ak_05_file_path', 'fr_ak_05_variables']);
        });
    }
};
