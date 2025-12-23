<?php

namespace Database\Seeders;

use App\Models\Tuk;
use Illuminate\Database\Seeder;

class TukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tukData = [
            [
                'nama' => 'Laboratorium Enterprise System',
                'kode' => 'TUK1',
                'kategori' => 'Lab',
                'alamat' => 'Jalan RS. Fatmawati Raya, Pd. Labu, Kec. Cilandak, Kota Jakarta Selatan',
            ],
            [
                'nama' => 'Laboratorium Software Engineering',
                'kode' => 'TUK2',
                'kategori' => 'Lab',
                'alamat' => 'Jalan RS. Fatmawati Raya, Pd. Labu, Kec. Cilandak, Kota Jakarta Selatan',
            ],
            [
                'nama' => 'Laboratorium Cyber Security & Networking',
                'kode' => 'TUK3',
                'kategori' => 'Lab',
                'alamat' => 'Jalan RS. Fatmawati Raya, Pd. Labu, Kec. Cilandak, Kota Jakarta Selatan',
            ],
            [
                'nama' => 'Laboratorium Internet of Things (IOT)',
                'kode' => 'TUK4',
                'kategori' => 'Lab',
                'alamat' => 'Jalan RS. Fatmawati Raya, Pd. Labu, Kec. Cilandak, Kota Jakarta Selatan',
            ],
            [
                'nama' => 'Laboratorium Artificial Intelligence & Robotics',
                'kode' => 'TUK5',
                'kategori' => 'Lab',
                'alamat' => 'Jalan RS. Fatmawati Raya, Pd. Labu, Kec. Cilandak, Kota Jakarta Selatan',
            ],
            [
                'nama' => 'Laboratorium Immersive & Multimedia',
                'kode' => 'TUK6',
                'kategori' => 'Lab',
                'alamat' => 'Jalan RS. Fatmawati Raya, Pd. Labu, Kec. Cilandak, Kota Jakarta Selatan',
            ],
            [
                'nama' => 'Laboratorium E-Governance',
                'kode' => 'TUK7',
                'kategori' => 'Lab',
                'alamat' => 'Jalan RS. Fatmawati Raya, Pd. Labu, Kec. Cilandak, Kota Jakarta Selatan',
            ],
            [
                'nama' => 'Laboratorium Big Data & Data Science',
                'kode' => 'TUK8',
                'kategori' => 'Lab',
                'alamat' => 'Jalan RS. Fatmawati Raya, Pd. Labu, Kec. Cilandak, Kota Jakarta Selatan',
            ],
            [
                'nama' => 'FIKLAB 302',
                'kode' => 'TUK9',
                'kategori' => 'Lab',
                'alamat' => 'Jalan RS. Fatmawati Raya, Pd. Labu, Kec. Cilandak, Kota Jakarta Selatan',
            ],
            [
                'nama' => 'Laboratorium B&C Pemrograman',
                'kode' => 'TUK10',
                'kategori' => 'Lab',
                'alamat' => 'Jalan RS. Fatmawati Raya, Pd. Labu, Kec. Cilandak, Kota Jakarta Selatan',
            ],
            [
                'nama' => 'Laboratorium Data Science dan Data Mining',
                'kode' => 'TUK11',
                'kategori' => 'Lab',
                'alamat' => 'Jalan RS. Fatmawati Raya, Pd. Labu, Kec. Cilandak, Kota Jakarta Selatan',
            ],
            [
                'nama' => 'Laboratorium Artificial Intelligence Lt 4 FIKLAB UPNVJ',
                'kode' => 'TUK12',
                'kategori' => 'Lab',
                'alamat' => 'Jalan RS. Fatmawati Raya, Pd. Labu, Kec. Cilandak, Kota Jakarta Selatan',
            ],
            [
                'nama' => 'FIKLAB-401',
                'kode' => 'TUK13',
                'kategori' => 'Lab',
                'alamat' => 'Jalan RS. Fatmawati Raya, Pd. Labu, Kec. Cilandak, Kota Jakarta Selatan',
            ],
        ];

        foreach ($tukData as $data) {
            Tuk::create($data);
        }
    }
}
