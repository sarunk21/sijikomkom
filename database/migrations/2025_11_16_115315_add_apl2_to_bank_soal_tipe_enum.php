<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify enum to add APL2
        DB::statement("ALTER TABLE bank_soal MODIFY COLUMN tipe ENUM('FR AI 03', 'FR AI 06', 'FR AI 07', 'APL2') NOT NULL");

        // Also make file_path and original_filename nullable for APL2
        DB::statement("ALTER TABLE bank_soal MODIFY COLUMN file_path VARCHAR(255) NULL");
        DB::statement("ALTER TABLE bank_soal MODIFY COLUMN original_filename VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove APL2 from enum
        DB::statement("ALTER TABLE bank_soal MODIFY COLUMN tipe ENUM('FR AI 03', 'FR AI 06', 'FR AI 07') NOT NULL");

        // Revert file_path and original_filename to NOT NULL
        DB::statement("ALTER TABLE bank_soal MODIFY COLUMN file_path VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE bank_soal MODIFY COLUMN original_filename VARCHAR(255) NOT NULL");
    }
};
