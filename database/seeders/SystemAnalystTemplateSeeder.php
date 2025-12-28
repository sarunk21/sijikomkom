<?php

namespace Database\Seeders;

use App\Models\TemplateMaster;
use App\Models\Skema;
use Illuminate\Database\Seeder;

class SystemAnalystTemplateSeeder extends Seeder
{
    /**
     * Konfigurasi skema yang perlu template APL1 dan FR_AK_05 (sama untuk semua skema)
     * Tambahkan skema baru di sini untuk auto-generate template APL1 dan FR_AK_05
     */
    private array $commonSchemas = [
        ['kode' => 'SA', 'nama' => 'System Analyst', 'file_slug' => 'system-analyst'],
        ['kode' => 'AP', 'nama' => 'Analis Program', 'file_slug' => 'analis-program'],
        ['kode' => 'ASP', 'nama' => 'Asisten Pemrograman', 'file_slug' => 'asisten-pemrograman'],
        ['kode' => 'JADE', 'nama' => 'Junior Associate Data Engineer', 'file_slug' => 'data-engineer'],
        ['kode' => 'JADS', 'nama' => 'Junior Associate Data Scientist', 'file_slug' => 'data-scientist'],
        ['kode' => 'CSA', 'nama' => 'Cyber Security Analyst', 'file_slug' => 'cyber-security-analyst'],
        ['kode' => 'DMM', 'nama' => 'Designer Multimedia Madya', 'file_slug' => 'designer-multimedia-madya'],
        ['kode' => 'JWP', 'nama' => 'Junior Web Programmer', 'file_slug' => 'junior-web-programmer'],
        ['kode' => 'PB', 'nama' => 'Pemrograman Basisdata', 'file_slug' => 'pemrograman-basisdata'],
    ];

