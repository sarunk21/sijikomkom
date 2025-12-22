<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call Seeders
        $this->call([
            TukSeeder::class,
            SkemaSeeder::class,
            SystemAnalystTemplateSeeder::class,
            SystemAnalystBankSoalSeeder::class,
            AsesiSeeder::class,
            AsesorSeeder::class,
            SystemAnalystUjikomSeeder::class,
            SystemAnalystJuliSeeder::class,
            AnalisProgramDesemberSeeder::class,
            AsistenPemrogramanDesemberSeeder::class,
        ]);
    }
}
