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
            'question_format' => 'checkbox_separate', // Format checkbox K/BK terpisah
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
            'question_format' => 'checkbox_separate', // Format checkbox K/BK terpisah
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
