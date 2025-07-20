<?php

namespace Database\Seeders;

use App\Models\Skema;
use Illuminate\Database\Seeder;

class SkemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skemaData = [
            [
                'nama' => 'Analis Program',
                'kode' => 'AP',
                'kategori' => 'Sertifikasi',
                'bidang' => 'S1 Sistem Informasi',
            ],
            [
                'nama' => 'Asisten Pemrograman',
                'kode' => 'ASP',
                'kategori' => 'Sertifikasi',
                'bidang' => 'S1 Teknik Informatika',
            ],
            [
                'nama' => 'Cyber Security Analyst',
                'kode' => 'CSA',
                'kategori' => 'Sertifikasi',
                'bidang' => 'S1 Teknik Informatika',
            ],
            [
                'nama' => 'Designer Multimedia Madya',
                'kode' => 'DMM',
                'kategori' => 'Sertifikasi',
                'bidang' => 'S1 Sistem Informasi',
            ],
            [
                'nama' => 'Junior Associate Data Enginer',
                'kode' => 'JADE',
                'kategori' => 'Sertifikasi',
                'bidang' => 'D3 Sistem Informasi',
            ],
            [
                'nama' => 'Junior Associate Data Scientist',
                'kode' => 'JADS',
                'kategori' => 'Sertifikasi',
                'bidang' => 'D3 Sistem Informasi',
            ],
            [
                'nama' => 'Junior Web Programmer',
                'kode' => 'JWP',
                'kategori' => 'Sertifikasi',
                'bidang' => 'D3 Sistem Informasi',
            ],
            [
                'nama' => 'Pemrograman Basisdata',
                'kode' => 'PB',
                'kategori' => 'Sertifikasi',
                'bidang' => 'S1 Teknik Informatika',
            ],
            [
                'nama' => 'System Analyst',
                'kode' => 'SA',
                'kategori' => 'Sertifikasi',
                'bidang' => 'S1 Sistem Informasi',
            ],
        ];

        foreach ($skemaData as $data) {
            Skema::create($data);
        }
    }
}
