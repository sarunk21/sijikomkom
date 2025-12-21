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
            $table->tinyInteger('kelayakan_status')->default(0)->after('status')
                ->comment('0=Belum Diperiksa, 1=Layak, 2=Tidak Layak');
            $table->text('kelayakan_catatan')->nullable()->after('kelayakan_status');
            $table->timestamp('kelayakan_verified_at')->nullable()->after('kelayakan_catatan');
            $table->foreignId('kelayakan_verified_by')->nullable()->after('kelayakan_verified_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->dropForeign(['kelayakan_verified_by']);
            $table->dropColumn([
                'kelayakan_status',
                'kelayakan_catatan',
                'kelayakan_verified_at',
                'kelayakan_verified_by'
            ]);
        });
    }
};

