<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nik')->nullable();
            $table->string('nim')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->unique();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('kebangsaan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('photo_diri')->nullable();
            $table->string('photo_ktp')->nullable();
            $table->string('photo_sertifikat')->nullable();
            $table->string('photo_ktmkhs')->nullable();
            $table->string('photo_administatif')->nullable();
            $table->string('tanda_tangan')->nullable();
            $table->string('user_type')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });


        // Default Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('admin'),
            'user_type' => 'admin',
        ]);

        User::create([
            'name' => 'Asesor',
            'email' => 'asesor@mail.com',
            'password' => Hash::make('asesor'),
            'user_type' => 'asesor',
        ]);

        User::create([
            'name' => 'Kaprodi',
            'email' => 'kaprodi@mail.com',
            'password' => Hash::make('kaprodi'),
            'user_type' => 'kaprodi',
        ]);

        User::create([
            'name' => 'Pimpinan',
            'email' => 'pimpinan@mail.com',
            'password' => Hash::make('pimpinan'),
            'user_type' => 'pimpinan',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
