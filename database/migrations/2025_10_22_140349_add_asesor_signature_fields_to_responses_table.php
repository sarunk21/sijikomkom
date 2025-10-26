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
        Schema::table('responses', function (Blueprint $table) {
            // Field untuk menyimpan tanda tangan digital asesor
            $table->text('asesor_signature')->nullable()->after('signature_ip');
            // Field untuk menyimpan timestamp tanda tangan asesor
            $table->timestamp('asesor_signature_timestamp')->nullable()->after('asesor_signature');
            // Field untuk menyimpan IP address saat tanda tangan asesor
            $table->string('asesor_signature_ip')->nullable()->after('asesor_signature_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropColumn([
                'asesor_signature',
                'asesor_signature_timestamp',
                'asesor_signature_ip'
            ]);
        });
    }
};
