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
            // Field untuk menyimpan jawaban BK/K (Belum Kompeten/Kompeten)
            $table->string('bk_k_answer')->nullable()->after('kesimpulan');
            // Field untuk menyimpan bukti isian tes
            $table->text('bukti_isian_tes')->nullable()->after('bk_k_answer');
            // Field untuk menyimpan file bukti jika ada
            $table->string('bukti_file_path')->nullable()->after('bukti_isian_tes');
            // Field untuk menyimpan tanda tangan digital
            $table->text('digital_signature')->nullable()->after('bukti_file_path');
            // Field untuk menyimpan timestamp tanda tangan
            $table->timestamp('signature_timestamp')->nullable()->after('digital_signature');
            // Field untuk menyimpan IP address saat tanda tangan
            $table->string('signature_ip')->nullable()->after('signature_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropColumn([
                'bk_k_answer',
                'bukti_isian_tes',
                'bukti_file_path',
                'digital_signature',
                'signature_timestamp',
                'signature_ip'
            ]);
        });
    }
};