    /**
     * Konfigurasi pertanyaan APL2 per skema (beda-beda untuk setiap skema)
     * Tambahkan konfigurasi skema baru dengan pertanyaannya masing-masing
     */
    private array $apl2Schemas = [
        'SA' => [
            'file_slug' => 'system-analyst',
            'question_format' => 'radio', // Format radio BK/K
            'questions' => [
                [
                    'name' => 'pertanyaan_1',
                    'label' => 'Elemen: Membuat berbagai operasi terhadap basis data • Kriteria Unjuk Kerja: 1.1 Data dapat disimpan/diubah ke dalam format basis data 1.2 Informasi yang diinginkan dapat dihasilkan menggunakan query tersebut 1.3 Indeks dipergunakan untuk mempercepat akses',
                ],
                [
                    'name' => 'pertanyaan_2',
                    'label' => 'Elemen: Membuat prosedur akses terhadap basis data • Kriteria Unjuk Kerja: 2.1 Library akses basis data dapat diterapkan 2.2 Perintah akses data yang relevan dengan teknologi atau jenis baru data, diterapkan untuk mengakses data',
                ],
                [
                    'name' => 'pertanyaan_3',
                    'label' => 'Elemen: Membuat Koneksi basis data • Kriteria Unjuk Kerja: 3.1 Teknologi koneksi yang sesuai dipilih 3.2 Keamanan koneksi ditentukan 3.3 Hak setiap pengguna ditentukan',
                ],
                [
                    'name' => 'pertanyaan_4',
                    'label' => 'Elemen: Menguji program basis data • Kriteria Unjuk Kerja: 4.1 Skenario pengujian disiapkan 4.2 Logika pemrograman mengacu pada kinerja statement akses data yang akan dibaca 4.3 Performansi mengacu pada kinerja statement akses data yang akan dibaca diuji.',
                ],
                [
                    'name' => 'pertanyaan_5',
                    'label' => 'Elemen: Menentukan kebutuhan uji coba dalam pengembangan • Kriteria Unjuk Kerja: 1.1 Prosedur uji coba aplikasi diidentifikasikan sesuai dengan software development life cycle. 1.2 Tools uji coba ditentukan. 1.3 Standar dan kondisi uji coba diidentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_6',
                    'label' => 'Elemen: Mempersiapkan dokumentasi uji coba • Kriteria Unjuk Kerja: 2.1 Kebutuhan untuk uji coba ditentukan. 2.2 Uji coba dengan variasi kondisi dapat dilaksanakan. 2.3 Skenario uji coba dibuat.',
                ],
                [
                    'name' => 'pertanyaan_7',
                    'label' => 'Elemen: Mempersiapkan data uji • Kriteria Unjuk Kerja: 3.1 Data uji unit tes diidentifikasi. 3.2 Data uji unit tes dibangkitkan.',
                ],
                [
                    'name' => 'pertanyaan_8',
                    'label' => 'Elemen: Melaksanakan prosedur uji coba • Kriteria Unjuk Kerja: 4.1 Skenario uji coba didesain. 4.2 Prosedur uji coba dalam algoritma didesain. 4.3 Uji coba dilaksanakan.',
                ],
                [
                    'name' => 'pertanyaan_9',
                    'label' => 'Elemen: Mengevaluasi hasil uji coba • Kriteria Unjuk Kerja: 5.1 Hasil uji coba dicatat. 5.2 Hasil uji coba dianalisis. 5.3 Prosedur uji coba dilaporkan. 5.4 Kesalahan/error diselesaikan.',
                ],
                [
                    'name' => 'pertanyaan_10',
                    'label' => 'Elemen: Menjelaskan varian dan invariant • Kriteria Unjuk Kerja: 1.1 Tipe data telah dijelaskan sesuai kaidah pemrograman. 1.2 Variabel telah dijelaskan sesuai kaidah pemrograman 1.3 Konstanta telah dijelaskan sesuai kaidah pemrograman',
                ],
                [
                    'name' => 'pertanyaan_11',
                    'label' => 'Elemen: Membuat alur logika pemrograman • Kriteria Unjuk Kerja: 2.1 Metode yang sesuai ditentukan. 2.2 Komponen yang dibutuhkan ditentukan 2.3 Relasi antara komponen ditetapkan 2.4 Alur mulai dan selesai ditetapkan',
                ],
                [
                    'name' => 'pertanyaan_12',
                    'label' => 'Elemen: Menerapkan Teknik dasar algoritma umum • Kriteria Unjuk Kerja: 3.1 Algoritma untuk sorting dibuat. 3.2 Algoritma untuk searching dibuat.',
                ],
                [
                    'name' => 'pertanyaan_13',
                    'label' => 'Elemen: Menggunakan prosedur dan fungsi • Kriteria Unjuk Kerja: 4.1 Konsep penggunaan Kembali prosedur dan fungsi dapat di identifikasi 4.2 Prosedur dapat digunakan. 4.3 Fungsi dapat digunakan.',
                ],
                [
                    'name' => 'pertanyaan_14',
                    'label' => 'Elemen: Membuat stored procedure • Kriteria Unjuk Kerja: 5.1 Stored Procedure dibuat dengan perintah SQL. 5.2 Prosedur diuji diperiksa input dan output-nya.',
                ],
                [
                    'name' => 'pertanyaan_15',
                    'label' => 'Elemen: Mengidentifikasikan kompleksitas algoritma • Kriteria Unjuk Kerja: 6.1 Kompleksitas waktu algoritma di identifikasi 6.2 Kompleksitas waktu algoritma di identifikasi.',
                ],
                [
                    'name' => 'pertanyaan_16',
                    'label' => 'Elemen: Mempersiapkan dokumentasi peralatan dan lingkungan pengujian integrasi • Kriteria Unjuk Kerja: 1.1 Peralatan pengujian ditentukan sesuai dengan scenario pengujian 1.2 Dokumen pendukung pengujian disiapkan.',
                ],
                [
                    'name' => 'pertanyaan_17',
                    'label' => 'Elemen: Mempersiapkan data uji • Kriteria Unjuk Kerja: 2.1 Data uji integrasi program di identifikasi. 2.2 Data uji integrasi program dibangkitkan.',
                ],
                [
                    'name' => 'pertanyaan_18',
                    'label' => 'Elemen: Melaksanakan pengujian integrasi • Kriteria Unjuk Kerja: 3.1 Modul program dijalankan sesuai dengan prosedur yang ditetapkan. 3.2 Data atau kondisi sebagai masukkan, diinputkan ke dalam sistem 3.3 Hasil pengujian dicatat dalam lembar pengujian.',
                ],
                [
                    'name' => 'pertanyaan_19',
                    'label' => 'Elemen: Menganalisis data pengujian integrasi • Kriteria Unjuk Kerja: 4.1 Modul yang terkait dianalisis sesuai dengan standar pengembangan perangkat lunak yang berlaku. 4.2 Data hasil keluaran dievaluasi kesesuaiannya dengan data yang direncanakan. 4.3 Status pada lembar pengujian dari hasil perbandingan data tersebut dicatat ke dalam lembar pengujian 4.4 Kondisi data yang tidak sesuai dan perkiraan kondisi tersebut dicatat ke dalam lembar hasil uji.',
                ],
                [
                    'name' => 'pertanyaan_20',
                    'label' => 'Elemen: Melaporkan hasil pengujian integrasi • Kriteria Unjuk Kerja: 5.1 Peralatan yang digunakan untuk pengujian dicatat ke dalam peralatan pengujian. 5.2 Kondisi yang terjadi selama pengujian dicatat ke dalam lembar pengujian. 5.3 Data yang diimplementasikan dan data hasil pengujian dicatat. 5.4 Analisis hasil pengujian dicatat sesuai dengan standar dokumentasi pengembangan perangkat lunak yang berlaku',
                ],
                [
                    'name' => 'pertanyaan_21',
                    'label' => 'Elemen: Mengidentifikasikan kompleksitas algoritma • Kriteria Unjuk Kerja: 6.1 Kompleksitas waktu algoritma di identifikasi 6.2 Kompleksitas waktu algoritma di identifikasi.',
                ],
                [
                    'name' => 'pertanyaan_22',
                    'label' => 'Elemen: Mempersiapkan kode program • Kriteria Unjuk Kerja: 1.1 Kode program sesuai spesifikasi disiapkan. 1.2 Debugging tools untuk melihat proses suatu modul dipersiapkan',
                ],
                [
                    'name' => 'pertanyaan_23',
                    'label' => 'Elemen: Melakukan debugging • Kriteria Unjuk Kerja: 2.1 Kode program dikompilasi sesuai bahasa pemrograman yang digunakan. 2.2 Kriteria lulus build dianalisis. 2.3 Kriteria eksekusi aplikasi dianalisis.',
                ],
                [
                    'name' => 'pertanyaan_24',
                    'label' => 'Elemen: Memperbaiki program • Kriteria Unjuk Kerja: 3.1 Perbaikan terhadap kesalahan kompilasi maupun build dirumuskan. 3.2 Perbaikan dilakukan.',
                ],
                [
                    'name' => 'pertanyaan_25',
                    'label' => 'Elemen: Mengumpulkan data waktu eksekusi komponen komponen yang ada pada program • Kriteria Unjuk Kerja: 1.1 Waktu eksekusi function, procedure atau method program yang diukur. 1.2 Penggunaan memory eksekusi function, procedure atau method program yang diukur.',
                ],
                [
                    'name' => 'pertanyaan_26',
                    'label' => 'Elemen: Menentukan bottleneck performa yang ada pada program • Kriteria Unjuk Kerja: 2.1 Bottleneck performa pada program diidentifikasi. 2.2 Dampak negatif bottleneck terhadap performa diidentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_27',
                    'label' => 'Elemen: Merancang solusi untuk mengurangi/menghilangkan bottleneck • Kriteria Unjuk Kerja: 3.1 Rancangan metode dijelaskan. 3.2 Peningkatan performa rancangan metode ditunjukkan.',
                ],
                [
                    'name' => 'pertanyaan_28',
                    'label' => 'Elemen: Menentukan kompleksitas algoritma • Kriteria Unjuk Kerja: 4.1 Algoritma pada program terindikasi bermasalah diidentifikasikan. 4.2 Metode untuk mengukur kompleksitas terhadap algoritma diidentifikasikan. 4.3 Kompleksitas algoritma yang berdampak penurunan performa diidentifikasikan.',
                ],
                [
                    'name' => 'pertanyaan_29',
                    'label' => 'Elemen: Melakukan identifikasi kode program • Kriteria Unjuk Kerja: 1.1 Modul program diidentifikasi 1.2 Parameter yang dipergunakan diidentifikasi. 1.3 Algoritma dijelaskan cara kerjanya 1.4 Komentar setiap baris kode termasuk data, eksepsi, fungsi, prosedur dan class (bila ada)',
                ],
                [
                    'name' => 'pertanyaan_30',
                    'label' => 'Elemen: Menggunakan fitur aplikasi SQL • Kriteria Unjuk Kerja: 2.1 Fitur pengolahan DML diidentifikasikan. 2.2 Fitur pengolahan DML dieksekusi sesuai kebutuhan.',
                ],
                [
                    'name' => 'pertanyaan_31',
                    'label' => 'Elemen: Mengisi tabel • Kriteria Unjuk Kerja: 3.1 Tabel diisi data menggunakan perintah DML. 3.2 Indeks dibangkitkan. 3.3 View tabel dibentuk sesuai kebutuhan.',
                ],
                [
                    'name' => 'pertanyaan_32',
                    'label' => 'Elemen: Membuat dokumentasi modul program • Kriteria Unjuk Kerja: 4.1 Dokumentasi modul dibuat sesuai dengan identitas untuk memudahkan pelacakan 4.2 Identifikasi dokumentasi diterapkan 4.3 Kegunaan modul dijelaskan 4.4 Dokumen direvisi sesuai perubahan kode program',
                ],
                [
                    'name' => 'pertanyaan_33',
                    'label' => 'Elemen: Membuat dokumentasi fungsi, prosedur atau method program • Kriteria Unjuk Kerja: 5.1 Dokumentasi fungsi, prosedur atau metod dibuat 5.2 Prosedur diuji diperiksa input dan output-nya. 5.3 Dokumen direvisi sesuai perubahan kode program',
                ],
                [
                    'name' => 'pertanyaan_34',
                    'label' => 'Elemen: Men-generate dokumentasi • Kriteria Unjuk Kerja: 5.1 Tools untuk generate dokumentasi diidentifikasi 5.2 Generate dokumentasi dilakukan',
                ],
                [
                    'name' => 'pertanyaan_35',
                    'label' => 'Elemen: Mengevaluasi kesesuaian kode dengan spesifikasinya • Kriteria Unjuk Kerja: 1.1 Kesesuaian kode dengan ketentuan yang ada diidentifikasikan. 1.2 Ketidak-sesuaian kode dengan ketentuan diidentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_36',
                    'label' => 'Elemen: Memperbaiki kode sesuai dengan coding-guideline dan best-practices • Kriteria Unjuk Kerja: 2.1 Kode yang tidak sesuai coding-guideline diperbaiki tanpa berubah spesifikasinya. 2.2 Kode yang tidak menerapkan best-practices diperbaiki.',
                ],
                [
                    'name' => 'pertanyaan_37',
                    'label' => 'Elemen: Membuat pengecualian penulisan kode terhadap coding-guidelines • Kriteria Unjuk Kerja: 3.1 Kode yang memang sebaiknya tidak perlu sesuai coding-guideline diidentifikasi. 3.2 Komentar yang menjelaskan kode pengecualian ditulis. 3.3 View tabel dibentuk sesuai kebutuhan.',
                ],
                [
                    'name' => 'pertanyaan_38',
                    'label' => 'Elemen: Mengumpulkan kebutuhan skalabilitas • Kriteria Unjuk Kerja: 1.1 Lingkup (scope) sistem teridentifikasi. 1.2 Lingkungan operasi aplikasi teridentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_39',
                    'label' => 'Elemen: Menganalisis kebutuhan skalabilitas • Kriteria Unjuk Kerja: 2.1 Masalah skalabilitas dianalisis berdasar lingkup dan lingkungan operasi sistem. 2.2 Kompleksitas aplikasi dianalisis sesuai dengan kebutuhan pemrosesan dan jumlah data/pengguna yang akan terlibat 2.3 Kebutuhan perangkat keras dianalisis. 2.4 Hasil analisis didokumentasikan.',
                ],
                [
                    'name' => 'pertanyaan_40',
                    'label' => 'Elemen: Mempersiapkan perangkat lunak aplikasi data deskripsi/SQL • Kriteria Unjuk Kerja: 1.1 Perangkat lunak aplikasi SQL telah dipasang. 1.2 Perangkat lunak aplikasi SQL dijalankan.',
                ],
                [
                    'name' => 'pertanyaan_41',
                    'label' => 'Elemen: Menggunakan fitur aplikasi SQL • Kriteria Unjuk Kerja: 2.1 Fitur pengolahan DML diidentifikasikan. 2.2 Fitur pengolahan DML dieksekusi sesuai kebutuhan.',
                ],
                [
                    'name' => 'pertanyaan_42',
                    'label' => 'Elemen: Mengisi tabel • Kriteria Unjuk Kerja: 3.1 Tabel diisi data menggunakan perintah DML. 3.2 Indeks dibangkitkan. 3.3 View tabel dibentuk sesuai kebutuhan.',
                ],
                [
                    'name' => 'pertanyaan_43',
                    'label' => 'Elemen: Melakukan operasi relasional • Kriteria Unjuk Kerja: 4.1 Fitur pengolahan DML diidentifikasikan. 4.2 Perintah DML dipergunakan untuk manipulasi antar tabel 4.3 Perintah DML dipergunakan untuk manipulasi antar view. 4.4 Perintah DML ditulis secara efisien',
                ],
                [
                    'name' => 'pertanyaan_44',
                    'label' => 'Elemen: Membuat stored procedure • Kriteria Unjuk Kerja: 5.1 Stored Procedure dibuat dengan perintah SQL. 5.2 Prosedur diuji diperiksa input dan output-nya.',
                ],
                [
                    'name' => 'pertanyaan_45',
                    'label' => 'Elemen: Membuat function • Kriteria Unjuk Kerja: 6.1 Function dibuat dengan perintah SQL. 6.2 Perintah SQL pada function ditulis secara efisien.',
                ],
                [
                    'name' => 'pertanyaan_46',
                    'label' => 'Elemen: Membuat trigger • Kriteria Unjuk Kerja: 7.1 Trigger didefinisikan dengan perintah SQL. 7.2 Kesesuaian hasil trigger diuji.',
                ],
                [
                    'name' => 'pertanyaan_47',
                    'label' => 'Elemen: Melakukan perintah commit dan rollback • Kriteria Unjuk Kerja: 8.1 Perubahan data dengan perintah commit dilakukan. 8.2 Pembatalan penulisan data dilakukan dengan rollback.',
                ],
                [
                    'name' => 'pertanyaan_48',
                    'label' => 'Elemen: Membuat berbagai operasi terhadap basis data • Kriteria Unjuk Kerja: 1.1 Data dapat disimpan/diubah ke dalam format basis data. 1.2 Informasi yang diinginkan dapat dihasilkan menggunakan query tersebut. 1.3 Indeks dipergunakan untuk mempercepat akses.',
                ],
                [
                    'name' => 'pertanyaan_49',
                    'label' => 'Elemen: Membuat prosedur akses terhadap basis data • Kriteria Unjuk Kerja: 2.1 Library akses basis data dapat diterapkan. 2.2 Perintah akses data yang relevan dengan teknologi atau jenis baru data, diterapkan untuk mengakses data.',
                ],
                [
                    'name' => 'pertanyaan_50',
                    'label' => 'Elemen: Membuat koneksi basis data • Kriteria Unjuk Kerja: 3.1 Teknologi koneksi yang sesuai dipilih. 3.2 Keamanan koneksi ditentukan. 3.3 Hak setiap pengguna ditentukan.',
                ],
                [
                    'name' => 'pertanyaan_51',
                    'label' => 'Elemen: Menguji program basis data • Kriteria Unjuk Kerja: 4.1 Skenario pengujian disiapkan. 4.2 Logika pemrograman mengacu pada kinerja statement akses data yang akan dibaca. 4.3 Performansi mengacu pada kinerja statement akses data yang akan dibaca data diuji.',
                ],
            ],
        ],
        'AP' => [
            'file_slug' => 'analis-program',
            'question_format' => 'radio', // Format checkbox K/BK terpisah
            'questions' => [
                [
                    'name' => 'pertanyaan_1',
                    'label' => 'Elemen: Membuat berbagai operasi terhadap basis data • Kriteria Unjuk Kerja: 1.1 Data dapat disimpan/diubah ke dalam format basis data 1.2 Informasi yang diinginkan dapat dihasilkan menggunakan query tersebut 1.3 Indeks dipergunakan untuk mempercepat akses',
                ],
                [
                    'name' => 'pertanyaan_2',
                    'label' => 'Elemen: Membuat prosedur akses terhadap basis data • Kriteria Unjuk Kerja: 2.1 Library akses basis data dapat diterapkan 2.2 Perintah akses data yang relevan dengan teknologi atau jenis baru data, diterapkan untuk mengakses data',
                ],
                [
                    'name' => 'pertanyaan_3',
                    'label' => 'Elemen: Membuat Koneksi basis data • Kriteria Unjuk Kerja: 3.1 Teknologi koneksi yang sesuai dipilih 3.2 Keamanan koneksi ditentukan 3.3 Hak setiap pengguna ditentukan',
                ],
                [
                    'name' => 'pertanyaan_4',
                    'label' => 'Elemen: Menguji program basis data • Kriteria Unjuk Kerja: 4.1 Skenario pengujian disiapkan 4.2 Logika pemrograman mengacu pada kinerja statement akses data yang akan dibaca 4.3 Performansi mengacu pada kinerja statement akses data yang akan dibaca diuji.',
                ],
                [
                    'name' => 'pertanyaan_5',
                    'label' => 'Elemen: Menentukan kebutuhan uji coba dalam pengembangan • Kriteria Unjuk Kerja: 1.1 Prosedur uji coba aplikasi diidentifikasikan sesuai dengan software development life cycle. 1.2 Tools uji coba ditentukan. 1.3 Standar dan kondisi uji coba diidentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_6',
                    'label' => 'Elemen: Mempersiapkan dokumentasi uji coba • Kriteria Unjuk Kerja: 2.1 Kebutuhan untuk uji coba ditentukan. 2.2 Uji coba dengan variasi kondisi dapat dilaksanakan. 2.3 Skenario uji coba dibuat.',
                ],
                [
                    'name' => 'pertanyaan_7',
                    'label' => 'Elemen: Mempersiapkan data uji • Kriteria Unjuk Kerja: 3.1 Data uji unit tes diidentifikasi. 3.2 Data uji unit tes dibangkitkan.',
                ],
                [
                    'name' => 'pertanyaan_8',
                    'label' => 'Elemen: Melaksanakan prosedur uji coba • Kriteria Unjuk Kerja: 4.1 Skenario uji coba didesain. 4.2 Prosedur uji coba dalam algoritma didesain. 4.3 Uji coba dilaksanakan.',
                ],
                [
                    'name' => 'pertanyaan_9',
                    'label' => 'Elemen: Mengevaluasi hasil uji coba • Kriteria Unjuk Kerja: 5.1 Hasil uji coba dicatat. 5.2 Hasil uji coba dianalisis. 5.3 Prosedur uji coba dilaporkan. 5.4 Kesalahan/error diselesaikan.',
                ],
                [
                    'name' => 'pertanyaan_10',
                    'label' => 'Elemen: Menjelaskan varian dan invariant • Kriteria Unjuk Kerja: 1.1 Tipe data telah dijelaskan sesuai kaidah pemrograman. 1.2 Variabel telah dijelaskan sesuai kaidah pemrograman 1.3 Konstanta telah dijelaskan sesuai kaidah pemrograman',
                ],
                [
                    'name' => 'pertanyaan_11',
                    'label' => 'Elemen: Membuat alur logika pemrograman • Kriteria Unjuk Kerja: 2.1 Metode yang sesuai ditentukan. 2.2 Komponen yang dibutuhkan ditentukan 2.3 Relasi antara komponen ditetapkan 2.4 Alur mulai dan selesai ditetapkan',
                ],
                [
                    'name' => 'pertanyaan_12',
                    'label' => 'Elemen: Menerapkan Teknik dasar algoritma umum • Kriteria Unjuk Kerja: 3.1 Algoritma untuk sorting dibuat. 3.2 Algortima untuk searching dibuat.',
                ],
                [
                    'name' => 'pertanyaan_13',
                    'label' => 'Elemen: Menggunakan prosedur dan fungsi • Kriteria Unjuk Kerja: 4.1 Konsep penggunaan Kembali prosedur dan fungsi dapat di identifkasi 4.2 Prosedur dapat digunakan. 4.3 Fungsi dapat digunakan.',
                ],
                [
                    'name' => 'pertanyaan_14',
                    'label' => 'Elemen: Membuat stored procedure • Kriteria Unjuk Kerja: 5.1 Stored Procedure dibuat dengan perintah SQL. 5.2 Prosedur diuji diperiksa input dan output-nya.',
                ],
                [
                    'name' => 'pertanyaan_15',
                    'label' => 'Elemen: Mengidentifkasikan kompleksitas algoritma • Kriteria Unjuk Kerja: 6.1 Kompleksitas waktu algoritma di identifkasi 6.2 Kompleksitas waktu algoritma di identifkasi.',
                ],
                [
                    'name' => 'pertanyaan_16',
                    'label' => 'Elemen: Mempersiapkan dokumentasi peralatan dan lingkungan pengujian integrasi • Kriteria Unjuk Kerja: 1.1 Peralatan pengujian ditentukan sesuai dengan kebutuhan pengujian. 1.2 Dokumen pendukung pengujian disiapkan.',
                ],
                [
                    'name' => 'pertanyaan_17',
                    'label' => 'Elemen: Mempersiapkan data uji • Kriteria Unjuk Kerja: 2.1 Data uji integrasi program di identifikasi. 2.2 Data uji integrasi program dibangkitkan.',
                ],
                [
                    'name' => 'pertanyaan_18',
                    'label' => 'Elemen: Melaksanakan pengujian integrasi • Kriteria Unjuk Kerja: 3.1 Modul program dijalankan sesuai dengan prosedur yang ditetapkan. 3.2 Data atau kondisi sebagai masukkan, diimplementasikan ke dalam program. 3.3 Hasil pengujian dicatat dalam lembar pengujian.',
                ],
                [
                    'name' => 'pertanyaan_19',
                    'label' => 'Elemen: Menganalisis data pengujian integrasi • Kriteria Unjuk Kerja: 4.1 Modul yang terkait dianalisis sesuai dengan standar pengembangan perangkat lunak yang berlaku. 4.2 Data hasil keluaran dievaluasi kesesuaiannya dengan data yang direncanakan. 4.3 Status pada lembar pengujian dari hasil perbandingan data tersebut dicatat ke dalam lembar pengujian 4.4 Kondisi data yang tidak sesuai dan perkiraan kondisi tersebut dicatat ke dalam lembar hasil uji.',
                ],
                [
                    'name' => 'pertanyaan_20',
                    'label' => 'Elemen: Melaporkan hasil pengujian integrasi • Kriteria Unjuk Kerja: 5.1 Peralatan yang digunakan untuk pengujian dicatat ke dalam lembar peralatan pengujian. 5.2 Kondisi yang terjadi selama pengujian dicatat ke dalam lembar pengujian. 5.3 Data yang diimplementasikan dan data hasil pengujian dicatat. 5.4 Analisis hasil pengujian dicatat sesuai dengan standar dokumentasi pengembangan perangkat lunak yang berlaku',
                ],
                [
                    'name' => 'pertanyaan_21',
                    'label' => 'Elemen: Mengidentifkasikan kompleksitas algoritma • Kriteria Unjuk Kerja: 6.1 Kompleksitas waktu algoritma di identifkasi 6.2 Kompleksitas waktu algoritma di identifkasi.',
                ],
                [
                    'name' => 'pertanyaan_22',
                    'label' => 'Elemen: Mempersiapkan kode program • Kriteria Unjuk Kerja: 1.1 Kode program sesuai spesifikasi disiapkan. 1.2 Debugging tools untuk melihat proses suatu modul dipersiapkan',
                ],
                [
                    'name' => 'pertanyaan_23',
                    'label' => 'Elemen: Melakukan debugging • Kriteria Unjuk Kerja: 2.1 Kode program dikompilasi sesuai bahasa pemrograman yang digunakan. 2.2 Kriteria lulus build dianalisis. 2.3 Kriteria eksekusi aplikasi dianalisis. 2.4 Kode kesalahan dicatat.',
                ],
                [
                    'name' => 'pertanyaan_24',
                    'label' => 'Elemen: Memperbaiki program • Kriteria Unjuk Kerja: 3.1 Perbaikan terhadap kesalahan kompilasi maupun build dirumuskan. 3.2 Perbaikan dilakukan.',
                ],
                [
                    'name' => 'pertanyaan_25',
                    'label' => 'Elemen: Mengumpulkan data waktu eksekusi komponen komponen yang ada pada program • Kriteria Unjuk Kerja: 1.1 Waktu eksekusi function, procedure atau method program yang diukur. 1.2 Penggunaan memory eksekusi function, procedure atau method program yang diukur.',
                ],
                [
                    'name' => 'pertanyaan_26',
                    'label' => 'Elemen: Menentukan bottleneck performa yang ada pada program • Kriteria Unjuk Kerja: 2.1 Bottleneck performa pada program diidentifikasi. 2.2 Dampak negatif bottleneck terhadap performa diidentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_27',
                    'label' => 'Elemen: Merancang solusi untuk mengurangi/menghilangkan bottleneck • Kriteria Unjuk Kerja: 3.1 Rancangan metode dijelaskan. 3.2 Peningkatan performa rancangan metode ditunjukkan.',
                ],
                [
                    'name' => 'pertanyaan_28',
                    'label' => 'Elemen: Menentukan kompleksitas algoritma • Kriteria Unjuk Kerja: 4.1 Algoritma pada program terindikasi bermasalah diidentifikasikan. 4.2 Metode untuk mengukur kompleksitas terhadap algoritma ditentukan. 4.3 Kompleksitas algoritma yang berdampak penurunan performa diidentifikasikan.',
                ],
                [
                    'name' => 'pertanyaan_29',
                    'label' => 'Elemen: Melakukan identifikasi kode program • Kriteria Unjuk Kerja: 1.1 Modul program diidentifikasi 1.2 Parameter yang dipergunakan diidentifikasi. 1.3 Algoritma dijelaskan cara kerjanya 1.4 Komentar setiap baris kode termasuk data, eksepsi, fungsi, prosedur dan class (bila ada)',
                ],
                [
                    'name' => 'pertanyaan_30',
                    'label' => 'Elemen: Menggunakan fitur aplikasi SQL • Kriteria Unjuk Kerja: 2.1 Fitur pengolahan DML diidentifikasikan. 2.2 Fitur pengolahan DML dieksekusi sesuai kebutuhan.',
                ],
                [
                    'name' => 'pertanyaan_31',
                    'label' => 'Elemen: Mengisi tabel • Kriteria Unjuk Kerja: 3.1 Tabel diisi data menggunakan perintah DML. 3.2 Indeks dibangkitkan. 3.3 View tabel dibentuk sesuai kebutuhan.',
                ],
                [
                    'name' => 'pertanyaan_32',
                    'label' => 'Elemen: Membuat dokumentasi modul program • Kriteria Unjuk Kerja: 4.1 Dokumentasi modul dibuat sesuai dengan identitas untuk memudahkan pelacakan 4.2 Identifikasi dokumentasi diterapkan 4.3 Kegunaan modul dijelaskan 4.4 Dokumen direvisi sesuai perubahan kode program',
                ],
                [
                    'name' => 'pertanyaan_33',
                    'label' => 'Elemen: Membuat dokumentasi fungsi, prosedur atau method program • Kriteria Unjuk Kerja: 5.1 Dokumentasi fungsi, prosedur atau metod dibuat 5.2 Prosedur diuji diperiksa input dan output-nya. 5.3 Dokumen direvisi sesuai perubahan kode program',
                ],
                [
                    'name' => 'pertanyaan_34',
                    'label' => 'Elemen: Men-generate dokumentasi • Kriteria Unjuk Kerja: 5.1 Tools untuk generate dokumentasi diidentifikasi 5.2 Generate dokumentasi dilakukan',
                ],
                [
                    'name' => 'pertanyaan_35',
                    'label' => 'Elemen: Mengevaluasi kesesuaian kode dengan spesifikasinya • Kriteria Unjuk Kerja: 1.1 Kesesuaian kode dengan ketentuan yang ada diidentifikasi. 1.2 Ketidak-sesuaian kode dengan ketentuan diidentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_36',
                    'label' => 'Elemen: Memperbaiki kode sesuai dengan coding-guideline dan best-practices • Kriteria Unjuk Kerja: 2.1 Kode yang tidak sesuai coding-guideline diperbaiki tanpa berubah spesifikasinya. 2.2 Kode yang tidak menerapkan best-practices diperbaiki.',
                ],
                [
                    'name' => 'pertanyaan_37',
                    'label' => 'Elemen: Membuat pengecualian penulisan kode terhadap coding-guidelines • Kriteria Unjuk Kerja: 3.1 Kode yang memang sebaiknya tidak perlu sesuai coding-guideline diidentifikasi. 3.2 Komentar yang menjelaskan kode pengecualian ditulis. 3.3 View tabel dibentuk sesuai kebutuhan.',
                ],
                [
                    'name' => 'pertanyaan_38',
                    'label' => 'Elemen: Mengumpulkan kebutuhan skalabilitas • Kriteria Unjuk Kerja: 1.1 Lingkup (scope) sistem teridentifikasi. 1.2 Lingkungan operasi aplikasi teridentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_39',
                    'label' => 'Elemen: Menganalisis kebutuhan skalabilitas • Kriteria Unjuk Kerja: 2.1 Masalah skalabilitas dianalisis berdasar lingkup dan lingkungan operasi sistem. 2.2 Kompleksitas aplikasi dianalisis sesuai dengan kebutuhan pemrosesan dan jumlah data/pengguna yang akan terlibat 2.3 Kebutuhan perangkat keras dianalisis. 2.4 Hasil analisis didokumentasikan.',
                ],
                [
                    'name' => 'pertanyaan_40',
                    'label' => 'Elemen: Mempersiapkan perangkat lunak aplikasi data deskripsi/SQL • Kriteria Unjuk Kerja: 1.1 Perangkat lunak aplikasi SQL telah dipasang. 1.2 Perangkat lunak aplikasi SQL dijalankan.',
                ],
                [
                    'name' => 'pertanyaan_41',
                    'label' => 'Elemen: Menggunakan fitur aplikasi SQL • Kriteria Unjuk Kerja: 2.1 Fitur pengolahan DML diidentifikasikan. 2.2 Fitur pengolahan DML dieksekusi sesuai kebutuhan.',
                ],
                [
                    'name' => 'pertanyaan_42',
                    'label' => 'Elemen: Mengisi tabel • Kriteria Unjuk Kerja: 3.1 Tabel diisi data menggunakan perintah DML. 3.2 Indeks dibangkitkan. 3.3 View tabel dibentuk sesuai kebutuhan.',
                ],
                [
                    'name' => 'pertanyaan_43',
                    'label' => 'Elemen: Melakukan operasi relasional • Kriteria Unjuk Kerja: 4.1 Fitur pengolahan DML diidentifikasikan. 4.2 Perintah DML dipergunakan untuk manipulasi antar tabel 4.3 Perintah DML dipergunakan untuk manipulasi antar view. 4.4 Perintah DML ditulis secara efisien',
                ],
                [
                    'name' => 'pertanyaan_44',
                    'label' => 'Elemen: Membuat stored procedure • Kriteria Unjuk Kerja: 5.1 Stored Procedure dibuat dengan perintah SQL. 5.2 Prosedur diuji diperiksa input dan output-nya.',
                ],
                [
                    'name' => 'pertanyaan_45',
                    'label' => 'Elemen: Membuat function • Kriteria Unjuk Kerja: 6.1 Function dibuat dengan perintah SQL. 6.2 Perintah SQL pada function ditulis secara efisien.',
                ],
                [
                    'name' => 'pertanyaan_46',
                    'label' => 'Elemen: Membuat trigger • Kriteria Unjuk Kerja: 7.1 Trigger didefinisikan dengan perintah SQL. 7.2 Kesesuaian hasil trigger diuji.',
                ],
                [
                    'name' => 'pertanyaan_47',
                    'label' => 'Elemen: Melakukan perintah commit dan rollback • Kriteria Unjuk Kerja: 8.1 Perubahan data dengan perintah commit dilakukan. 8.2 Pembatalan penulisan data dilakukan dengan rollback.',
                ],
                [
                    'name' => 'pertanyaan_48',
                    'label' => 'Elemen: Membuat berbagai operasi terhadap basis data • Kriteria Unjuk Kerja: 1.1 Data dapat disimpan/diubah ke dalam format basis data. 1.2 Informasi yang diinginkan dapat dihasilkan menggunakan query tersebut. 1.3 Indeks dipergunakan untuk mempercepat akses.',
                ],
                [
                    'name' => 'pertanyaan_49',
                    'label' => 'Elemen: Membuat prosedur akses terhadap basis data • Kriteria Unjuk Kerja: 2.1 Library akses basis data dapat diterapkan. 2.2 Perintah akses data yang relevan dengan teknologi atau jenis baru data, diterapkan untuk mengakses data.',
                ],
                [
                    'name' => 'pertanyaan_50',
                    'label' => 'Elemen: Membuat koneksi basis data • Kriteria Unjuk Kerja: 3.1 Teknologi koneksi yang sesuai dipilih. 3.2 Keamanan koneksi ditentukan. 3.3 Hak setiap pengguna ditentukan.',
                ],
                [
                    'name' => 'pertanyaan_51',
                    'label' => 'Elemen: Menguji program basis data • Kriteria Unjuk Kerja: 4.1 Skenario pengujian disiapkan. 4.2 Logika pemrograman mengacu pada kinerja statement akses data yang akan dibaca. 4.3 Performansi mengacu pada kinerja statement akses data yang akan dibaca data diuji.',
                ],
            ],
        ],
        'ASP' => [
            'file_slug' => 'asisten-pemrograman',
            'question_format' => 'radio', // Format checkbox K/BK terpisah
            'questions' => [
                [
                    'name' => 'pertanyaan_1',
                    'label' => 'Elemen: Menunjukkan jenis platform sistem operasi berbasis mobile • Kriteria Unjuk Kerja: 1.1 Arsitektur dasar sistem operasi berbasis mobile ditunjukkan sesuai dengan perangkat keras yang digunakan. 1.2 Platform sistem operasi berbasis mobile ditunjukkan sesuai dengan perangkat keras yang terkait. 1.3 Security pada platform sistem operasi berbasis mobile ditunjukkan sesuai dengan perangkat keras yang digunakan.',
                ],
                [
                    'name' => 'pertanyaan_2',
                    'label' => 'Elemen: Menentukan platform sistem operasi yang sesuai kebutuhan user • Kriteria Unjuk Kerja: 2.1 Kebutuhan user dirancang berdasarkan spesifikasinya. 2.2 Sistem operasi untuk mengembangkan aplikasi mobile ditentukan platform-nya',
                ],
                [
                    'name' => 'pertanyaan_3',
                    'label' => 'Elemen: Menjelaskan Bahasa pemrograman berbasis mobile • Kriteria Unjuk Kerja: 3.1 Mobile pemrograman berbasis mobile ditentukan jenis Bahasa pemrogramannya 3.2 Bahasa pemrograman berbasis mobile dibandingkan perbedannya 3.3 Perangkat lunak terkait penggunaan Bahasa pemrograman berbasis mobile dikonfigurasi sesuai dengan spesifikasinya 3.4 Alur program dihasilkan untuk pembuatan aplikasi berbasis mobile 3.5 Konsep variabel dan konstanta dalam salah satu Bahasa pemrograman berbasis mobile ditentukan tipe-datanya. 3.6 Konsep struktur kondisi dan perulangan ditentukan dalam salah satu Bahasa pemrograman berbasis mobile. 3.7 Konsep layout dan objek dijelaskan dalam salah satu Bahasa pemrograman berbasis mobile 3.8 Aplikasi mobile sederhana dibangun dengan Bahasa pemrograman mobile',
                ],
                [
                    'name' => 'pertanyaan_4',
                    'label' => 'Elemen: Mengidentifikasi rancangan user interface • Kriteria Unjuk Kerja: 1.1 Rancangan user interface diidentifikasi sesuai kebutuhan. 1.2 Komponen user interface dialog diidentifikasi sesuai konteks rancangan proses. 1.3 Urutan dari akses komponen user interface dialog dijelaskan. 1.4 Simulasi (mock-up) dari aplikasi yang akan dikembangkan dibuat.',
                ],
                [
                    'name' => 'pertanyaan_5',
                    'label' => 'Elemen: Melakukan implementasi rancangan user interface • Kriteria Unjuk Kerja: 2.1 Menu program sesuai dengan rancangan program diterapkan. 2.2 Penempatan user interface dialog diatur secara sekuensial. 2.3 Setting aktif-pasif komponen user interface dialog disesuaikan dengan urutan alur proses. 2.4 Bentuk style dari komponen user interface ditentukan. 2.5 Penerapan simulasi dijadikan suatu proses yang sesungguhnya.',
                ],
                [
                    'name' => 'pertanyaan_6',
                    'label' => 'Elemen: Memilih tools pemrograman yang sesuai dengan kebutuhan • Kriteria Unjuk Kerja: 1.1 Platform (lingkungan) yang akan digunakan untuk menjalankan tools pemrograman diidentifikasi sesuai dengan kebutuhan. 1.2 Tools bahasa pemrogram dipilih sesuai dengan kebutuhaan dan lingkungan pengembangan.',
                ],
                [
                    'name' => 'pertanyaan_7',
                    'label' => 'Elemen: Instalasi tool pemrograman • Kriteria Unjuk Kerja: 2.1 Tools pemrogaman ter-install sesuai dengan prosedur. 2.2 Tools pemrograman bisa dijalankan di lingkungan pengembangan yang telah ditetapkan.',
                ],
                [
                    'name' => 'pertanyaan_8',
                    'label' => 'Elemen: Menerapkan hasil pemodelan kedalam eksekusi script sederhana • Kriteria Unjuk Kerja: 3.1 Script (source code) sederhana dibuat sesuai tools pemrogaman yang di-install 3.2 Script dapat dijalankan dengan benar dan menghasilkan keluaran sesuai skenario yang diharapkan',
                ],
                [
                    'name' => 'pertanyaan_9',
                    'label' => 'Elemen: Melakukan konfigurasi tools untuk pemrograman • Kriteria Unjuk Kerja: 1.1 Target hasil dari konfigurasi ditentukan. 1.2 Tools pemrograman setelah dikonfigurasikan, tetap bisa digunakan sebagaimana mestinya.',
                ],
                [
                    'name' => 'pertanyaan_10',
                    'label' => 'Elemen: Menggunakan tools sesuai kebutuhan pembuatan program • Kriteria Unjuk Kerja: 2.1 Fitur-fitur dasar yang dibutuhkan untuk mendukung pembuatan program diidentifikasikan. 2.2 Fitur-fitur dasar tools untuk pembuatan program dikuasai.',
                ],
                [
                    'name' => 'pertanyaan_11',
                    'label' => 'Elemen: Membuat program berorientasi objek dengan memanfaatkan class • Kriteria Unjuk Kerja: 1.1 Program dengan menggunakan class dibuat. 1.2 Properti class yang akan direalisasikan dalam bentuk prosedur/fungsi dibuat. 1.3 Data didalam class dibuat mandiri. 1.4 Hak akses dari tipe data (private, protected, public) dikelola.',
                ],
                [
                    'name' => 'pertanyaan_12',
                    'label' => 'Elemen: Menggunakan tipe data dan control program pada metode atau operasi dari suatu kelas • Kriteria Unjuk Kerja: 2.1 Tipe data diidentifikasi. 2.2 Sintaks program dikuasai sesuai dengan bahasa pemrogramnnya. 3.3 Control program dikuasai.',
                ],
                [
                    'name' => 'pertanyaan_13',
                    'label' => 'Elemen: Membuat program dengan konsep berbasis objek • Kriteria Unjuk Kerja: 3.1 Inheritance pada class diterapkan. 3.2 Polymorphism pada class diterapkan. 3.4 Overloading pada class diterapkan.',
                ],
                [
                    'name' => 'pertanyaan_14',
                    'label' => 'Elemen: Membuat program object oriented dengan interface dan paket • Kriteria Unjuk Kerja: 4.1 Interface class program dibuat. 4.2 Paket dengan program dibuat.',
                ],
                [
                    'name' => 'pertanyaan_15',
                    'label' => 'Elemen: Mengkompilasi Program • Kriteria Unjuk Kerja: 5.1 Kesalahan dapat dikoreksi. 5.2 Program bebas salah sintaks dihasilkan.',
                ],
                [
                    'name' => 'pertanyaan_16',
                    'label' => 'Elemen: Melakukan identifikasi kode program • Kriteria Unjuk Kerja: 1.1 Modul program diidentifikasi 1.2 Parameter yang dipergunakan diidentifikasi 1.3 Algoritma dijelaskan cara kerjanya 1.4 Komentar setiap baris kode termasuk data, eksepsi, fungsi, prosedur dan class (bila ada) diberikan',
                ],
                [
                    'name' => 'pertanyaan_17',
                    'label' => 'Elemen: Membuat dokumentasi modul program • Kriteria Unjuk Kerja: 2.1 Dokumentasi modul dibuat sesuai dengan identitas untuk memudahkan pelacakan 2.2 Identifikasi dokumentasi diterapkan 2.3 Kegunaan modul dijelaskan 2.4 Dokumen direvisi sesuai perubahan kode program',
                ],
                [
                    'name' => 'pertanyaan_18',
                    'label' => 'Elemen: Membuat dokumentasi fungsi, prosedur atau method program • Kriteria Unjuk Kerja: 3.1 Dokumentasi modul dibuat sesuai dengan identitas untuk memudahkan pelacakan 3.2 Identifikasi dokumentasi diterapkan 3.3 Kegunaan modul dijelaskan 3.4 Dokumen direvisi sesuai perubahan kode program',
                ],
                [
                    'name' => 'pertanyaan_19',
                    'label' => 'Elemen: Men-generate dokumentasi • Kriteria Unjuk Kerja: 4.1 Tools untuk generate dokumentasi diidentifikasi 4.2 Generate dokumentasi dilakukan',
                ],
                [
                    'name' => 'pertanyaan_20',
                    'label' => 'Elemen: Menerapkankonsep/metodepencatatan versi dari setiap program sumber • Kriteria Unjuk Kerja: 1.1 Pengertian konsep penerapan versi kode program dapat dijelaskan. 1.2 Proses branching, merging, commit, check-in, check-out dan cloning dapat dijelaskan. 1.3 Konsep repository dapat dijelaskan.',
                ],
                [
                    'name' => 'pertanyaan_21',
                    'label' => 'Elemen: Menggunakan suatu tools untuk menyimpan versi • Kriteria Unjuk Kerja: 2.1 Guna dari alat/tools dapat ditunjukkan. 2.2 Alat/tools dapat diusulkan. 2.3 Karakteristik dari tools/alat dapat dijelaskan atau ditunjukkan. 2.4 Proses branching, merging, commit, check-in, check-out dan cloning dilakukan.',
                ],
                [
                    'name' => 'pertanyaan_22',
                    'label' => 'Elemen: Menentukan kebutuhan uji coba dalam pengembangan • Kriteria Unjuk Kerja: 1.1 Prosedur uji coba aplikasi diidentifikasikan sesuai dengan software development life cycle. 1.2 Tools uji coba ditentukan. 1.3 Standar dan kondisi uji coba diidentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_23',
                    'label' => 'Elemen: Mempersiapkan dokumentasi uji coba • Kriteria Unjuk Kerja: 2.1 Kebutuhan untuk uji coba ditentukan. 2.2 Uji coba dengan variasi kondisi dapat dilaksanakan. 2.3 Skenario uji coba dibuat.',
                ],
                [
                    'name' => 'pertanyaan_24',
                    'label' => 'Elemen: Mempersiapkan data uji • Kriteria Unjuk Kerja: 3.1 Data uji unit tes diidentifikasi. 3.2 Data uji unit tes dibangkitkan.',
                ],
                [
                    'name' => 'pertanyaan_25',
                    'label' => 'Elemen: Melaksanakan prosedur uji coba • Kriteria Unjuk Kerja: 4.1 Skenario uji coba didesain. 4.2 Prosedur uji coba dalam algoritma didesain. 4.3 Uji coba dilaksanakan.',
                ],
                [
                    'name' => 'pertanyaan_26',
                    'label' => 'Elemen: Mengevaluasihasil uji coba • Kriteria Unjuk Kerja: 5.1 Hasil uji coba dicatat. 5.2 Hasil uji coba dianalisis. 5.3 Prosedur uji coba dilaporkan. 5.4 Kesalahan/error diselesaikan.',
                ],
                [
                    'name' => 'pertanyaan_27',
                    'label' => 'Elemen: Mempersiapkan komputer yang akan di-install • Kriteria Unjuk Kerja: 1.1 Perangkat bantu diperiksa dan berjalan dengan normal. 1.2 Komputer yang akan install sistem operasi disiapkan berserta buku manual terkait. 1.3 Komputer dinyalakan dan berjalan dengan normal.',
                ],
                [
                    'name' => 'pertanyaan_28',
                    'label' => 'Elemen: Mengidentifikasi instalasi system operasi • Kriteria Unjuk Kerja: 2.1 Setting BIOS dibuka saat komputer mulai restart dan dapat berjalan dengan normal. 2.2 Konfigurasi boot sequence dilakukan sesuai dengan Buku manual terkait. 2.3 Konfigurasi BIOS disimpan sesuai dengan buku manual terkait.',
                ],
                [
                    'name' => 'pertanyaan_29',
                    'label' => 'Elemen: Melakukan instalasi system operasi • Kriteria Unjuk Kerja: 3.1 Restart komputer dilakukan dan berjalan dengan normal. 3.2 Media installer dipasang sesuai dengan konfigurasi boot sequence. 3.3 Proses instalasi dan konfigurasi dilakukan sesuai dengan buku manual terkait.',
                ],
                [
                    'name' => 'pertanyaan_30',
                    'label' => 'Elemen: Mempersiapkan pekerjaan instalasi software aplikasi • Kriteria Unjuk Kerja: 1.1 Paket instalasi software disediakan dalam media penyimpanan beserta buku manual terkait. 1.2 Perangkat komputer dengan sistem operasinya sudah dinyalakan.',
                ],
                [
                    'name' => 'pertanyaan_31',
                    'label' => 'Elemen: Melaksanakan instalasi software aplikasi • Kriteria Unjuk Kerja: 2.1 Proses instalasi dilaksanakan sesuai dengan buku manual. 2.2 Seluruh file, icon (jika ada) dan konfigurasi sistem telah terinstal dan terkonfigurasi. 2.3 Pada layar muncul pesan bahwa proses instalasi telah berhasil dilaksanakan sesuai dengan buku manual.',
                ],
                [
                    'name' => 'pertanyaan_32',
                    'label' => 'Elemen: Menguji hasil Instalasi software aplikasi • Kriteria Unjuk Kerja: 3.1 Software aplikasi dijalankan secara sampling tanpa error. 3.2 Software aplikasi ditutup tanpa error.',
                ],
                [
                    'name' => 'pertanyaan_33',
                    'label' => 'Elemen: Mengidentifikasi dan menjabarkan teknik penyampaian multimedia • Kriteria Unjuk Kerja: 1.1 Teknologi komputer termasuk CPU, ROM, RAM, storage devices, monitor, dan peralatan input sehubungan dengan multimedia diidentifikasi dan dijelaskan fungsi-fungsinya. 1.2 Peralatan analog dan digital yang relevan dengan multimedia diidentifikasi dan dikenali. 1.3 Properti dari data yang telah dikenal, didefinisikan dengan benar menjadi spesifikasi. 1.4 Permasalahan sehubungan dengan perubahan teknologi yang cepat termasuk media elektronik dan fotografi digital didiskusikan untuk mendapatkan hasil yang spesifik. 1.5 Permasalahan sehubungan dengan perubahan teknologi yang cepat termasuk media elektronik dan fotografi digital didiskusikan untuk mendapatkan hasil yang spesifik. 1.6 Permasalahan sehubungan dengan perubahan teknologi yang cepat termasuk media elektronik dan fotografi digital didiskusikan untuk mendapatkan hasil yang spesifik.',
                ],
                [
                    'name' => 'pertanyaan_34',
                    'label' => 'Elemen: Mengeksplorasi ruang lingkup multimedia • Kriteria Unjuk Kerja: 2.1 Ruang lingkup multimedia dieksplorasi dan dijelaskan secara relevan dengan sektor industri. 2.2 Peran pembuatan proyek multimedia diidentifikasi dan dijelaskan secara benar. 2.3 Beragam komponen-komponen proyek multimedia termasuk teks, grafik, fotografi, tipografi, suara, animasi dan video diperinci secara benar ke dalam media komponen. 2.4 Kegunaan multimeda dan hubungannya dengan pra cetak untuk mendapatkan hasil yang spesifik dijabarkan. 2.5 Perbedaan antara media pasif dan interaktif dieksplorasi dan dijelaskan secara benar. 2.6 Fungsi-fungsi software multimedia kontemporer sehubungan dengan teks, grafik, fotografi, tipografi, suara, animasi, dan video, diidentifikasi untuk memastikan aplikasi pada hasil telah relevan. 2.7 Kegunaan multimedia sehubungan dengan berbagai hasil termasuk surat kabar, majalah, sheet fed tradisional, percetakan digital, halaman www internet, bill board digital dan CD ROM diidentifikasi dan kesesuaian multimedia untuk hasil tersebut didiskusikan.',
                ],
                [
                    'name' => 'pertanyaan_35',
                    'label' => 'Elemen: Mengembangkan persyaratan fungsi • Kriteria Unjuk Kerja: 1.1 Persyaratan fungsi yang akurat, komplit dan sesuai prioritas diidentifikasi sesuai keperluan dengan referensi semua tipe media. 1.2 Persyaratan yang berlawanan dan overlapping diidentifikasi. 1.3 Persyaratan fungsi didokumentasi dan divalidasi oleh klien. 1.4 Sumber-sumber dan pembiayaan yang tersedia diidentifikasi dan divalidasi oleh klien.',
                ],
                [
                    'name' => 'pertanyaan_36',
                    'label' => 'Elemen: Memilih Peralatan • Kriteria Unjuk Kerja: 2.1 Produk-produk dan peralatan yang relevan diidentifikasi dan dievaluasi dengan referensi persyaratan fungsi. 2.2 Kemandirian produk dan peralatan diidentifikasi dan dianalisa dengan referensi pada persyaratan fungsi dan arsitektur sistem. 2.3 Produk terbaik dan solusi peralatan, termasuk keterbatasan-keterbatasan diidentifikasi dan didokumentasikan 2.4 Peralatan dipilih dan dipesan sebagaimana diperlukan sehubungan dengan kebijaksanaan perusahaan penjualan.',
                ],
                [
                    'name' => 'pertanyaan_37',
                    'label' => 'Elemen: Mengkonfigurasi dan menguji peralatan yang telah dipasang • Kriteria Unjuk Kerja: 3.1 Peralatan dipasang dan dikonfigurasi menurut petunjuk dari vendor dengan referensi pada sistem arsitektur dan persyaratan fungsi pelanggan. 3.2 Sistem arsitektur dan konfigurasi disesuaikan sebagaimana keperluan. 3.3 Tes disiapkan dan dijadwalkan untuk dilaksanakan sebagaimana keperluan. 3.4 Error dilacak, diterjemahkan dan diperbaiki sebagaimana keperluan. 3.5 Perubahan dibuat sebagaimana diperlukan berdasar pada hasil pengujian. 3.6 Konfigurasi peralatan didokumentasikan sesuai permintaan pelanggan. 3.7 Implikasi pembuatan professional diidentifikasi, didokumentasi, dan dilaporkan dengan referensi pada kebijaksanaan perusahaan.',
                ],
                [
                    'name' => 'pertanyaan_38',
                    'label' => 'Elemen: Menggunakan peralatan • Kriteria Unjuk Kerja: 4.1 Pendidikan dan pelatihan pemakai peralatan dilakukan sesuai keperluan dengan referensi pada kebijaksanaan perusahaan. 4.2 Peralatan digunakan sesuai petunjuk dari vendor. 4.3 Peralatan dievaluasi berdasarkan referensi kebutuhan klien.',
                ],
            ],
        ],
        'JADE' => [
            'file_slug' => 'data-engineer',
            'question_format' => 'radio', // Format checkbox K/BK terpisah
            'questions' => [
                [
                    'name' => 'pertanyaan_1',
                    'label' => 'Elemen: Menetapkan persyaratan basis data • Kriteria Unjuk Kerja: 1.1 Analisis kebutuhan pengguna dilakukan untuk menentukan fungsionalitas basis data. 1.2 Persyaratan teknis diidentifikasi berdasarkan hasil analisis kebutuhan pengguna. 1.3 Model konseptual basis data dibuat sesuai kebutuhan pengguna. 1.4 Model konseptual dikirim kepada klien untuk ditinjau sesuai kebutuhan. 1.5 Umpan balik klien dievaluasi untuk perubahan seperlunya.',
                ],
                [
                    'name' => 'pertanyaan_2',
                    'label' => 'Elemen: Membuat model data logis • Kriteria Unjuk Kerja: 2.1 Atribut dan tipe data diidentifikasi sesuai model data. 2.2 Normalisasi atribut dilakukan sesuai model konseptual. 2.3 Diagram relasi antar entitas (Entity Relationship Diagram/ERD) atau class diagram dibuat untuk memperjelas kardinalitas relasi. 2.4 Atribut, data yang dinormalisasi, dan diagram ERD didokumentasikan sesuai kebutuhan. 2.5 Dokumentasi dikirimkan ke klien untuk konfirmasi.',
                ],
                [
                    'name' => 'pertanyaan_3',
                    'label' => 'Elemen: Merancang struktur data • Kriteria Unjuk Kerja: 3.1 Primary key dan foreign key untuk table ditetapkan sesuai model data. 3.2 Aturan bisnis klien ditinjau sesuai kebutuhan organisasi. 3.3 Kendala integritas referensial diidentifikasi. 3.4 Aturan validasi data dikembangkan sesuai kebutuhan. 3.5 Indeks dan kamus data dirancang sesuai kebutuhan. 3.6 Desain basis data dibuatkan dokumentasinya.',
                ],
                [
                    'name' => 'pertanyaan_4',
                    'label' => 'Elemen: Merancang query, tampilan dan laporan • Kriteria Unjuk Kerja: 4.1 Antarmuka pengguna untuk basis data, termasuk menu, layar input dan output dirancang. 4.2 Query dirancang berdasarkan kebutuhan. 4.3 Laporan keluaran dirancang, berdasarkan kebutuhan. 4.4 Desain fisikal dibandingkan dengan model konseptual atau analisis kebutuhan pengguna. 4.5 Perubahan digabungkan sesuai kebutuhan / requirement',
                ],
                [
                    'name' => 'pertanyaan_5',
                    'label' => 'Elemen: Mendefinisikan Kebutuhan akan reference and master data • Kriteria Unjuk Kerja: 1.1 Kebutuhan reference and master data dianalisis sesuai dengan kebutuhan organisasi. 1.2 Sumber-sumber data dianalisis sesuai dengan kebutuhan penyusunan reference and master data. 1.3 Definisi-definisi tentang data diidentifikasi sesuai dengan kebutuhan manajemen data.',
                ],
                [
                    'name' => 'pertanyaan_6',
                    'label' => 'Elemen: Membangun reference and master data • Kriteria Unjuk Kerja: 2.1 Definisi-definisi yang telah teridentifikasi dimasukkan ke dalam reference data termasuk ontology data. 2.2 Data yang teridentifikasi dimasukkan ke dalam master data. 2.3 Pemetaan pembagian data (shared data) disusun sesuai dengan kebutuhan manajemen data.',
                ],
                [
                    'name' => 'pertanyaan_7',
                    'label' => 'Elemen: Menggunakan reference and master data • Kriteria Unjuk Kerja: 3.1 Penyebaran pembagian data (shared data) dilakukan sesuai dengan kebutuhan manajemen data. 3.2 Perubahan yang terjadi terhadap reference and master data dikelola sesuai kebutuhan manajemen data.',
                ],
                [
                    'name' => 'pertanyaan_8',
                    'label' => 'Elemen: Mendefinisikan Kebutuhan akan metadata • Kriteria Unjuk Kerja: 1.1 Kebutuhan metadata dianalisis sesuai dengan kebutuhan organisasi. 1.2 Arsitektur metadata ditentukan sesuai dengan hasil analisis kebutuhan metadata. 1.3 Standar metadata disusun sesuai dengan kebutuhan manajemen data.',
                ],
                [
                    'name' => 'pertanyaan_9',
                    'label' => 'Elemen: Membangun metadata • Kriteria Unjuk Kerja: 2.1 Standar metadata yang telah tersusun diterapkan sesuai dengan kebutuhan manajemen data. 2.2 Metadata yang telah terdefiniskan diintegrasikan sesuai dengan kebutuhan manajemen data. 2.3 Metadata repository dibangun sesuai dengan kebutuhan manajemen data.',
                ],
                [
                    'name' => 'pertanyaan_10',
                    'label' => 'Elemen: Menggunakan metadata • Kriteria Unjuk Kerja: 3.1 Metadata yang telah terbangun didistribusikan sesuai dengan kebutuhan organisasi. 3.2 Metadata digunakan untuk kebutuhan query, laporan dan analisis sesuai dengan kebutuhan organisasi. 3.3 Standar metadata dipelihara sesuai perkembangan manajemen data. 3.4 Metadata repository dipelihara sesuai dengan perkembangan manajemen data.',
                ],
                [
                    'name' => 'pertanyaan_11',
                    'label' => 'Elemen: Mempersiapkan perangkat lunak aplikasi data deskripsi/SQL • Kriteria Unjuk Kerja: 1.1 Perangkat lunak aplikasi SQL telah dipasang. 1.2 Perangkat lunak aplikasi SQL dijalankan.',
                ],
                [
                    'name' => 'pertanyaan_12',
                    'label' => 'Elemen: Menggunakan fitur aplikasi SQL • Kriteria Unjuk Kerja: 2.1 Fitur pengolahan DML diidentifikasikan. 2.2 Fitur pengolahan DML dieksekusi sesuai kebutuhan.',
                ],
                [
                    'name' => 'pertanyaan_13',
                    'label' => 'Elemen: Mengisi tabel • Kriteria Unjuk Kerja: 3.1 Tabel diisi data menggunakan perintah DML. 3.2 Indeks dibangkitkan. 3.3 View tabel dibentuk sesuai kebutuhan.',
                ],
                [
                    'name' => 'pertanyaan_14',
                    'label' => 'Elemen: Melakukan operasi relasional • Kriteria Unjuk Kerja: 4.1 Fitur pengolahan DML diidentifikasikan. 4.2 Perintah DML dipergunakan untuk manipulasi antar tabel 4.3 Perintah DML dipergunakan untuk manipulasi antar - view 4.4 Perintah DML ditulis secara efisien',
                ],
                [
                    'name' => 'pertanyaan_15',
                    'label' => 'Elemen: Membuat stored procedure • Kriteria Unjuk Kerja: 5.1 Stored Procedure dibuat dengan perintah SQL. 5.2 Prosedur diuji diperiksa input dan output nya.',
                ],
                [
                    'name' => 'pertanyaan_16',
                    'label' => 'Elemen: Membuat function • Kriteria Unjuk Kerja: 6.1 Function dibuat dengan perintah SQL. 6.2 Perintah SQL pada function ditulis secara efisien.',
                ],
                [
                    'name' => 'pertanyaan_17',
                    'label' => 'Elemen: Membuat trigger • Kriteria Unjuk Kerja: 7.1 Trigger didefinisikan dengan perintah SQL. 7.2 Kesesuaian hasil trigger diuji.',
                ],
                [
                    'name' => 'pertanyaan_18',
                    'label' => 'Elemen: Melakukan perintah commit dan rollback • Kriteria Unjuk Kerja: 8.1 Perubahan data dengan perintah commit dilakukan. 8.2 Pembatalan penulisan data dilakukan dengan rollback.',
                ],
                [
                    'name' => 'pertanyaan_19',
                    'label' => 'Elemen: Membuat basis data • Kriteria Unjuk Kerja: 1.1 Hasil desain basis data diidentifikasi sesuai dengan kebutuhan organisasi. 1.2 Basis data dibuat sesuai dengan desain. 1.3 Tools yang sesuai dengan basis data digunakan untuk membuat koleksi data. 1.4 Field-field basis data diisi sesuai dengan kebutuhan organisasi.',
                ],
                [
                    'name' => 'pertanyaan_20',
                    'label' => 'Elemen: Menguji basis data • Kriteria Unjuk Kerja: 2.1 Skenario uji dibuat sesuai kebutuhan organisasi. 2.2 Basis data diuji berdasarkan dengan skenario uji. 2.3 Informasi yang tampil yang merupakan hasil uji basis data dipastikan sesuai dengan persyaratan kebutuhan organisasi.',
                ],
                [
                    'name' => 'pertanyaan_21',
                    'label' => 'Elemen: Mengembangkan arsitektur integrasi data • Kriteria Unjuk Kerja: 1.1 Pemetaan dari sumber data terhadap target data dibuat sesuai dengan kebutuhan integrasi. 1.2 Model interaksi dipilih sesuai kebutuhan organisasi. 1.3 Data services atau exchange patterns untuk mengalirkan data didesain sesuai dengan standar yang berlaku. 1.4 Data orchestration dirancang sesuai dengan kebutuhan.',
                ],
                [
                    'name' => 'pertanyaan_22',
                    'label' => 'Elemen: Membuat solusi integrasi data • Kriteria Unjuk Kerja: 2.1 Data services dibuat sesuai dengan model interaksi yang dipilih. 2.2 Data flows dibuat sesuai dengan kebutuhan integrasi. 2.3 Proses data migration dibuat sesuai dengan kebutuhan. 2.4 Proses data publication dibuat sesuai dengan kebutuhan integrasi.',
                ],
                [
                    'name' => 'pertanyaan_23',
                    'label' => 'Elemen: Mengimplementasi solusi integrasi data • Kriteria Unjuk Kerja: 3.1 Data services diuji sesuai dengan spesifikasi yang sudah dikembangkan. 3.2 Data services yang sudah diuji dioperasikan sesuai dengan standard yang ditetapkan.',
                ],
                [
                    'name' => 'pertanyaan_24',
                    'label' => 'Elemen: Memonitor penerapan integrasi data • Kriteria Unjuk Kerja: 4.1 Parameter monitoring ditentukan sesuai dengan rencana integrasi. 4.2 Berjalannya sistem dianalisa sesuai dengan standar layanan yang telah ditentukan. 4.3 Hasil analisis didokumentasikan sesuai dengan prosedur.',
                ],
                [
                    'name' => 'pertanyaan_25',
                    'label' => 'Elemen: Menetapkan tools yang akan digunakan • Kriteria Unjuk Kerja: 1.1 Tools untuk menggunakan data diidentifikasi sesuai dengan kebutuhan organisasi. 1.2 Tools untuk keperluan menggunakan data ditentukan kebutuhan organisasi.',
                ],
                [
                    'name' => 'pertanyaan_26',
                    'label' => 'Elemen: Mengakses data • Kriteria Unjuk Kerja: 2.1 Kebutuhan akses data diidentifikasi sesuai dengan kebutuhan organisasi. 2.2 Kebutuhan basis data untuk memenuhi kebutuhan pengguna dilakukan dengan menggunakan tools yang telah ditentukan. 2.3 Pelaksanaan akses basis data didokumentasikan sesuai dengan standar yang berlaku.',
                ],
                [
                    'name' => 'pertanyaan_27',
                    'label' => 'Elemen: Memonitor penggunaan data • Kriteria Unjuk Kerja: 3.1 Penggunaan data dimonitor sesuai dengan hak akses 3.2 Laporan penggunaan didokumentasikan kebutuhan organisasi.',
                ],
            ],
        ],
        'JADS' => [
            'file_slug' => 'data-scientist',
            'question_format' => 'radio', // Format checkbox K/BK terpisah
            'questions' => [
                [
                    'name' => 'pertanyaan_1',
                    'label' => 'Elemen: Menentukan kebutuhan data • Kriteria Unjuk Kerja: 1.1 Kebutuhan data diidentifikasi sesuai tujuan teknis data science 1.2 Kebutuhan data diperiksa ketersediannya sesuai aturan yang berlaku. 1.3 Kebutuhan data ditentukan volumenya sesuai tujuan teknis data science',
                ],
                [
                    'name' => 'pertanyaan_2',
                    'label' => 'Elemen: Mengambil Data • Kriteria Unjuk Kerja: 2.1 Metode dan tools pengambilan data diidentifikasi sesuai tujuan teknis data scence 2.2 Tools pengambilan data ditentukan sesuai tujuan teknis data science 2.3 Tools pengambilan data disiapkan sesuai tujuan teknis data science 2.4 Proses pengambilan data dijalankan sesuai dengan tools yang telah disiapkan',
                ],
                [
                    'name' => 'pertanyaan_3',
                    'label' => 'Elemen: Mengintegrasikan Data • Kriteria Unjuk Kerja: 3.1 Integritas data diperiksa sesuai tujuan teknis data science 3.2 Data diintegrasikan sesuai tujuan teknis data science',
                ],
                [
                    'name' => 'pertanyaan_4',
                    'label' => 'Elemen: Menganalisis tipe dan relasi data • Kriteria Unjuk Kerja: 1.1 Tipe data yang terkumpul diidentifikasi sesuai tujuan teknis 1.2. Nilai atribut data yang terkumpul diuraikan sesuai dengan batasan konteks bisnisnya 1.3 Relasi antar data yang terkumpul diidentifikasi sesuai dengan tujuan teknis',
                ],
                [
                    'name' => 'pertanyaan_5',
                    'label' => 'Elemen: Menganalisis karakteristik data • Kriteria Unjuk Kerja: 2.1 Karakteristik data yang terkumpul disajikan dengan deskripsi statistik dasar 2.2 Karakteristik data yang terkumpul disajikan dengan visualisasi grafik 2.3 Hasil penyajian data dianalisis karakteristiknya untuk telaah data',
                ],
                [
                    'name' => 'pertanyaan_6',
                    'label' => 'Elemen: Membuat laporan telaah data • Kriteria Unjuk Kerja: 3.1. Hasil analisis didokumentasikan dalam bentuk laporan sesuai dengan tujuan teknis 3.2 Hipotesis disusun berdasar hasil analisis sesuai tujuan teknis data science',
                ],
                [
                    'name' => 'pertanyaan_7',
                    'label' => 'Elemen: Melakukan pengecekan kelengkapan data • Kriteria Unjuk Kerja: 1.1 Penilaian kualitas data dari hasil telaah disajikan sesuai tujuan teknis data science 1.2. Penilaian tingkat kecukupan data dari hasil telaah disajikan sesuai tujuan teknis data science.',
                ],
                [
                    'name' => 'pertanyaan_8',
                    'label' => 'Elemen: Membuat rekomendasi kelengkapan data • Kriteria Unjuk Kerja: 2.1 Rekomendasi hasil penilaian kualitas disusun sesuai tujuan teknis data science 2.2 Rekomendasi hasil penilaian kecukupan data disusun sesuai tujuan teknis data science',
                ],
                [
                    'name' => 'pertanyaan_9',
                    'label' => 'Elemen: Memutuskan kriteria dan teknik pemilihan data • Kriteria Unjuk Kerja: 1.1 Kriteria pemilihan data diidentifikasi sesuai dengan tujuan teknis dan aturan yang berlaku 1.2. Teknik pemilihan data ditetapkan sesuai dengan kriteria pemilihan data.',
                ],
                [
                    'name' => 'pertanyaan_10',
                    'label' => 'Elemen: Menentukan attributes (columns) & records (row) data • Kriteria Unjuk Kerja: 2.1 Attributes (columns) data diidentifikasi sesuai dengan kriteria pemilihan data 2.2 Records (row) data diidentifikasi sesuai dengan kriteria pemilihan data',
                ],
                [
                    'name' => 'pertanyaan_11',
                    'label' => 'Elemen: Melakukan pembersihan data yang kotor • Kriteria Unjuk Kerja: 1.1 Strategi pembersihan data ditentukan berdasarkan hasil telaah data. 1.2. Data yang kotor dikoreksi berdasarkan strategi pembersihan data.',
                ],
                [
                    'name' => 'pertanyaan_12',
                    'label' => 'Elemen: Membuat laporan dan rekomendasi hasil membersihkan data • Kriteria Unjuk Kerja: 2.1 Masalah dan teknis koreksi data dideskripsikan sesuai dengan kondisi data dan strategi pembersihan data 2.2 Evaluasi dihasilkan berdasarkan analisis koreksi yang telah dilakukan 2.3 Evaluasi proses dan hasilnya didokumentasikan',
                ],
                [
                    'name' => 'pertanyaan_13',
                    'label' => 'Elemen: Menganalisis teknik transformasi data • Kriteria Unjuk Kerja: 1.1 Analisis data untuk menentukan representasi fitur data awal. 1.2. Analisis representasi fitur data awal untuk menentukan teknik rekayasa fitur yang diperlukan untuk pembangunan model data science.',
                ],
                [
                    'name' => 'pertanyaan_14',
                    'label' => 'Elemen: Melakukan transformasi data • Kriteria Unjuk Kerja: 2.1 Transformasi dilakukan untuk mendapatkan fitur data awal 2.2 Rekayasa fitur data dilakukan untuk mendapatkan fitur baru yang diperlukan untuk pembangunan model data science',
                ],
                [
                    'name' => 'pertanyaan_15',
                    'label' => 'Elemen: Membuat dokumentasi konstruksi data • Kriteria Unjuk Kerja: 3.1 Teknis transformasi data dijabarkan dalam bentuk tertulis 3.2 Hasil transformasi data dan rekomendasi hasil transformasi dituangkan dalam bentuk tertulis',
                ],
                [
                    'name' => 'pertanyaan_16',
                    'label' => 'Elemen: Melakukan pelabelan data • Kriteria Unjuk Kerja: 1.1 Analisis hasil pelabelan data sejenis yang sudah ada diuraikan kesesuaiannya dengan Standard Operating Procedure (SOP) pelabelan. 1.2. Pelabelan data dilakukan sesuai dengan SOP pelabelan.',
                ],
                [
                    'name' => 'pertanyaan_17',
                    'label' => 'Elemen: Membuat laporan hasil pelabelan data • Kriteria Unjuk Kerja: 2.1 Statistik hasil pelabelan diuraikan pada laporan. 2.2 Evaluasi proses pelabelan diuraikan pada laporan',
                ],
                [
                    'name' => 'pertanyaan_18',
                    'label' => 'Elemen: Menyiapkan parameter model • Kriteria Unjuk Kerja: 1.1 Parameter-parameter yang sesuai dengan model diidentifikasi. 1.2. Nilai toleransi parameter evaluasi pengujian ditetapkan sesuai dengan tujuan teknis.',
                ],
                [
                    'name' => 'pertanyaan_19',
                    'label' => 'Elemen: Menggunakan tools pemodelan • Kriteria Unjuk Kerja: 2.1 Tools untuk membuat model diidentifikasi sesuai dengan tujuan teknis data science. 2.2 Algoritma untuk teknik pemodelan yang ditentukan dibangun menggunakan tools yang dipilih. 2.3 Algoritma pemodelan dieksekusi sesuai dengan skenario pengujian dan tools untuk membuat model yang telah ditetapkan. 2.4 Parameter model algoritma dioptimasi untuk menghasilkan nilai parameter evaluasi yang sesuai dengan skenario pengujian.',
                ],
                [
                    'name' => 'pertanyaan_20',
                    'label' => 'Elemen: Menggunakan model dengan data riil • Kriteria Unjuk Kerja: 1.1 Data baru untuk evaluasi pemodelan dikumpulkan sesuai kebutuhan yang mengacu kepada parameter evaluasi. 1.2. Model diuji dengan menggunakan data riil yang telah dikumpulkan',
                ],
                [
                    'name' => 'pertanyaan_21',
                    'label' => 'Elemen: Menilai hasil pemodelan • Kriteria Unjuk Kerja: 2.1 Keluaran pengujian model dinilai berdasarkan metrik kesuksesan 2.2 Hasil penilaian didokumentasikan sesuai standar yang berlaku',
                ],
            ],
        ],
        'CSA' => [
            'file_slug' => 'cyber-security-analyst',
            'question_format' => 'radio', // Format checkbox K/BK terpisah
            'questions' => [
                [
                    'name' => 'pertanyaan_1',
                    'label' => 'Elemen: Mematuhi dan melaksanakan petunjuk yang terdapat pada dokumen yang diterbitkan khusus oleh pemerintah atau badan‐badan resmi terkait untuk mengelola sistem operasi Lingkungan Komputasi • Kriteria Unjuk Kerja: 1.1 Dokumen yang diterbitkan khusus oleh pemerintah atau badan‐badan resmi terkait untuk mengelola sistem operasi Lingkungan Komputasi diindentifikasi. 1.2 Butir‐butir pokok yang terdapat pada dokumentasi tersebut dideskripsikan.',
                ],
                [
                    'name' => 'pertanyaan_2',
                    'label' => 'Elemen: Menerapkan ketentuan hukum keamanan sistem dan peraturan yang sesuai dengan infrastruktur sistem teknologi informasi yang didukung • Kriteria Unjuk Kerja: 2.1 Dokumen daftar seluruh pelanggaran dan tindakannya pra solusi/preventif-nya atas keamanan sistem infrastruktur dan sistem Teknologi Informasi yang didukung dibuat.',
                ],
                [
                    'name' => 'pertanyaan_3',
                    'label' => 'Elemen: Mematuhi hukum/regulasi keamanan sistem informasi dan segala peraturannya untuk mendukung operasi fungsional dari lingkungan jaringan • Kriteria Unjuk Kerja: 3.1 Dokumen regulasi/peraturan keamanan sistem informasi dipatuhi. 3.2 Hasil audit/rekomendasi kepatuhan pelaksanaan kegiatan sehari‐hari yang terkait dengan regulasi keamanan sistem informasi yang berlaku diterapkan.',
                ],
                [
                    'name' => 'pertanyaan_4',
                    'label' => 'Elemen: Mengidentifikasi dan/atau menentukan apakah sebuah insiden keamanan merupakan indikasi dari pelanggaran hukum yang memerlukan tindakan hukum tertentu • Kriteria Unjuk Kerja: 4.1 Dokumen yang terkait dengan regulasi dan /atau undang‐undang tentang keamanan informasi yang berlaku diidentifikasi. 4.2 Log catatan insiden dan resolusinya dibuat. 4.3 Rekomendasi hasil evaluasi indikasi pelanggaran hukum diberikan.',
                ],
                [
                    'name' => 'pertanyaan_5',
                    'label' => 'Elemen: Menyusun, menerapkan, dan menegakkan kebijakan dan prosedur yang mencerminkan tujuan legislatif hukum dan peraturan yang berlaku untuk lingkungan jaringan sistem informasi organisasi • Kriteria Unjuk Kerja: 5.1 Kebijakan dan prosedur legal dan peraturan yang berlaku untuk lingkungan jaringan sistem informasi organisasi disusun. 5.2 Kebijakan dan prosedur legal dan peraturan yang berlaku untuk lingkungan jaringan sistem informasi organisasi disetujui oleh pimpinan untuk diterapkan. 5.3 Hasil audit/rekomendasi pelaksanaan kebijakan dan prosedur diterapkan.',
                ],
                [
                    'name' => 'pertanyaan_6',
                    'label' => 'Elemen: Memberikan dukungan dalam pengumpulan dan pelestarian bukti yang digunakan dalam proses penuntutan kejahatan komputer • Kriteria Unjuk Kerja: 6.1 Dokumen hasil kegiatan pengumpulan dan pelestarian bukti yang digunakan dalam proses penuntutan kejahatan komputer diberikan.',
                ],
                [
                    'name' => 'pertanyaan_7',
                    'label' => 'Elemen: Menunjukkan kepemimpinan dan memberikan pengarahan kepada personil-personil keamanan operasional • Kriteria Unjuk Kerja: 1.1 Daftar peraturan dan arahan yang berisi standar instruksi kepada para personil keamanan disusun.',
                ],
                [
                    'name' => 'pertanyaan_8',
                    'label' => 'Elemen: Melaksanakan koordinasi dan/atau menyediakan bantuan untuk semua aplikasi posisi strategis dan operasi • Kriteria Unjuk Kerja: 2.1 Daftar layanan informasi beserta ketentuan keamanan informasi untuk tingkatan strategis disusun. 2.2 Laporan kegiatan dukungan keamanan aplikasi pada tingkatan strategis dibuat.',
                ],
                [
                    'name' => 'pertanyaan_9',
                    'label' => 'Elemen: Mengarahkan/memimpin tim dan/atau menyediakan dukungan untuk menyelesaikan dengan cepat atau mengurangi masalah keamanan untuk lingkungan strategis • Kriteria Unjuk Kerja: 3.1 Daftar layanan informasi beserta ketentuan keamanan informasi untuk tingkatan strategis disusun. 3.2 Laporan kegiatan penanganan masalah keamanan pada tingkatan strategis dibuat.',
                ],
                [
                    'name' => 'pertanyaan_10',
                    'label' => 'Elemen: Menyediakan kepemimpinan dan arahan kepada SDM jaringan sistem informasi dengan memastikan bahwa kesadaran keamanan, dasar‐dasar, literasi, dan pelatihan diberikan kepada personil operasi sepadan dengan tanggung jawab mereka • Kriteria Unjuk Kerja: 4.1 Kebijakan tentang keamanan sistem informasi disusun dan diaplikasikan. 4.2 Sosialisasi dan pelatihan tentang kesadaran dan kewaspadaan keamanan sistem informasi kepada SDM terkait dilaksanakan. 4.3 Tugas dan tanggung jawab yang terkait dengan keamanan sistem informasi diaplikasikan.',
                ],
                [
                    'name' => 'pertanyaan_11',
                    'label' => 'Elemen: Melakukan validasi penunjukan/penugasan pengguna untuk tugas‐tugas yang terkait dengan keamanan informasi yang sensitif • Kriteria Unjuk Kerja: 1.1 Dokumen pelaksanaan tugas‐tugas yang terkait dengan keamanan informasi yang sensitif kepada peran/jabatan terkait dalam organisasi dibuat. 1.2 Hasil audit/rekomendasi pelaksanaan tugas‐tugas keamanan informasi oleh peran/jabatan terkait dilaporkan.',
                ],
                [
                    'name' => 'pertanyaan_12',
                    'label' => 'Elemen: Merekomendasikan alokasi sumber daya yang dibutuhkan untuk secara aman mengoperasikan dan memelihara keamanan jaringan organisasi seusai dengan persyaratannya • Kriteria Unjuk Kerja: 2.1 Dokumen penugasan SDM pemeliharaan keamanan jaringan dan lingkungan komputasi diterima oleh SDM yang diberi tanggung jawab pelaksanaannya.',
                ],
                [
                    'name' => 'pertanyaan_13',
                    'label' => 'Elemen: Mendapatkan dan mempertahankan sertifikasi keamanan sesuai dengan posisi/jabatan dalam organisasi • Kriteria Unjuk Kerja: 3.1 SDM yang memiliki tanggung jawab keamanan sesuai dengan peran/jabatan dalam organisasi memiliki sertifikasi keamanan yang dikeluarkan oleh badan/lembaga terkait.',
                ],
                [
                    'name' => 'pertanyaan_14',
                    'label' => 'Elemen: Menganalisis kinerja sistem untuk potensi masalah‐masalah keamanan • Kriteria Unjuk Kerja: 1.1 Dokumen hasil analisis kinerja sistem keamanan yang ada dibuat. 1.2 Daftar potensi ancaman keamanan yang dapat terjadi di dalam sistem disusun.',
                ],
                [
                    'name' => 'pertanyaan_15',
                    'label' => 'Elemen: Menilai kinerja kontrol keamanan di dalam lingkungan jaringan • Kriteria Unjuk Kerja: 2.1 Daftar penilaian kontrol keamanan didalam lingkungan jaringan disusun.',
                ],
                [
                    'name' => 'pertanyaan_16',
                    'label' => 'Elemen: Memonitor kinerja sistem dan peraturan untuk memenuhi persyaratan keamanan dan privasi dalam lingkungan komputasi • Kriteria Unjuk Kerja: 3.1 Rencana pemantauan kinerja sistem dan peraturan untuk memenuhi persyaratan keamanan dan privasi dalam lingkungan komputasi disusun. 3.2 Laporan berkala hasil pemantuankinerja dan peraturan keamananinformasi dibuat.',
                ],
                [
                    'name' => 'pertanyaan_17',
                    'label' => 'Elemen: Melacak dan melaporkan semua butir tinjauan manajemen keamanan • Kriteria Unjuk Kerja: 4.1 Rencana kegiatan pemantauan /peninjauan manajemen keamanan disusun. 4.2 Laporan hasil pemantauan/tinjauan manajemen keamanan disusun.',
                ],
                [
                    'name' => 'pertanyaan_18',
                    'label' => 'Elemen: Mendeteksi potensi pelanggaran keamanan, mengambil tindakan yang sesuai untuk melaporkan kejadian tersebut sesuai dengan peraturan dan mengurangi dampak yang merugikan • Kriteria Unjuk Kerja: 1.1 Laporan deteksi potensi pelanggaran keamanan beserta tindakan pengamanan yang telah dilaksanakan dibuat.',
                ],
                [
                    'name' => 'pertanyaan_19',
                    'label' => 'Elemen: Memeriksa seluruh potensi atas pelanggaran keamanan untuk menentukan apakah kebijakan lingkungan teknologi jaringan telah dilanggar, menganalisa dan mencatat seluruh dampak dan juga menjaga barang bukti • Kriteria Unjuk Kerja: 2.1 Daftar seluruh pelanggaran, hasil analisa, dan pencatatan dampak negatif yang terjadi dari adanya pelanggaran kebijakan disusun. 2.2 Jumlah potensi pelanggaran keamanan yang ada di dalam suatu sistem Teknologi Informasi diidentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_20',
                    'label' => 'Elemen: Mengidentifikasi kerentanan keamanan yang dari rencana implementasi atau kerentananyang tidak terdeteksi pada saat uji coba • Kriteria Unjuk Kerja: 3.1 Daftar potensi kerentanan keamanan yang sudah terdeteksi dalam fase uji coba maupun yang tidak terdeteksi dalam fase uji coba disusun. 3.2 Daftar kerentanan dan solusinya masing-masing disusun.',
                ],
                [
                    'name' => 'pertanyaan_21',
                    'label' => 'Elemen: Melakukan tinjauan perlindungan keamanan tertentu untuk menentukan masalah keamanan (yang diidentifikasi dalam rencana yang telah disetujui) telah sepenuhnya ditangani • Kriteria Unjuk Kerja: 4.1 Laporan hasil kegiatan tinjauan perlindungan keamanan dibuat.',
                ],
                [
                    'name' => 'pertanyaan_22',
                    'label' => 'Elemen: Mengimplementasikan koreksi atas segala kerentanan sistem yang bersifat teknis • Kriteria Unjuk Kerja: 1.1 Laporan hasil implementasi koreksi kerentanan yang ada dibuat. 1.2 Daftar tindakan korektif dan relevansinya terhadap penanganan kerentanan sistem disusun.',
                ],
                [
                    'name' => 'pertanyaan_23',
                    'label' => 'Elemen: Memberikan arahan dan/atau dukungan untuk para pengembang sistem mengenai pengkoreksian dari seluruh masalah keamanan data yang teridentifikasi pada fase pengujian • Kriteria Unjuk Kerja: 2.1 Daftar potensi kerentanan keamanan yang sudah terdeteksi dalam fase uji coba maupun yang tidak terdeteksi dalam fase uji coba disusun. 2.2 Daftar kerentanan dan solusinya masing-masing disusun.',
                ],
                [
                    'name' => 'pertanyaan_24',
                    'label' => 'Elemen: Mengimplementasi penanganan kerentanan dari sistem strategis • Kriteria Unjuk Kerja: 3.1 Prosedur dan kebijakan penanganan kerentanan keamanan informasi organisasi pada tingkatan strategis diidentifikasi. 3.2 Log insiden kerentanan keamanan pada tingkatan strategis dan solusinya dibuat.',
                ],
            ],
        ],
        'DMM' => [
            'file_slug' => 'designer-multimedia-madya',
            'question_format' => 'radio', // Format checkbox K/BK terpisah
            'questions' => [
                [
                    'name' => 'pertanyaan_1',
                    'label' => 'Elemen: Melakukan riset konten • Kriteria Unjuk Kerja: 1.1 Data riset konten multimedia dikumpulkan. 1.2 Data riset konten multimedia dianalisa. 1.3 Rekomendasi konten multimedia dihasilkan.',
                ],
                [
                    'name' => 'pertanyaan_2',
                    'label' => 'Elemen: Melakukan riset teknologi • Kriteria Unjuk Kerja: 2.1. Data riset teknologi multimedia dikumpulkan. 2.2. Data riset teknologi multimedia dianalisa. 2.3 Rekomendasi teknologi multimedia dihasilkan.',
                ],
                [
                    'name' => 'pertanyaan_3',
                    'label' => 'Elemen: Melakukan riset kebutuhan user • Kriteria Unjuk Kerja: 3.1. Data riset kebutuhan user multimedia dikumpulkan. 3.2. Data riset kebutuhan user multimedia dianalisa. 3.3 Rekomendasi kebutuhan multimedia dihasilkan.',
                ],
                [
                    'name' => 'pertanyaan_4',
                    'label' => 'Elemen: Mengidentifikasi ruang lingkup pekerjaan visual mengacu creative brief yang telah ditetapkan • Kriteria Unjuk Kerja: 1.1 Ruang lingkup pekerjaan visual diidentifikasi berdasarkan creative brief. 1.2 Langkah-langkah pencarian gagasan visua ditetapkan berdasarkan hasil identifikasi raung lingkup pekerjaan.',
                ],
                [
                    'name' => 'pertanyaan_5',
                    'label' => 'Elemen: Mengembangkan ide-ide elemen visual berdasar creative brief yang telah ditetapkan • Kriteria Unjuk Kerja: 2.1 Alternatif gagasan visual dibuat berdasarkan ruang lingkup pekerjaan. 2.2 Alternatif gagasan visual dirangkum berdasarkan arahan creative brief. 2.3 Gagasan visual terpilih ditetapkan melalui persetujuan creative director.',
                ],
                [
                    'name' => 'pertanyaan_6',
                    'label' => 'Elemen: Membuat arahan visual berdasar creative brief yang telah ditetapkan • Kriteria Unjuk Kerja: 2.1 Arahan visual dibuat berdasarkan gagasan visual terpilih. 2.2 Arahan visual dirumuskan kepada personil/tim produksi secara jelas dan tepat.',
                ],
                [
                    'name' => 'pertanyaan_7',
                    'label' => 'Elemen: Mengelola jalannya produksi aset visual multimedia • Kriteria Unjuk Kerja: 1.1 Proses produksi aset visual multimedia diatur berdasarkan langkah-langkah kerja yang sudah ditetapkan. 1.2 Pendampingan dalam proses produksi aset visual multimedia dilakukan pada setiap langkah- langkah kerja.',
                ],
                [
                    'name' => 'pertanyaan_8',
                    'label' => 'Elemen: Mempresentasikan output produksi visual multimedia • Kriteria Unjuk Kerja: 2.1 Output produski aset visual multimedia disusun kedalam sebuah materi presentasi yang tepat kepada creative director. 2.2 Materi presentasi dikomunikasikan kepada creative director. 2.3 Umpan balik (feed back) dari creative director dirangkum dengan cermat.',
                ],
                [
                    'name' => 'pertanyaan_9',
                    'label' => 'Elemen: Mengevaluasi hasil produksi asset visual multimedia • Kriteria Unjuk Kerja: 2.1 Arahan produksi aset visual multimedia diperbaiki berdasarkan umpan balik (feed back) dari creative director. 2.2 Perbaikan arahan aset visual multimedia dikomunikasikan kepada personil/tim produksisecara jelas dan tepat.',
                ],
                [
                    'name' => 'pertanyaan_10',
                    'label' => 'Elemen: mengidentifikasi ruang lingkup pekerjaan audio • Kriteria Unjuk Kerja: 1.1 Ruang lingkup pekerjaan audiodiidentifikasi berdasarkan creativebrief. 1.2 Langkah-langkah pencarian ide audio ditetapkan berdasarkan hasil identifikasi ruang lingkup pekerjaan.',
                ],
                [
                    'name' => 'pertanyaan_11',
                    'label' => 'Elemen: Mengembangkan ide nuansa audio • Kriteria Unjuk Kerja: 2.1 Alternatif ide nuansa audio dibuat ruang lingkup. 2.2 Alternatif ide nuansa audio dirangkum berdasarkan arahan creative brief. 2.3 Ide nuansa audio ditetapkan melalui creative director.',
                ],
                [
                    'name' => 'pertanyaan_12',
                    'label' => 'Elemen: Membuat Arahan Nuansa • Kriteria Unjuk Kerja: 2.1 Arahan nuansa audio dibuat berdasarkan ide visual terpilih 2.2 Arahan nuansa audio dikomunikasikan kepada personil/tim produksi secara jelas dan tepat.',
                ],
                [
                    'name' => 'pertanyaan_13',
                    'label' => 'Elemen: Memproduksi aset audio multimedia • Kriteria Unjuk Kerja: 1.1 Prosedur pengerjaan teridentifikasi. 1.2 Aset audio multimedia diproduksi. 1.3 Penyimpanan secara dilakukan.',
                ],
                [
                    'name' => 'pertanyaan_14',
                    'label' => 'Elemen: Mereview hasil produksi audio • Kriteria Unjuk Kerja: 2.1 Hasil kerja direview secara berkala. 2.2 Tuntutan perbaikan visual dipersiapkan. 2.3 Proses perbaikan dilakukan.',
                ],
                [
                    'name' => 'pertanyaan_15',
                    'label' => 'Elemen: Menerjemahkan konsep teknis • Kriteria Unjuk Kerja: 1.1 Kebutuhan teknis untuk mendukung konsep diidentifikasi sesuai dengan langkah kerja. 1.2 Kebutuhan teknis diterapkan sesuai dengan langkah kerja.',
                ],
                [
                    'name' => 'pertanyaan_16',
                    'label' => 'Elemen: Merencanakan jenis algoritma pemrograman • Kriteria Unjuk Kerja: 2.1 Logika algoritma dijelaskan sesuai dengan konsep. 2.2 Jenis-jenis bahasa pemrograman dipilih sesuai kebutuhan konsep.',
                ],
                [
                    'name' => 'pertanyaan_17',
                    'label' => 'Elemen: Mengidentifikasi kebutuhan bahasa pemrograman, piranti lunak dan piranti keras • Kriteria Unjuk Kerja: 1.1 Bahasa pemrograman diidentifikasi sesuai kebutuhan teknis. 1.2 Jenis – jenis piranti lunak diidentifikasi sesuai kebutuhan teknis 1.3 Spesifikasi piranti keras diidentifikasi sesuai kebutuhan teknis.',
                ],
                [
                    'name' => 'pertanyaan_18',
                    'label' => 'Elemen: Menentukan kebutuhan bahasa pemrograman, piranti lunak dan piranti keras • Kriteria Unjuk Kerja: 2.1 Jenis bahasa pemrograman teknis ditentukan sesuai langkah kerja. 2.2 Jenis piranti lunak yang sesuai kebutuhan teknis ditentukan. 2.3 Jenis piranti keras yang sesuai kebutuhan teknis ditentukan.',
                ],
                [
                    'name' => 'pertanyaan_19',
                    'label' => 'Elemen: Mempersiapkan pemrograman interaktif • Kriteria Unjuk Kerja: 1.1 Seluruh aset multimedia diidentifikasi sudah sesuai dengan sistem integrasi pemrograman Interaktif. 1.2 Pemrograman interaktif disusun kedalam langkah kerja sesuai aset multimedia.',
                ],
                [
                    'name' => 'pertanyaan_20',
                    'label' => 'Elemen: Membangun pemrograman interaktif • Kriteria Unjuk Kerja: 2.1 Pemrograman interaktif ditetapkan sesuai dengan langkah kerja. 2.2 Sistem integrasi dibangun interaktif melalui multimedia pemrograman yang sesuai dengan langkah kerja.',
                ],
                [
                    'name' => 'pertanyaan_21',
                    'label' => 'Elemen: Melakukan uji coba UX • Kriteria Unjuk Kerja: 1.1 Kriteria sampel program/perangkat lunak evaluasi ditentukan sesuai dengan instrument uji coba. 1.2 Instrument uji coba dipersiapkan sesuai dengan karakteristik produk yang diuji. 1.3 Uji coba dilakukan sesuai prosedur pengujian.',
                ],
                [
                    'name' => 'pertanyaan_22',
                    'label' => 'Elemen: Menvalidasi hasil uji coba UX • Kriteria Unjuk Kerja: 2.1 Hasil uji coba dianalisis sesuai dengan tujuan pengujian. 2.2 Hasil analisis dituangkan ke dalam laporan sesuai pengujian.',
                ],
            ],
        ],
        'JWP' => [
            'file_slug' => 'junior-web-programmer',
            'question_format' => 'radio', // Format checkbox K/BK terpisah
            'questions' => [
                [
                    'name' => 'pertanyaan_1',
                    'label' => 'Elemen: Mengidentifikasi konsep data dan struktur data • Kriteria Unjuk Kerja: 1.1 Konsep data dan struktur data diidentifikasi sesuai dengan konteks permasalahan. 1.2 Alternatif struktur data dibandingkan kelebihan dan kekurangannya untuk konteks permasalahan yang diselesaikan.',
                ],
                [
                    'name' => 'pertanyaan_2',
                    'label' => 'Elemen: Menerapkan struktur data dan akses terhadap struktur data tersebut • Kriteria Unjuk Kerja: 2.1 Struktur data diimplementasikan sesuai dengan bahasa pemrograman yang akan dipergunakan. 2.2 Akses terhadap data dinyatakan dalam algoritma yang efisiensi sesuai bahasa pemrograman yang akan dipakai.',
                ],
                [
                    'name' => 'pertanyaan_3',
                    'label' => 'Elemen: Mengidentifikasi rancangan user interface • Kriteria Unjuk Kerja: 1.1 Rancangan user interface diidentifikasi sesuai kebutuhan. 1.2 Komponen user interface dialog diidentifikasi sesuai konteks rancangan proses. 1.3 Urutan dari akses komponen user interface dialog dijelaskan. 1.4 Simulasi (mock-up) dari aplikasi yang akan dikembangkan dibuat.',
                ],
                [
                    'name' => 'pertanyaan_4',
                    'label' => 'Elemen: Melakukan implementasi rancangan user interface • Kriteria Unjuk Kerja: 1.1. Menu program sesuai dengan rancangan program diterapkan. 1.2. Penempatan user interface dialog diatur secara sekuensial. 1.3. Setting aktif-pasif komponen user interface dialog disesuaikan dengan urutan alur proses. 1.4. Bentuk style dari komponen user interface ditentukan. 1.5. Penerapan simulasi dijadikan suatu proses yang sesungguhnya.',
                ],
                [
                    'name' => 'pertanyaan_5',
                    'label' => 'Elemen: Memilih tools pemrograman yang sesuai dengan kebutuhan • Kriteria Unjuk Kerja: 1.1 Platform (lingkungan) yang akan digunakan untuk menjalankan tools pemrograman diidentifikasi sesuai dengan kebutuhan. 1.2 Tools bahasa pemrogram dipilih sesuai dengan kebutuhaan dan lingkungan pengembangan.',
                ],
                [
                    'name' => 'pertanyaan_6',
                    'label' => 'Elemen: Instalasi tool pemrograman • Kriteria Unjuk Kerja: 2.1 Tools pemrogaman ter-install sesuai dengan prosedur. 2.2 Tools pemrograman bisa dijalankan di lingkungan pengembangan yang telah ditetapkan.',
                ],
                [
                    'name' => 'pertanyaan_7',
                    'label' => 'Elemen: Menerapkan hasil pemodelan kedalam eksekusi script sederhana • Kriteria Unjuk Kerja: 3.1 Script (source code) sederhana dibuat sesuai tools pemrogaman yang di-install 3.2 Script dapat dijalankan dengan benar dan menghasilkan keluaran sesuai skenario yang diharapkan',
                ],
                [
                    'name' => 'pertanyaan_8',
                    'label' => 'Elemen: Menerapkan coding- guidelines dan best practices dalam penulisan program (kode sumber) • Kriteria Unjuk Kerja: 1.1 Kode sumber dituliskan mengikuti coding-guidelines dan best practices. 1.2 Struktur program yang sesuai dengan konsep paradigmanya dibuat. 1.3 Galat/error ditangani.',
                ],
                [
                    'name' => 'pertanyaan_9',
                    'label' => 'Elemen: Menggunakan ukuran performansi dalam menuliskan kode sumber • Kriteria Unjuk Kerja: 2.1 Efisiensi penggunaan resources oleh kode dihitung. 2.2 Kemudahan interaksi selalu di- implementasikan sesuai standar yang berlaku.',
                ],
                [
                    'name' => 'pertanyaan_10',
                    'label' => 'Elemen: Menggunakan tipe data dan control program • Kriteria Unjuk Kerja: 1.1 Tipe data yang sesuai standar ditentukan. 1.2 Syntax program yang dikuasai digunakan sesuai standar 1.3 Struktur kontrol program yang dikuasai digunakan sesuai standar.',
                ],
                [
                    'name' => 'pertanyaan_11',
                    'label' => 'Elemen: Membuat program sederhana • Kriteria Unjuk Kerja: 2.1 Program baca tulis untuk memasukkan data dari keyboard dan menampilkan ke layar monitor termasuk variasinya sesuai standar masukan/keluaran telah dibuat. 2.2 Struktur kontrol percabangan dan pengulangan dalam membuat program telah digunakan.',
                ],
                [
                    'name' => 'pertanyaan_12',
                    'label' => 'Elemen: Membuat program menggunakan prosedur dan fungsi • Kriteria Unjuk Kerja: 3.1 Program dengan menggunakan prosedur dibuat sesuai aturan penulisan program. 3.2 Program dengan menggunakan fungsi dibuat sesuai aturan penulisan program. 3.3 Program dengan menggunakan prosedur dan fungsi secara bersamaan dibuat sesuai aturan penulisan program. 3.4 Keterangan untuk setiap prosedur dan fungsi telah diberikan.',
                ],
                [
                    'name' => 'pertanyaan_13',
                    'label' => 'Elemen: Membuat program menggunakan array • Kriteria Unjuk Kerja: 4.1 Dimensi array telah ditentukan. 4.2 Tipe data array telah ditentukan. 4.3 Panjang array telah ditentukan. 4.4 Pengurutan array telah digunakan.',
                ],
                [
                    'name' => 'pertanyaan_14',
                    'label' => 'Elemen: Membuat program untuk akses file • Kriteria Unjuk Kerja: 5.1 Program untuk menulis data dalam media penyimpan telah dibuat. 5.2 Program untuk membaca data dari media penyimpan telah dibuat.',
                ],
                [
                    'name' => 'pertanyaan_15',
                    'label' => 'Elemen: Mengkompilasi Program • Kriteria Unjuk Kerja: 6.1 Kesalahan program telah dikoreksi. 6.2 Kesalahan syntax dalam program telah dibebaskan.',
                ],
                [
                    'name' => 'pertanyaan_16',
                    'label' => 'Elemen: Melakukan pemilihan unit-unit reuse yang potensial • Kriteria Unjuk Kerja: 1.1 Class unit-unit reuse (dari aplikasi lain) yang sesuai dapat diidentifikasi. 1.2 Keuntungan efisiensi dari pemanfaatan komponen reuse dapat dihitung. 1.3 Lisensi, Hak cipta dan hak paten tidak dilanggar dalam pemanfaatan komponen reuse tersebut.',
                ],
                [
                    'name' => 'pertanyaan_17',
                    'label' => 'Elemen: Melakukan integrasi library atau komponen pre-existing dengan source code yang ada • Kriteria Unjuk Kerja: 2.1 Ketergantungan antar unit diidentifikasi. 2.2 Penggunaan komponen yang sudah obsolete dihindari. 2.3 Program yang dihubungkan dengan library diterapkan.',
                ],
                [
                    'name' => 'pertanyaan_18',
                    'label' => 'Elemen: Melakukan pembaharuan library atau komponen pre- existing yang digunakan • Kriteria Unjuk Kerja: 3.1 Cara-cara pembaharuan library atau komponen pre-existing diidentifikasi. 3.2 Pembaharuan library atau komponen pre- existing berhasil dilakukan.',
                ],
                [
                    'name' => 'pertanyaan_19',
                    'label' => 'Elemen: Melakukan identifikasi kode program • Kriteria Unjuk Kerja: 1.1 Modul program diidentifikasi 1.2 Parameter yang dipergunakan diidentifikasi 1.3 Algoritma dijelaskan cara kerjanya 1.4 Komentar setiap baris kode termasuk data, eksepsi, fungsi, prosedur dan class (bila ada) diberikan',
                ],
                [
                    'name' => 'pertanyaan_20',
                    'label' => 'Elemen: Membuat dokumentasi modul program • Kriteria Unjuk Kerja: 2.1 Dokumentasi modul dibuat sesuai dengan identitas untuk memudahkan pelacakan 2.2 Identifikasi dokumentasi diterapkan 2.3 Kegunaan modul dijelaskan 2.4 Dokumen direvisi sesuai perubahan kode program',
                ],
                [
                    'name' => 'pertanyaan_21',
                    'label' => 'Elemen: Membuat dokumentasi fungsi, prosedur atau method program • Kriteria Unjuk Kerja: 3.1 Dokumentasi fungsi, prosedur atau metod dibuat 3.2 Kemungkinan eksepsi dijelaskan 3.3 Dokumen direvisi sesuai perubahan kode program',
                ],
                [
                    'name' => 'pertanyaan_22',
                    'label' => 'Elemen: Men-generate dokumentasi • Kriteria Unjuk Kerja: 4.1 Tools untuk generate dokumentasi diidentifikasi 4.2 Generate dokumentasi dilakukan',
                ],
                [
                    'name' => 'pertanyaan_23',
                    'label' => 'Elemen: Mempersiapkan kode program • Kriteria Unjuk Kerja: 1.1 Kode program sesuai spesifikasi disiapkan. 1.2 Debugging tools untuk melihat proses suatu modul dipersiapkan.',
                ],
                [
                    'name' => 'pertanyaan_24',
                    'label' => 'Elemen: Melakukan debugging • Kriteria Unjuk Kerja: 2.1 Kode program dikompilasi sesuai bahasa pemrograman yang digunakan. 2.2 Kriteria lulus build dianalisis. 2.3 Kriteria eksekusi aplikasi dianalisis. 2.4 Kode kesalahan dicatat.',
                ],
                [
                    'name' => 'pertanyaan_25',
                    'label' => 'Elemen: Memperbaiki program • Kriteria Unjuk Kerja: 3.1 Perbaikan terhadap kesalahan kompilasi maupun build dirumuskan. 3.2 Perbaikan dilakukan.',
                ],
            ],
        ],
        'PB' => [
            'file_slug' => 'pemrograman-basisdata',
            'question_format' => 'radio', // Format checkbox K/BK terpisah
            'questions' => [
                [
                    'name' => 'pertanyaan_1',
                    'label' => 'Elemen: Mengindentifikasi tools yang akan digunakan • Kriteria Unjuk Kerja: 1.1 Kebutuhan tools perangkat lunak diidentifikasi dari dokumen yang tersedia. 1.2 Kemungkinan penggunaan tools yang tersedia diidentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_2',
                    'label' => 'Elemen: Menggunakan tools perangkat lunak • Kriteria Unjuk Kerja: 2.1. Tools pengembangan dipilih sesuai kebutuhan lingkungan pengembangan. 2.2. Penggunaan tools pengembangan yang diperlukan diuji coba. 2.3 Risiko pengembangan sistem dengan menggunakan tools tersebut diidentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_3',
                    'label' => 'Elemen: Menganalisis library, komponen, atau framework yang sesuai dengan konteks • Kriteria Unjuk Kerja: 1.1 Ruang lingkup kebutuhan akan library, komponen atau framework diidentifikasikan sesuai lingkungan pengembangan. 1.2 Keuntungan penggunaan dibandingkan dengan mengembangkan sendiri diidentifikasikan',
                ],
                [
                    'name' => 'pertanyaan_4',
                    'label' => 'Elemen: Membuat proof of concept library, komponen atau framework berdasarkan konteks kebutuhan • Kriteria Unjuk Kerja: 2.1 Fitur-fitur terkait penggunaan library, komponen atau framework versi sederhana dibuat. 2.2 Manfaat penggunaan didemostrasikan.',
                ],
                [
                    'name' => 'pertanyaan_5',
                    'label' => 'Elemen: Merancang integrasi dan batasan penggunaan library, komponen atau framework • Kriteria Unjuk Kerja: 3.1 Rencana integrasi ditentukan. 3.2 Limitasi diidentifikasikan.',
                ],
                [
                    'name' => 'pertanyaan_6',
                    'label' => 'Elemen: Mengidentifikasi konsep data dan struktur data • Kriteria Unjuk Kerja: 1.1 Konsep data dan struktur data diidentifikasi sesuai dengan konteks permasalahan. 1.2 Alternatif struktur data dibandingkan kelebihan dan kekurangannya untuk konteks permasalahan yang diselesaikan.',
                ],
                [
                    'name' => 'pertanyaan_7',
                    'label' => 'Elemen: Menerapkan struktur data dan akses terhadap struktur data tersebut • Kriteria Unjuk Kerja: 2.1 Struktur data diimplementasikan sesuai dengan bahasa pemrograman yang akan dipergunakan. 2.2 Akses terhadap data dinyatakan dalam algoritma yang efisiensi sesuai bahasa pemrograman yang akan dipakai.',
                ],
                [
                    'name' => 'pertanyaan_8',
                    'label' => 'Elemen: Mengidentifikasi entitas yang terkait dengan lingkup program yang akan dibuat beserta hubungannya • Kriteria Unjuk Kerja: 1.1 Entitas yang menggambarkan sistem yang dibuat dapat diidentifikasikan sesuai dokumen perancangan. 1.2 Berbagai diagram dapat dibuat dari entity yang telah didefinisikan.',
                ],
                [
                    'name' => 'pertanyaan_9',
                    'label' => 'Elemen: Membuat query informasi dasar terhadap model data yang telah dikembangkan • Kriteria Unjuk Kerja: 2.1 Informasi yang diperlukan oleh aplikasi dapat dihasilkan dengan efisien dari model yang dibuat. 2.2 Diagram berdasar entitas dan hubungan yang telah diidentifikasi dapat diimplementasikan menggunakan tools yang ada',
                ],
                [
                    'name' => 'pertanyaan_10',
                    'label' => 'Elemen: Mempersiapkan perangkat lunak aplikasi data deskripsi/SQL • Kriteria Unjuk Kerja: 1.1 Perangkat lunak aplikasi SQL telah dipasang. 1.2 Perangkat lunak aplikasi SQL dijalankan.',
                ],
                [
                    'name' => 'pertanyaan_11',
                    'label' => 'Elemen: Menggunakan fitur aplikasi SQL • Kriteria Unjuk Kerja: 2.1 Fitur pengolahan DML diidentifikasikan. 2.2 Fitur pengolahan DML dieksekusi sesuai kebutuhan.',
                ],
                [
                    'name' => 'pertanyaan_12',
                    'label' => 'Elemen: Mengisi tabel • Kriteria Unjuk Kerja: 3.1 Tabel diisi data menggunakan perintah DML. 3.2 Indeks dibangkitkan. 3.3 View tabel dibentuk sesuai kebutuhan.',
                ],
                [
                    'name' => 'pertanyaan_13',
                    'label' => 'Elemen: Melakukan operasi relasional • Kriteria Unjuk Kerja: 4.1 Fitur pengolahan DML diidentifikasikan. 4.2 Perintah DML dipergunakan untuk manipulasi antar tabel 4.3 Perintah DML dipergunakan untuk manipulasi antar view. 4.4 Perintah DML ditulis secara efisien',
                ],
                [
                    'name' => 'pertanyaan_14',
                    'label' => 'Elemen: Membuat stored procedure • Kriteria Unjuk Kerja: 5.1 Stored Procedure dibuat dengan perintah SQL. 5.2 Prosedur diuji diperiksa input dan output-nya.',
                ],
                [
                    'name' => 'pertanyaan_15',
                    'label' => 'Elemen: Membuat function • Kriteria Unjuk Kerja: 6.1 Function dibuat dengan perintah SQL. 6.2 Perintah SQL pada function ditulis secara efisien.',
                ],
                [
                    'name' => 'pertanyaan_16',
                    'label' => 'Elemen: Membuat trigger • Kriteria Unjuk Kerja: 7.1 Trigger didefinisikan dengan perintah SQL. 7.2 Kesesuaian hasil trigger diuji.',
                ],
                [
                    'name' => 'pertanyaan_17',
                    'label' => 'Elemen: Melakukan perintah commit dan rollback • Kriteria Unjuk Kerja: 8.1 Perubahan data dengan perintah commit dilakukan. 8.2 Pembatalan penulisan data dilakukan dengan rollback.',
                ],
                [
                    'name' => 'pertanyaan_18',
                    'label' => 'Elemen: Membuat berbagai operasi terhadap basis data • Kriteria Unjuk Kerja: 1.1 Data dapat disimpan/diubah ke dalam format basis data. 1.2 Informasi yang diinginkan dapat dihasilkan menggunakan query tersebut. 1.3 Indeks dipergunakan untuk mempercepat akses.',
                ],
                [
                    'name' => 'pertanyaan_19',
                    'label' => 'Elemen: Membuat prosedur akses terhadap basis data • Kriteria Unjuk Kerja: 2.1 Library akses basis data dapat diterapkan. 2.2 Perintah akses data yang relevan dengan teknologi atau jenis baru data, diterapkan untuk mengakses data.',
                ],
                [
                    'name' => 'pertanyaan_20',
                    'label' => 'Elemen: Membuat koneksi basis data • Kriteria Unjuk Kerja: 3.1 Teknologi koneksi yang sesuai dipilih. 3.2 Keamanan koneksi ditentukan. 3.3 Hak setiap pengguna ditentukan.',
                ],
                [
                    'name' => 'pertanyaan_21',
                    'label' => 'Elemen: Menguji program basis data • Kriteria Unjuk Kerja: 4.1 Skenario pengujian disiapkan. 4.2 Logika pemrograman mengacu pada kinerja statement akses data yang akan dibaca. 4.3 Performansi mengacu pada kinerja statement akses data yang akan dibaca data diuji',
                ],
                [
                    'name' => 'pertanyaan_22',
                    'label' => 'Elemen: Menjelaskan varian dan invarian • Kriteria Unjuk Kerja: 1.1 Tipe data telah dijelaskan sesuai kaidah pemrograman. 1.2 Variabel telah dijelaskan sesuai kaidah pemrograman. 1.3 Konstanta telah dijelaskan sesuai kaidah pemrograman.',
                ],
                [
                    'name' => 'pertanyaan_23',
                    'label' => 'Elemen: Membuat alur logika pemrograman • Kriteria Unjuk Kerja: 2.1 Metode yang sesuai ditentukan. 2.2 Komponen yang dibutuhkan ditentukan. 2.3 Relasi antar komponen ditetapkan. 2.4 Alur mulai dan selesai ditetapkan.',
                ],
                [
                    'name' => 'pertanyaan_24',
                    'label' => 'Elemen: Menerapkan teknik dasar algoritma umum • Kriteria Unjuk Kerja: 3.1 Algoritma untuk sorting dibuat. 3.2 Algoritma untuk searching dibuat.',
                ],
                [
                    'name' => 'pertanyaan_25',
                    'label' => 'Elemen: Menggunakan prosedur dan fungsi • Kriteria Unjuk Kerja: 4.1 Konsep penggunaan kembali prosedur dan fungsi dapat diidentifikasi. 4.2 Prosedur dapat digunakan. 4.3 Fungsi dapat digunakan.',
                ],
                [
                    'name' => 'pertanyaan_26',
                    'label' => 'Elemen: Mengidentifikasikan kompleksitas algoritma • Kriteria Unjuk Kerja: 5.1 Kompleksitas waktu algoritma diidentifikasi. 5.2 Kompleksitas penggunaan memory algoritma diidentifikasi',
                ],
                [
                    'name' => 'pertanyaan_27',
                    'label' => 'Elemen: Mempersiapkan kode program • Kriteria Unjuk Kerja: 1.1 Kode program sesuai spesifikasi disiapkan. 1.2 Debugging tools untuk melihat proses suatu modul dipersiapkan.',
                ],
                [
                    'name' => 'pertanyaan_28',
                    'label' => 'Elemen: Melakukan debugging • Kriteria Unjuk Kerja: 2.1 Kode program dikompilasi sesuai bahasa pemrograman yang digunakan. 2.2 Kriteria lulus build dianalisis. 2.3 Kriteria eksekusi aplikasi dianalisis. 2.4 Kode kesalahan dicatat.',
                ],
                [
                    'name' => 'pertanyaan_29',
                    'label' => 'Elemen: Memperbaiki program • Kriteria Unjuk Kerja: 3.1 Perbaikan terhadap kesalahan kompilasi maupun build dirumuskan. 3.2 Perbaikan dilakukan',
                ],
                [
                    'name' => 'pertanyaan_30',
                    'label' => 'Elemen: Mengevaluasi kesesuaian kode dengan spesifikasinya • Kriteria Unjuk Kerja: 1.1 Kesesuaian kode dengan ketentuan yang ada diidentifikasi. 1.2 Ketidak-sesuaian kode dengan ketentuan diidentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_31',
                    'label' => 'Elemen: Memperbaiki kode sesuai dengan codingguidelines dan bestpractices • Kriteria Unjuk Kerja: 2.1 Kode yang tidak sesuai coding-guideline diperbaiki tanpa berubah spesifikasinya. 2.2 Kode yang tidak menerapkan bestpractices diperbaiki',
                ],
                [
                    'name' => 'pertanyaan_32',
                    'label' => 'Elemen: Membuat pengecualian penulisan kode terhadap codingguidelines • Kriteria Unjuk Kerja: 3.1 Kode yang memang sebaiknya tidak perlu sesuai coding-guideline diidentifikasi. 3.2 Komentar yang menjelaskan kode pengecualian ditulis.',
                ],
                [
                    'name' => 'pertanyaan_33',
                    'label' => 'Elemen: Melakukan analisis keberadaan dan kebutuhan environment • Kriteria Unjuk Kerja: 1.1 Jumlah keberadaan environment diidentifikasi sesuai kebutuhan. 1.2 Spesifikasi masing-masing environment diidentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_34',
                    'label' => 'Elemen: Melakukan konfigurasi perangkat lunak masing-masing environment • Kriteria Unjuk Kerja: 2.1. Konfigurasi environment yang menjadi bagian perangkat lunak dibuat. 2.2. Aktivasi konfigurasi perangkat lunak pada satu waktu dilakukan.',
                ],
                [
                    'name' => 'pertanyaan_35',
                    'label' => 'Elemen: Analisis permintaan perubahan • Kriteria Unjuk Kerja: 1.1 Hasil akhir perubahan pada aplikasi diidentifikasi. 1.2 Perbandingan perbedaan hasil akhir perubahan dengan kondisi existing dibuat',
                ],
                [
                    'name' => 'pertanyaan_36',
                    'label' => 'Elemen: Analisis komponen, modul yang perlu dimodifikasi • Kriteria Unjuk Kerja: 2.1 Analisis kelayakan atau ketidaklayakan komponen modul existing untuk dimodifikasi dilakukan. 2.2 Komponen dan modul yang perlu dimodifikasi untuk memfasilitasi perubahan diidentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_37',
                    'label' => 'Elemen: Analisis dampak perubahan dan efek samping perubahan • Kriteria Unjuk Kerja: 3.1 Dampak waktu dan jumlah orang untuk melakukan perubahan diidentifikasi. 3.2 Efek samping perubahan terhadap aplikasi diidentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_38',
                    'label' => 'Elemen: Analisis resources yang kritikal yang diperlukan aplikasi • Kriteria Unjuk Kerja: 1.1 Resources kritikal yang diperlukan diidentifikasi. 1.2 Batas atas sebelum failure untuk setiap sampai tahap kritis diidentifikasi.',
                ],
                [
                    'name' => 'pertanyaan_39',
                    'label' => 'Elemen: Membuat modul visualisasi penggunaan resources • Kriteria Unjuk Kerja: 2.1. Visualisasi penggunaan untuk masingmasing resources diidentifikasi. 2.2. Modul visualisasi serta tampilan batas atas dibuat.',
                ],
                [
                    'name' => 'pertanyaan_40',
                    'label' => 'Elemen: Menganalisis diferensiasi perangkat lunak yang terbaru dengan yang existing • Kriteria Unjuk Kerja: 1.1 Diferensiasi perangkat lunak diidentifikasikan. 1.2 Mekanisme pengaplikasian diferensiasi dirancang.',
                ],
                [
                    'name' => 'pertanyaan_41',
                    'label' => 'Elemen: Membuat pogram perangkat lunak penambahan diferensiasi • Kriteria Unjuk Kerja: 2.1. Program pertambahan diferensiasi dibuat 2.2. Program pertambahan diaplikasikan pada perangkat lunak.',
                ],
                [
                    'name' => 'pertanyaan_42',
                    'label' => 'Elemen: Mengidentifikasi standar keamanan informasi (seperti SNI‐ISO 27001, COBIT, dll) • Kriteria Unjuk Kerja: 1.1 Referensi standar keamanan informasi diidentifikasi. 1.2 Prioritas penerapan standar keamanan informasi organisasi disetujui oleh pimpinan organisasi.',
                ],
                [
                    'name' => 'pertanyaan_43',
                    'label' => 'Elemen: Mengevaluasi komponen pokok standar keamanan untuk menentukan apakah bisa diaplikasikan secara efektif untuk kebutuhan organisasi. • Kriteria Unjuk Kerja: 2.1 Daftar komponen pokok standar keamanan untuk kebutuhan organisasi disusun. 2.2 Rekomendasi hasil analisa standar keamanan untuk kebutuhan strategis organisasi dibuat.',
                ],
                [
                    'name' => 'pertanyaan_44',
                    'label' => 'Elemen: Menganalisa skema akses berbasis peran/tanggung jawab/jabatan untuk implementasi • Kriteria Unjuk Kerja: 3.1 Rincian pekerjaan untuk setiap peran/jabatan dalam organisasi dan akuntabilitas informasi untuk masing-masing peran/jabatan tersebut diidentifikasi. 3.2 Prosedur tentang tugas dan tanggungjawab yang terkait dengan keamanan sistem informasi dibuat.',
                ],
                [
                    'name' => 'pertanyaan_45',
                    'label' => 'Elemen: Menganalisis dan memilih referensi standar keamanan dalam tingkatan strategis • Kriteria Unjuk Kerja: 4.1 Risiko sistem informasi, analisa dampak bisnis dan rencana mitigasi disusun. 4.2 Referensi untuk pembuatan kebijakan dan prosedur keamanan informasi diseleksi',
                ],
            ],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $createdCount = 0;

        // Generate Template APL1 dan FR_AK_05 untuk semua skema yang dikonfigurasi (sama untuk semua)
        foreach ($this->commonSchemas as $schemaConfig) {
            $skema = Skema::where('kode', $schemaConfig['kode'])->first();

            if (!$skema) {
                $this->command->warn("⚠ Skema {$schemaConfig['nama']} ({$schemaConfig['kode']}) tidak ditemukan!");
                continue;
            }

            // Generate APL1
            $this->createApl1Template($skema, $schemaConfig);
            $createdCount++;

            // Generate FR_AK_05
            $this->createFrAk05Template($skema, $schemaConfig);
            $createdCount++;
        }

        // Generate Template APL2 untuk setiap skema yang dikonfigurasi (beda-beda pertanyaannya)
        foreach ($this->apl2Schemas as $kodeSkema => $apl2Config) {
            $skema = Skema::where('kode', $kodeSkema)->first();

            if (!$skema) {
                $this->command->warn("⚠ Skema dengan kode {$kodeSkema} tidak ditemukan!");
                continue;
            }

            $this->createApl2Template($skema, $apl2Config);
            $createdCount++;
        }

        // Summary
        $this->command->info('');
        $this->command->info('=== SELESAI ===');
        $this->command->info("Total {$createdCount} template berhasil dibuat/diperbarui!");
        $this->command->info('');
        $this->command->warn('⚠ CATATAN: File template (.docx) perlu diupload manual melalui admin panel!');
    }

    /**
     * Buat atau Update Template APL1
     */
    private function createApl1Template(Skema $skema, array $config): void
    {
        $apl1Variables = [
            'user.name',
            'user.email',
            'user.telephone',
            'user.nik',
            'user.tempat_lahir',
            'user.tanggal_lahir',
            'user.jenis_kelamin',
            'user.pendidikan',
            'ttd_digital_asesi'
        ];

        $apl1CustomVariables = [
            [
                'name' => 'ttd_digital_asesi',
                'role' => 'asesi',
                'type' => 'signature_pad',
                'label' => 'Tanda Tangan Asesi',
                'options' => null,
                'required' => '1'
            ],
        ];

        TemplateMaster::updateOrCreate(
            [
                'tipe_template' => 'APL1',
                'skema_id' => $skema->id,
            ],
            [
                'deskripsi' => "Template APL 1 (Asesmen Mandiri) untuk Skema {$config['nama']}",
                'file_path' => "templates/{$config['file_slug']}-apl1.docx",
                'is_active' => true,
                'variables' => $apl1Variables,
                'custom_variables' => $apl1CustomVariables,
            ]
        );

        $this->command->info("✓ Template Master {$config['nama']} APL1 berhasil dibuat/diperbarui!");
    }

    /**
     * Buat atau Update Template APL2
     */
    private function createApl2Template(Skema $skema, array $config): void
    {
        $questions = $config['questions'];
        $fileSlug = $config['file_slug'];
        $questionFormat = $config['question_format'] ?? 'radio'; // Default radio untuk backward compatibility

        // Generate variables dari pertanyaan
        $variables = [
            'user.name',
            'user.email',
            'asesor.name',
            'jadwal.tanggal_ujian',
        ];

        // Generate custom_variables dari pertanyaan
        $customVariables = [];

        if ($questionFormat === 'checkbox_separate') {
            // Format checkbox terpisah (K/BK) untuk Analis Program
            foreach ($questions as $question) {
                $questionName = $question['name'];

                // Tambahkan variabel untuk K (Kompeten)
                $variables[] = "{$questionName}_k";
                $customVariables[] = [
                    'name' => "{$questionName}_k",
                    'role' => 'asesi',
                    'type' => 'checkbox',
                    'label' => "{$question['label']} - Kompeten (K)",
                    'options' => null,
                    'required' => '0'
                ];

                // Tambahkan variabel untuk BK (Belum Kompeten)
                $variables[] = "{$questionName}_bk";
                $customVariables[] = [
                    'name' => "{$questionName}_bk",
                    'role' => 'asesi',
                    'type' => 'checkbox',
                    'label' => "{$question['label']} - Belum Kompeten (BK)",
                    'options' => null,
                    'required' => '0'
                ];

                // Tambahkan variabel untuk bukti yang relevan
                // $variables[] = "{$questionName}_bukti";
                // $customVariables[] = [
                //     'name' => "{$questionName}_bukti",
                //     'role' => 'asesi',
                //     'type' => 'textarea',
                //     'label' => "{$question['label']} - Bukti yang relevan",
                //     'options' => null,
                //     'required' => '0'
                // ];
            }
        } else {
            // Format radio (BK/K) untuk System Analyst dan skema lainnya
            foreach ($questions as $question) {
                $variables[] = $question['name'];
                $customVariables[] = [
                    'name' => $question['name'],
                    'role' => 'asesi',
                    'type' => 'radio',
                    'label' => $question['label'],
                    'options' => 'BK,K',
                    'required' => '1'
                ];
            }
        }

        $variables[] = 'ttd_digital_asesi';
        $variables[] = 'ttd_digital_asesor';

        // Tambahkan TTD
        $customVariables[] = [
            'name' => 'ttd_digital_asesi',
            'role' => 'asesi',
            'type' => 'signature_pad',
            'label' => 'Tanda Tangan Asesi',
            'options' => null,
            'required' => '1'
        ];

        $customVariables[] = [
            'name' => 'ttd_digital_asesor',
            'role' => 'asesor',
            'type' => 'signature_pad',
            'label' => 'Tanda Tangan Asesor',
            'options' => null,
            'required' => '1'
        ];

        TemplateMaster::updateOrCreate(
            [
                'tipe_template' => 'APL2',
                'skema_id' => $skema->id,
            ],
            [
                'deskripsi' => "Template APL 2 (Portofolio) untuk Skema {$skema->nama}",
                'file_path' => "templates/{$fileSlug}-apl2.docx",
                'is_active' => true,
                'variables' => $variables,
                'custom_variables' => $customVariables,
            ]
        );

        $questionCount = count($questions);
        $formatInfo = $questionFormat === 'checkbox_separate' ? 'format checkbox K/BK terpisah' : 'format radio BK/K';
        $this->command->info("✓ Template Master {$skema->nama} APL2 berhasil dibuat/diperbarui dengan {$questionCount} pertanyaan ({$formatInfo}) + 2 TTD (Asesi & Asesor)!");
    }

    /**
     * Buat atau Update Template FR_AK_05
     */
    private function createFrAk05Template(Skema $skema, array $config): void
    {
        $fileSlug = $config['file_slug'];

        TemplateMaster::updateOrCreate(
            [
                'tipe_template' => 'FR_AK_05',
                'skema_id' => $skema->id,
            ],
            [
                'deskripsi' => "Template FR AK 05 (Form Asesmen Asesor) untuk Skema {$skema->nama}",
                'file_path' => "templates/{$fileSlug}-fr-ak-05.docx",
                'is_active' => true,
                'variables' => [
                    'asesor.name',
                    'jadwal.tanggal_ujian',
                    'ttd_digital_asesor'
                ],
                'custom_variables' => [
                    [
                        'name' => 'ttd_digital_asesor',
                        'role' => 'asesor',
                        'type' => 'signature_pad',
                        'label' => 'Tanda Tangan Asesor',
                        'options' => null,
                        'required' => '1'
                    ],
                ],
            ]
        );

        $this->command->info("✓ Template Master {$skema->nama} FR AK 05 berhasil dibuat/diperbarui!");
    }
}
