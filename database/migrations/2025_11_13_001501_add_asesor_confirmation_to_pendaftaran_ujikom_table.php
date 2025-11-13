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
        Schema::table('pendaftaran_ujikom', function (Blueprint $table) {
            $table->boolean('asesor_confirmed')->default(false)->after('asesor_id')->comment('Apakah asesor sudah konfirmasi ketersediaan untuk jadwal ini');
            $table->timestamp('asesor_confirmed_at')->nullable()->after('asesor_confirmed')->comment('Tanggal asesor konfirmasi');
            $table->text('asesor_notes')->nullable()->after('asesor_confirmed_at')->comment('Catatan dari asesor jika tidak bisa hadir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_ujikom', function (Blueprint $table) {
            $table->dropColumn(['asesor_confirmed', 'asesor_confirmed_at', 'asesor_notes']);
        });
    }
};
