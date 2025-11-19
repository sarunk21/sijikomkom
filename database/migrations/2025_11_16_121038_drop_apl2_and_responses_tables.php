<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop foreign keys first if any
        try {
            DB::statement('ALTER TABLE responses DROP FOREIGN KEY responses_pendaftaran_id_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist
        }

        try {
            DB::statement('ALTER TABLE responses DROP FOREIGN KEY responses_apl2_id_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist
        }

        try {
            DB::statement('ALTER TABLE apl2 DROP FOREIGN KEY apl2_skema_id_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist
        }

        // Drop tables
        Schema::dropIfExists('responses');
        Schema::dropIfExists('apl2');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot rollback - data already migrated to bank_soal
        // This is a one-way migration
    }
};
