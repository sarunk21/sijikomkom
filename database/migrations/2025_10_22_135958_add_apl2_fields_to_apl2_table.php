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
        Schema::table('apl2', function (Blueprint $table) {
            // Field untuk menyimpan konfigurasi soal APL2
            $table->json('question_config')->nullable()->after('question_text');
            // Field untuk menentukan tipe soal (text, checkbox, radio, file)
            $table->string('question_type')->default('text')->after('question_config');
            // Field untuk menyimpan opsi jika tipe checkbox/radio
            $table->json('question_options')->nullable()->after('question_type');
            // Field untuk menyimpan bukti isian tes
            $table->text('bukti_isian_tes')->nullable()->after('question_options');
            // Field untuk menentukan apakah soal ini untuk BK/K
            $table->boolean('is_bk_k_question')->default(false)->after('bukti_isian_tes');
            // Field untuk urutan soal
            $table->integer('urutan')->default(0)->after('is_bk_k_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apl2', function (Blueprint $table) {
            $table->dropColumn([
                'question_config',
                'question_type',
                'question_options',
                'bukti_isian_tes',
                'is_bk_k_question',
                'urutan'
            ]);
        });
    }
};
