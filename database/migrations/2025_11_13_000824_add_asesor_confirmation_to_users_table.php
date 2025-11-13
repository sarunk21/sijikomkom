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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_asesor_active')->default(true)->after('user_type')->comment('Status aktif asesor - bisa ditugaskan atau tidak');
            $table->timestamp('asesor_confirmed_at')->nullable()->after('is_asesor_active')->comment('Tanggal konfirmasi sebagai asesor');
            $table->unsignedBigInteger('confirmed_by')->nullable()->after('asesor_confirmed_at')->comment('User ID yang mengkonfirmasi asesor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_asesor_active', 'asesor_confirmed_at', 'confirmed_by']);
        });
    }
};
