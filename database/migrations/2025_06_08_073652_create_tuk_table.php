<?php

use App\Models\Tuk;
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
        Schema::create('tuk', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode');
            $table->string('kategori');
            $table->string('alamat');
            $table->timestamps();
            $table->softDeletes();
        });

        Tuk::create([
            'nama' => 'TUK 1',
            'kode' => 'TUK1',
            'kategori' => 'Lab',
            'alamat' => 'Jl. Raya No. 1',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuk');
    }
};
