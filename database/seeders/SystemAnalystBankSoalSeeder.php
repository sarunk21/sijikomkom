<?php

namespace Database\Seeders;

use App\Models\BankSoal;
use App\Models\Skema;
use Illuminate\Database\Seeder;

class SystemAnalystBankSoalSeeder extends Seeder
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

        // 1. FR IA 03 - Sudah ada di database, skip atau update jika perlu
        BankSoal::updateOrCreate(
            [
                'skema_id' => $skemaSystemAnalyst->id,
                'tipe' => 'FR IA 03',
            ],
            [
                'nama' => 'System Analyst FR IA 03',
                'target' => 'asesi',
                'is_active' => true,
                'keterangan' => 'FR IA 03 - Pertanyaan untuk mendukung observasi',
                'variables' => [
                    'user.name',
                    'asesor.name',
                    'jadwal.tanggal_ujian',
                    'pertanyaan_1',
                    'pertanyaan_2',
                    'pertanyaan_3',
                    'ttd_digital_asesi',
                    'ttd_digital_asesor'
                ],
                'custom_variables' => [
                    [
                        'name' => 'pertanyaan_1',
                        'role' => 'asesi',
                        'type' => 'textarea',
                        'label' => 'Tindakan apa yang akan anda lakukan apabila terdapat ketidaksesuaian pada desain yang dibuat (CMS)',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'role' => 'asesi',
                        'type' => 'textarea',
                        'label' => 'Tindakan apa yang akan anda lakukan apabila menemukan ketidaksesuaian dalam meninjau ulang kebutuhan perangkat lunak (CMS)',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'role' => 'asesi',
                        'type' => 'textarea',
                        'label' => 'Jelaskan langkah apa yang akan anda lakukan, apabila terjadi perubahan kebutuhan implementasi perancangan UI yang sebelumnya berbasis desktop ke berbasis web atau berbasis mobile (TrS)',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'ttd_digital_asesi',
                        'role' => 'asesi',
                        'type' => 'signature_pad',
                        'label' => 'Tanda Tangan Asesi',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'ttd_digital_asesor',
                        'role' => 'asesor',
                        'type' => 'signature_pad',
                        'label' => 'Tanda Tangan Asesor',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                ],
            ]
        );

        $this->command->info('✓ Bank Soal System Analyst FR IA 03 berhasil dibuat/diperbarui!');

        // 2. FR IA 06 - Update dengan pertanyaan dari screenshot
        BankSoal::updateOrCreate(
            [
                'skema_id' => $skemaSystemAnalyst->id,
                'tipe' => 'FR IA 06',
            ],
            [
                'nama' => 'System Analyst FR IA 06',
                'target' => 'asesi',
                'is_active' => true,
                'keterangan' => 'FR IA 06 - Pertanyaan tulis essai',
                'variables' => [
                    'user.name',
                    'jadwal.tanggal_ujian',
                    'jadwal.waktu_mulai',
                    'asesor.name',
                    'pertanyaan_1',
                    'pertanyaan_2',
                    'pertanyaan_3',
                    'pertanyaan_4',
                    'pertanyaan_5',
                    'pertanyaan_6',
                    'pertanyaan_7',
                    'pertanyaan_8',
                    'ttd_digital_asesi',
                    'ttd_digital_asesor'
                ],
                'custom_variables' => [
                    [
                        'name' => 'pertanyaan_1',
                        'role' => 'asesi',
                        'type' => 'textarea',
                        'label' => 'Pertama melakukan identifikasi kebutuhan user, kemudian mengklasifikasikan hasil identifikasi kebutuhan user tersebut ke kebutuhan Fungsional dan Non-Fungsional. Setelah itu dilakukan validasi dan verifikasi kebutuhan kepada user.',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'role' => 'asesi',
                        'type' => 'textarea',
                        'label' => 'Cara menjembatani kebutuhan Perangkat Lunak apabila terjadi perbedaaan kebutuhan yaitu dengan mengidentifikasi dan mendokumentasikan pebedaan tersebut, kemudian lakukan Diskusi serta mediasi. Setelah itu lakukan prioritas kebutuhan.',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'role' => 'asesi',
                        'type' => 'textarea',
                        'label' => 'Yaitu dengan melakukan analisis dampak dari setiap identifikasi dari kebutuhan yang telah teridentifikasi.',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'role' => 'asesi',
                        'type' => 'textarea',
                        'label' => 'Berikut langkah dalam menyusun high-level yang sesuai dengan standar yaitu dengan mengidentifikasi tujuan dari dokumen, kemudian mendeskripsikan system setelah itu lakukan persyaratan kebutuhan fungsional dan nonfungsional kemudian buat diagram alur kerja (work flow)',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'role' => 'asesi',
                        'type' => 'textarea',
                        'label' => 'Dengan melakukan identifikasi kebutuhan dari pemangku kepentingan kemudian melakukannya dengan wawancara, analisis dokumen yang ada dan juga dilakukan Teknik observasi. Setelah semua terkumpul dapat dibuat daftar kebutuhan awal.',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_6',
                        'role' => 'asesi',
                        'type' => 'textarea',
                        'label' => 'Tahapan dalam pengidentifikasiaan standar SKPL yaitu dengan menganalisis kebutuhan proyek setelah itu mengidentifikasi aspek khusus dengan mempelajari dokumen atau konsul dengan orang yang expert dibidangnya.',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_7',
                        'role' => 'asesi',
                        'type' => 'textarea',
                        'label' => 'Langkah dalam identifikasi kelengkapan berkas / dokumen SKPL yaitu dengan membuat daftar elemen kebutuhan yang diperlukan terlebih dahulu, kemudian melakukan pemeriksaan dengan memeriksa daftar checklist. Setelah itu hasilnya lakukan review oleh pemangku kepentingan. Kemudian buat dokumen sesuai dengan template yang ada dan pastikan melakukan penomoran dengan nomer identifikasi untuk setiap kebutuhan yang ada.',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_8',
                        'role' => 'asesi',
                        'type' => 'textarea',
                        'label' => 'Yang pertama harus paham tentang kebutuhan bisnis dan pengguna, kemudian memilih metode pengujian sesuai dengan uji fungsional, uji integritas dan uji system. Lakukan identifikasi pengguna kemudian dari hasil tersebut dirangcang prototype dan melakukan uji scenario.',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'ttd_digital_asesi',
                        'role' => 'asesi',
                        'type' => 'signature_pad',
                        'label' => 'Tanda Tangan Asesi',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'ttd_digital_asesor',
                        'role' => 'asesor',
                        'type' => 'signature_pad',
                        'label' => 'Tanda Tangan Asesor',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                ],
            ]
        );

        $this->command->info('✓ Bank Soal System Analyst FR IA 06 berhasil dibuat/diperbarui dengan 8 pertanyaan + 2 TTD!');

        // 3. FR IA 07 - Ceklis observasi asesor (13 pertanyaan)
        BankSoal::updateOrCreate(
            [
                'skema_id' => $skemaSystemAnalyst->id,
                'tipe' => 'FR IA 07',
            ],
            [
                'nama' => 'System Analyst FR IA 07',
                'target' => 'asesor',
                'is_active' => true,
                'keterangan' => 'FR IA 07 - Ceklis observasi asesor',
                'variables' => [
                    'user.name',
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
                    'ttd_digital_asesor'
                ],
                'custom_variables' => [
                    [
                        'name' => 'pertanyaan_1',
                        'role' => 'asesor',
                        'type' => 'textarea',
                        'label' => 'Jelaskan tahapan dalam metodologi pengembangan sistem yang dapat digunakan dalam merancang [ DPL1 ] TS',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'role' => 'asesor',
                        'type' => 'textarea',
                        'label' => 'Bagaimana melakukan elisitasi kebutuhan perangkat lunak yang sesuai dengan kebutuhan stakeholder [ DPL2 ] TMS',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'role' => 'asesor',
                        'type' => 'textarea',
                        'label' => 'Tindakan apa yang akan dilakukan pada saat wawancara ketika anda dihadapkan pada keterbatasan waktu wawancara untuk menemukan kebutuhan perangkat lunak yang sesuai [DPL3] CMS',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'role' => 'asesor',
                        'type' => 'textarea',
                        'label' => 'Bagaimana Langkah-langkah mengklasifikasikan kebutuhan perangkat lunak berdasarkan kategori fungsional atau non-fungsional, yang sesuai dengan kebutuhan [ DPL4 ] JRES',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'role' => 'asesor',
                        'type' => 'textarea',
                        'label' => 'Jelaskan bagaimana menjembatani Kebutuhan Perangkat lunak apabila terjadi perbedaan kebutuhan antar stakeholder [ DPL5 ] TrS',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_6',
                        'role' => 'asesor',
                        'type' => 'textarea',
                        'label' => 'Bagaimana Langkah-langkah dalam menyusun dokumen high-level system yang berhubungan antara sistem/perangkat lunak dengan pengguna sesuai dengan standar pengembangan perangkat lunak [DPL 6] JRES',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_7',
                        'role' => 'asesor',
                        'type' => 'textarea',
                        'label' => 'Bagaimana melakukan mengidentifikasi spesifikasi Kebutuhan sistem sesuai dengan standar pengembangan perangkat lunak [ DPL 7 ] TMS',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_8',
                        'role' => 'asesor',
                        'type' => 'textarea',
                        'label' => 'Bagaimana tahapan mengidentifikasikan template/standard spesifikasi kebutuhan perangkat lunak sesuai dengan standar pengembangan perangkat lunak [ DPL 8 ] JRES',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_9',
                        'role' => 'asesor',
                        'type' => 'textarea',
                        'label' => 'Jelaskan bagaimana mengidentifikasi kelengkapan berkas/dokumen spesifikasi kebutuhan perangkat lunak sesuai dengan standar pengembangan perangkat lunak [ DPL 9 ] TRs',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_10',
                        'role' => 'asesor',
                        'type' => 'textarea',
                        'label' => 'Bagaimana Langkah-langkah dalam mengidentifikasi prototipe, test scenario, dan test script [ DPL 10 ] JRES',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_11',
                        'role' => 'asesor',
                        'type' => 'textarea',
                        'label' => 'Jelaskan bagaimana menerapkan architectural style perangkat lunak sesuai dengan standar pengembangan perangkat lunak [ DPL 11 ] TrS',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_12',
                        'role' => 'asesor',
                        'type' => 'textarea',
                        'label' => 'Apabila pada saat memodelkan system perangkat lunak terdapat kebutuhan merubah model system, Langkah apa yang akan kalian lakukan agar kebutuhan user dapat terakomodir dengan baik sesuai dengan standar pengembangan perangkat lunak [ DPL 12 ] CMS',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'pertanyaan_13',
                        'role' => 'asesor',
                        'type' => 'textarea',
                        'label' => 'Bagaimana anda memastikan bahwa mekanisme interaksi yang tepat dalam UI sesuai dengan kebutuhan user [ DPL 13 ] TMS',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                    [
                        'name' => 'ttd_digital_asesor',
                        'role' => 'asesor',
                        'type' => 'signature_pad',
                        'label' => 'Tanda Tangan Asesor',
                        'mapping' => null,
                        'options' => null,
                        'required' => '1'
                    ],
                ],
            ]
        );

        $this->command->info('✓ Bank Soal System Analyst FR IA 07 berhasil dibuat/diperbarui dengan 13 pertanyaan + 2 TTD!');
        $this->command->info('');
        $this->command->info('=== SELESIA ===');
        $this->command->info('Total 3 Bank Soal berhasil dibuat/diperbarui:');
        $this->command->info('  1. FR IA 03 - dengan 3 pertanyaan textarea + 2 TTD (Target: Asesi)');
        $this->command->info('  2. FR IA 06 - dengan 8 pertanyaan textarea + 2 TTD (Target: Asesi)');
        $this->command->info('  3. FR IA 07 - dengan 13 pertanyaan textarea + 2 TTD (Target: Asesor)');
        $this->command->info('');
        $this->command->warn('⚠ CATATAN: File template (.docx) perlu diupload manual melalui admin panel!');
    }
}
