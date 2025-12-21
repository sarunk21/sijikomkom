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
        // Drop kolom 'nama' dari tabel bank_soal
        if (Schema::hasColumn('bank_soal', 'nama')) {
            Schema::table('bank_soal', function (Blueprint $table) {
                $table->dropColumn('nama');
            });
        }

        // Drop kolom 'nama_template' dari tabel template_master
        if (Schema::hasColumn('template_master', 'nama_template')) {
            Schema::table('template_master', function (Blueprint $table) {
                $table->dropColumn('nama_template');
            });
        }

        // Drop tabel pembayaran_asesor (tidak dipakai)
        Schema::dropIfExists('pembayaran_asesor');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore kolom 'nama' di tabel bank_soal
        if (!Schema::hasColumn('bank_soal', 'nama')) {
            Schema::table('bank_soal', function (Blueprint $table) {
                $table->string('nama')->after('skema_id')->comment('Nama bank soal / formulir');
            });
        }

        // Restore kolom 'nama_template' di tabel template_master
        if (!Schema::hasColumn('template_master', 'nama_template')) {
            Schema::table('template_master', function (Blueprint $table) {
                $table->string('nama_template')->after('id');
            });
        }

        // Restore tabel pembayaran_asesor
        if (!Schema::hasTable('pembayaran_asesor')) {
            Schema::create('pembayaran_asesor', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asesor_id')->constrained('users');
                $table->foreignId('jadwal_id')->constrained('jadwal');
                $table->string('bukti_pembayaran')->nullable();
                $table->integer('status')->default(1);
                $table->timestamps();
            });
        }
    }
};

