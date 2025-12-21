<?php

namespace Database\Seeders;

use App\Models\TemplateMaster;
use App\Models\Skema;
use Illuminate\Database\Seeder;

class SystemAnalystTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil Skema System Analyst
        $skemaSystemAnalyst = Skema::where('kode', 'SA')->first();

        if (!$skemaSystemAnalyst) {
            $this->command->error('Skema System Analyst (SA) tidak ditemukan! Jalankan SkemaSeeder terlebih dahulu.');
            return;
        }

        // 1. Buat atau Update Template APL1 untuk System Analyst
        TemplateMaster::updateOrCreate(
            [
                'tipe_template' => 'APL1',
                'skema_id' => $skemaSystemAnalyst->id,
            ],
            [
                'deskripsi' => 'Template APL 1 (Asesmen Mandiri) untuk Skema System Analyst',
                'file_path' => 'templates/system-analyst-apl1.docx', // TODO: Upload file manual
                'is_active' => true,
                'variables' => [
                    'user.name',
                    'user.email',
                    'user.telephone',
                    'user.nik',
                    'user.tempat_lahir',
                    'user.tanggal_lahir',
                    'user.jenis_kelamin',
                    'user.pendidikan',
                    'ttd_digital_asesi'
                ],
                'custom_variables' => [
                    [
                        'name' => 'ttd_digital_asesi',
                        'role' => 'asesi',
                        'type' => 'signature_pad',
                        'label' => 'Tanda Tangan Asesi',
                        'options' => null,
                        'required' => '1'
                    ],
                ],
            ]
        );

        $this->command->info('✓ Template Master System Analyst APL1 berhasil dibuat/diperbarui!');

        // 2. Buat atau Update Template APL2 untuk System Analyst
        TemplateMaster::updateOrCreate(
            [
                'tipe_template' => 'APL2',
                'skema_id' => $skemaSystemAnalyst->id,
            ],
            [
                'deskripsi' => 'Template APL 2 (Portofolio) untuk Skema System Analyst',
                'file_path' => 'templates/system-analyst-apl2.docx', // Placeholder, nanti akan diupdate
                'is_active' => true,
                'variables' => [
                    'user.name',
                    'user.email',
                    'asesor.name',
                    'jadwal.tanggal_ujian',
                    'pertanyaan_1',
                    'pertanyaan_2',
                    'pertanyaan_3',
                    'pertanyaan_4',
                    'pertanyaan_5',
                    'pertanyaan_6',
                    'pertanyaan_7',
                    'pertanyaan_8',
                    'pertanyaan_9',
                    'pertanyaan_10',
                    'pertanyaan_11',
                    'pertanyaan_12',
                    'pertanyaan_13',
                    'pertanyaan_14',
                    'pertanyaan_15',
                    'pertanyaan_16',
                    'pertanyaan_17',
                    'pertanyaan_18',
                    'pertanyaan_19',
                    'pertanyaan_20',
                    'pertanyaan_21',
                    'pertanyaan_22',
                    'pertanyaan_23',
                    'pertanyaan_24',
                    'pertanyaan_25',
                    'pertanyaan_26',
                    'pertanyaan_27',
                    'pertanyaan_28',
                    'pertanyaan_29',
                    'pertanyaan_30',
                    'pertanyaan_31',
                    'pertanyaan_32',
                    'pertanyaan_33',
                    'pertanyaan_34',
                    'pertanyaan_35',
                    'pertanyaan_36',
                    'pertanyaan_37',
                    'pertanyaan_38',
                    'pertanyaan_39',
                    'pertanyaan_40',
                    'pertanyaan_41',
                    'pertanyaan_42',
                    'pertanyaan_43',
                    'pertanyaan_44',
                    'pertanyaan_45',
                    'pertanyaan_46',
                    'pertanyaan_47',
                    'pertanyaan_48',
                    'pertanyaan_49',
                    'pertanyaan_50',
                    'pertanyaan_51',
                    'ttd_digital_asesi',
                    'ttd_digital_asesor',
                ],
                'custom_variables' => [
                    // Pertanyaan 1-6 (yang sudah ada)
                    [
                        'name' => 'pertanyaan_1',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Membuat berbagai operasi terhadap basis data • Kriteria Unjuk Kerja: 1.1 Data dapat disimpan/diubah ke dalam format basis data 1.2 Informasi yang diinginkan dapat dihasilkan menggunakan query tersebut 1.3 Indeks dipergunakan untuk mempercepat akses',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Membuat prosedur akses terhadap basis data • Kriteria Unjuk Kerja: 2.1 Library akses basis data dapat diterapkan 2.2 Perintah akses data yang relevan dengan teknologi atau jenis baru data, diterapkan untuk mengakses data',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Membuat Koneksi basis data • Kriteria Unjuk Kerja: 3.1 Teknologi koneksi yang sesuai dipilih 3.2 Keamanan koneksi ditentukan 3.3 Hak setiap pengguna ditentukan',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Menguji program basis data • Kriteria Unjuk Kerja: 4.1 Skenario pengujian disiapkan 4.2 Logika pemrograman mengacu pada kinerja statement akses data yang akan dibaca 4.3 Performansi mengacu pada kinerja statement akses data yang akan dibaca diuji.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Menentukan kebutuhan uji coba dalam pengembangan • Kriteria Unjuk Kerja: 1.1 Prosedur uji coba aplikasi diidentifikasikan sesuai dengan software development life cycle. 1.2 Tools uji coba ditentukan. 1.3 Standar dan kondisi uji coba diidentifikasi.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_6',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Mempersiapkan dokumentasi uji coba • Kriteria Unjuk Kerja: 2.1 Kebutuhan untuk uji coba ditentukan. 2.2 Uji coba dengan variasi kondisi dapat dilaksanakan. 2.3 Skenario uji coba dibuat.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    // Pertanyaan 7-9 (lanjutan dari screenshot pertama)
                    [
                        'name' => 'pertanyaan_7',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Mempersiapkan data uji • Kriteria Unjuk Kerja: 3.1 Data uji unit tes diidentifikasi. 3.2 Data uji unit tes dibangkitkan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_8',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Melaksanakan prosedur uji coba • Kriteria Unjuk Kerja: 4.1 Skenario uji coba didesain. 4.2 Prosedur uji coba dalam algoritma didesain. 4.3 Uji coba dilaksanakan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_9',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Mengevaluasi hasil uji coba • Kriteria Unjuk Kerja: 5.1 Hasil uji coba dicatat. 5.2 Hasil uji coba dianalisis. 5.3 Prosedur uji coba dilaporkan. 5.4 Kesalahan/error diselesaikan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    // Pertanyaan 10-15 (dari screenshot kedua)
                    [
                        'name' => 'pertanyaan_10',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Menjelaskan varian dan invariant • Kriteria Unjuk Kerja: 1.1 Tipe data telah dijelaskan sesuai kaidah pemrograman. 1.2 Variabel telah dijelaskan sesuai kaidah pemrograman 1.3 Konstanta telah dijelaskan sesuai kaidah pemrograman',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_11',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Membuat alur logika pemrograman • Kriteria Unjuk Kerja: 2.1 Metode yang sesuai ditentukan. 2.2 Komponen yang dibutuhkan ditentukan 2.3 Relasi antara komponen ditetapkan 2.4 Alur mulai dan selesai ditetapkan',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_12',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Menerapkan Teknik dasar algoritma umum • Kriteria Unjuk Kerja: 3.1 Algoritma untuk sorting dibuat. 3.2 Algoritma untuk searching dibuat.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_13',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Menggunakan prosedur dan fungsi • Kriteria Unjuk Kerja: 4.1 Konsep penggunaan Kembali prosedur dan fungsi dapat di identifikasi 4.2 Prosedur dapat digunakan. 4.3 Fungsi dapat digunakan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_14',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Membuat stored procedure • Kriteria Unjuk Kerja: 5.1 Stored Procedure dibuat dengan perintah SQL. 5.2 Prosedur diuji diperiksa input dan output-nya.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_15',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Mengidentifikasikan kompleksitas algoritma • Kriteria Unjuk Kerja: 6.1 Kompleksitas waktu algoritma di identifikasi 6.2 Kompleksitas waktu algoritma di identifikasi.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    // Pertanyaan 16-21 (dari screenshot ketiga)
                    [
                        'name' => 'pertanyaan_16',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Mempersiapkan dokumentasi peralatan dan lingkungan pengujian integrasi • Kriteria Unjuk Kerja: 1.1 Peralatan pengujian ditentukan sesuai dengan scenario pengujian 1.2 Dokumen pendukung pengujian disiapkan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_17',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Mempersiapkan data uji • Kriteria Unjuk Kerja: 2.1 Data uji integrasi program di identifikasi. 2.2 Data uji integrasi program dibangkitkan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_18',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Melaksanakan pengujian integrasi • Kriteria Unjuk Kerja: 3.1 Modul program dijalankan sesuai dengan prosedur yang ditetapkan. 3.2 Data atau kondisi sebagai masukkan, diinputkan ke dalam sistem 3.3 Hasil pengujian dicatat dalam lembar pengujian.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_19',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Menganalisis data pengujian integrasi • Kriteria Unjuk Kerja: 4.1 Modul yang terkait dianalisis sesuai dengan standar pengembangan perangkat lunak yang berlaku. 4.2 Data hasil keluaran dievaluasi kesesuaiannya dengan data yang direncanakan. 4.3 Status pada lembar pengujian dari hasil perbandingan data tersebut dicatat ke dalam lembar pengujian 4.4 Kondisi data yang tidak sesuai dan perkiraan kondisi tersebut dicatat ke dalam lembar hasil uji.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_20',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Melaporkan hasil pengujian integrasi • Kriteria Unjuk Kerja: 5.1 Peralatan yang digunakan untuk pengujian dicatat ke dalam peralatan pengujian. 5.2 Kondisi yang terjadi selama pengujian dicatat ke dalam lembar pengujian. 5.3 Data yang diimplementasikan dan data hasil pengujian dicatat. 5.4 Analisis hasil pengujian dicatat sesuai dengan standar dokumentasi pengembangan perangkat lunak yang berlaku',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_21',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Mengidentifikasikan kompleksitas algoritma • Kriteria Unjuk Kerja: 6.1 Kompleksitas waktu algoritma di identifikasi 6.2 Kompleksitas waktu algoritma di identifikasi.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    // Pertanyaan 22-29 (dari screenshot keempat)
                    [
                        'name' => 'pertanyaan_22',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Mempersiapkan kode program • Kriteria Unjuk Kerja: 1.1 Kode program sesuai spesifikasi disiapkan. 1.2 Debugging tools untuk melihat proses suatu modul dipersiapkan',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_23',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Melakukan debugging • Kriteria Unjuk Kerja: 2.1 Kode program dikompilasi sesuai bahasa pemrograman yang digunakan. 2.2 Kriteria lulus build dianalisis. 2.3 Kriteria eksekusi aplikasi dianalisis.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_24',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Memperbaiki program • Kriteria Unjuk Kerja: 3.1 Perbaikan terhadap kesalahan kompilasi maupun build dirumuskan. 3.2 Perbaikan dilakukan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_25',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Mengumpulkan data waktu eksekusi komponen komponen yang ada pada program • Kriteria Unjuk Kerja: 1.1 Waktu eksekusi function, procedure atau method program yang diukur. 1.2 Penggunaan memory eksekusi function, procedure atau method program yang diukur.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_26',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Menentukan bottleneck performa yang ada pada program • Kriteria Unjuk Kerja: 2.1 Bottleneck performa pada program diidentifikasi. 2.2 Dampak negatif bottleneck terhadap performa diidentifikasi.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_27',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Merancang solusi untuk mengurangi/menghilangkan bottleneck • Kriteria Unjuk Kerja: 3.1 Rancangan metode dijelaskan. 3.2 Peningkatan performa rancangan metode ditunjukkan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_28',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Menentukan kompleksitas algoritma • Kriteria Unjuk Kerja: 4.1 Algoritma pada program terindikasi bermasalah diidentifikasikan. 4.2 Metode untuk mengukur kompleksitas terhadap algoritma diidentifikasikan. 4.3 Kompleksitas algoritma yang berdampak penurunan performa diidentifikasikan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_29',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Melakukan identifikasi kode program • Kriteria Unjuk Kerja: 1.1 Modul program diidentifikasi 1.2 Parameter yang dipergunakan diidentifikasi. 1.3 Algoritma dijelaskan cara kerjanya 1.4 Komentar setiap baris kode termasuk data, eksepsi, fungsi, prosedur dan class (bila ada)',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    // Pertanyaan 30-32 (dari screenshot kelima)
                    [
                        'name' => 'pertanyaan_30',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Menggunakan fitur aplikasi SQL • Kriteria Unjuk Kerja: 2.1 Fitur pengolahan DML diidentifikasikan. 2.2 Fitur pengolahan DML dieksekusi sesuai kebutuhan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_31',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Mengisi tabel • Kriteria Unjuk Kerja: 3.1 Tabel diisi data menggunakan perintah DML. 3.2 Indeks dibangkitkan. 3.3 View tabel dibentuk sesuai kebutuhan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_32',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Membuat dokumentasi modul program • Kriteria Unjuk Kerja: 4.1 Dokumentasi modul dibuat sesuai dengan identitas untuk memudahkan pelacakan 4.2 Identifikasi dokumentasi diterapkan 4.3 Kegunaan modul dijelaskan 4.4 Dokumen direvisi sesuai perubahan kode program',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    // Pertanyaan 33-39 (dari screenshot keenam)
                    [
                        'name' => 'pertanyaan_33',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Membuat dokumentasi fungsi, prosedur atau method program • Kriteria Unjuk Kerja: 5.1 Dokumentasi fungsi, prosedur atau metod dibuat 5.2 Prosedur diuji diperiksa input dan output-nya. 5.3 Dokumen direvisi sesuai perubahan kode program',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_34',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Men-generate dokumentasi • Kriteria Unjuk Kerja: 5.1 Tools untuk generate dokumentasi diidentifikasi 5.2 Generate dokumentasi dilakukan',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_35',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Mengevaluasi kesesuaian kode dengan spesifikasinya • Kriteria Unjuk Kerja: 1.1 Kesesuaian kode dengan ketentuan yang ada diidentifikasikan. 1.2 Ketidak-sesuaian kode dengan ketentuan diidentifikasi.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_36',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Memperbaiki kode sesuai dengan coding-guideline dan best-practices • Kriteria Unjuk Kerja: 2.1 Kode yang tidak sesuai coding-guideline diperbaiki tanpa berubah spesifikasinya. 2.2 Kode yang tidak menerapkan best-practices diperbaiki.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_37',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Membuat pengecualian penulisan kode terhadap coding-guidelines • Kriteria Unjuk Kerja: 3.1 Kode yang memang sebaiknya tidak perlu sesuai coding-guideline diidentifikasi. 3.2 Komentar yang menjelaskan kode pengecualian ditulis. 3.3 View tabel dibentuk sesuai kebutuhan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_38',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Mengumpulkan kebutuhan skalabilitas • Kriteria Unjuk Kerja: 1.1 Lingkup (scope) sistem teridentifikasi. 1.2 Lingkungan operasi aplikasi teridentifikasi.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_39',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Menganalisis kebutuhan skalabilitas • Kriteria Unjuk Kerja: 2.1 Masalah skalabilitas dianalisis berdasar lingkup dan lingkungan operasi sistem. 2.2 Kompleksitas aplikasi dianalisis sesuai dengan kebutuhan pemrosesan dan jumlah data/pengguna yang akan terlibat 2.3 Kebutuhan perangkat keras dianalisis. 2.4 Hasil analisis didokumentasikan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    // Pertanyaan 40-47 (dari screenshot ketujuh)
                    [
                        'name' => 'pertanyaan_40',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Mempersiapkan perangkat lunak aplikasi data deskripsi/SQL • Kriteria Unjuk Kerja: 1.1 Perangkat lunak aplikasi SQL telah dipasang. 1.2 Perangkat lunak aplikasi SQL dijalankan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_41',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Menggunakan fitur aplikasi SQL • Kriteria Unjuk Kerja: 2.1 Fitur pengolahan DML diidentifikasikan. 2.2 Fitur pengolahan DML dieksekusi sesuai kebutuhan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_42',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Mengisi tabel • Kriteria Unjuk Kerja: 3.1 Tabel diisi data menggunakan perintah DML. 3.2 Indeks dibangkitkan. 3.3 View tabel dibentuk sesuai kebutuhan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_43',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Melakukan operasi relasional • Kriteria Unjuk Kerja: 4.1 Fitur pengolahan DML diidentifikasikan. 4.2 Perintah DML dipergunakan untuk manipulasi antar tabel 4.3 Perintah DML dipergunakan untuk manipulasi antar view. 4.4 Perintah DML ditulis secara efisien',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_44',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Membuat stored procedure • Kriteria Unjuk Kerja: 5.1 Stored Procedure dibuat dengan perintah SQL. 5.2 Prosedur diuji diperiksa input dan output-nya.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_45',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Membuat function • Kriteria Unjuk Kerja: 6.1 Function dibuat dengan perintah SQL. 6.2 Perintah SQL pada function ditulis secara efisien.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_46',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Membuat trigger • Kriteria Unjuk Kerja: 7.1 Trigger didefinisikan dengan perintah SQL. 7.2 Kesesuaian hasil trigger diuji.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_47',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Melakukan perintah commit dan rollback • Kriteria Unjuk Kerja: 8.1 Perubahan data dengan perintah commit dilakukan. 8.2 Pembatalan penulisan data dilakukan dengan rollback.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    // Pertanyaan 48-51 (dari screenshot kedelapan)
                    [
                        'name' => 'pertanyaan_48',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Membuat berbagai operasi terhadap basis data • Kriteria Unjuk Kerja: 1.1 Data dapat disimpan/diubah ke dalam format basis data. 1.2 Informasi yang diinginkan dapat dihasilkan menggunakan query tersebut. 1.3 Indeks dipergunakan untuk mempercepat akses.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_49',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Membuat prosedur akses terhadap basis data • Kriteria Unjuk Kerja: 2.1 Library akses basis data dapat diterapkan. 2.2 Perintah akses data yang relevan dengan teknologi atau jenis baru data, diterapkan untuk mengakses data.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_50',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Membuat koneksi basis data • Kriteria Unjuk Kerja: 3.1 Teknologi koneksi yang sesuai dipilih. 3.2 Keamanan koneksi ditentukan. 3.3 Hak setiap pengguna ditentukan.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_51',
                        'role' => 'asesi',
                        'type' => 'radio',
                        'label' => 'Elemen: Menguji program basis data • Kriteria Unjuk Kerja: 4.1 Skenario pengujian disiapkan. 4.2 Logika pemrograman mengacu pada kinerja statement akses data yang akan dibaca. 4.3 Performansi mengacu pada kinerja statement akses data yang akan dibaca data diuji.',
                        'options' => 'BK,K',
                        'required' => '1'
                    ],
                    // TTD Digital Asesi
                    [
                        'name' => 'ttd_digital_asesi',
                        'role' => 'asesi',
                        'type' => 'signature_pad',
                        'label' => 'Tanda Tangan Asesi',
                        'options' => null,
                        'required' => '1'
                    ],
                    // TTD Digital Asesor
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

        $this->command->info('✓ Template Master System Analyst APL2 berhasil dibuat/diperbarui dengan 51 pertanyaan + 2 TTD (Asesi & Asesor)!');

        // 3. Buat atau Update Template FR_AK_05 untuk System Analyst
        TemplateMaster::updateOrCreate(
            [
                'tipe_template' => 'FR_AK_05',
                'skema_id' => $skemaSystemAnalyst->id,
            ],
            [
                'deskripsi' => 'Template FR AK 05 (Form Asesmen Asesor) untuk Skema System Analyst',
                'file_path' => 'templates/system-analyst-fr-ak-05.docx', // TODO: Upload file manual
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

        $this->command->info('✓ Template Master System Analyst FR AK 05 berhasil dibuat/diperbarui!');
        $this->command->info('');
        $this->command->info('=== SELESAI ===');
        $this->command->info('Total 3 template berhasil dibuat/diperbarui:');
        $this->command->info('  1. APL1 - dengan TTD Asesi');
        $this->command->info('  2. APL2 - dengan 51 pertanyaan + TTD Asesi & Asesor');
        $this->command->info('  3. FR AK 05 - dengan TTD Asesor');
        $this->command->info('');
        $this->command->warn('⚠ CATATAN: File template (.docx) perlu diupload manual melalui admin panel!');
    }
}
