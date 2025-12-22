<?php

namespace Database\Seeders;

use App\Models\BankSoal;
use App\Models\Skema;
use Illuminate\Database\Seeder;

class SystemAnalystBankSoalSeeder extends Seeder
{
    /**
     * Mapping file slug per skema untuk generate file_path
     */
    private array $skemaFileSlugs = [
        'SA' => 'system-analyst',
        'AP' => 'analis-program',
        'ASP' => 'asisten-pemrograman',
    ];

    /**
     * Konfigurasi bank soal per skema
     * Tambahkan konfigurasi skema baru dengan bank soal masing-masing
     */
    private array $bankSoalSchemas = [
        'SA' => [
            'FR IA 03' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 03 - Pertanyaan untuk mendukung observasi',
                'question_format' => 'textarea', // Format textarea standar
                'variables' => [
                    'user.name',
                    'asesor.name',
                    'jadwal.tanggal_ujian',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Tindakan apa yang akan anda lakukan apabila terdapat ketidaksesuaian pada desain yang dibuat (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Tindakan apa yang akan anda lakukan apabila menemukan ketidaksesuaian dalam meninjau ulang kebutuhan perangkat lunak (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Jelaskan langkah apa yang akan anda lakukan, apabila terjadi perubahan kebutuhan implementasi perancangan UI yang sebelumnya berbasis desktop ke berbasis web atau berbasis mobile (TrS)',
                    ],
                ],
            ],
            'FR IA 06' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 06 - Pertanyaan tulis essai',
                'question_format' => 'textarea', // Format textarea standar
                'variables' => [
                    'user.name',
                    'jadwal.tanggal_ujian',
                    'jadwal.waktu_mulai',
                    'asesor.name',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Pertama melakukan identifikasi kebutuhan user, kemudian mengklasifikasikan hasil identifikasi kebutuhan user tersebut ke kebutuhan Fungsional dan Non-Fungsional. Setelah itu dilakukan validasi dan verifikasi kebutuhan kepada user.',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Cara menjembatani kebutuhan Perangkat Lunak apabila terjadi perbedaaan kebutuhan yaitu dengan mengidentifikasi dan mendokumentasikan pebedaan tersebut, kemudian lakukan Diskusi serta mediasi. Setelah itu lakukan prioritas kebutuhan.',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Yaitu dengan melakukan analisis dampak dari setiap identifikasi dari kebutuhan yang telah teridentifikasi.',
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'label' => 'Berikut langkah dalam menyusun high-level yang sesuai dengan standar yaitu dengan mengidentifikasi tujuan dari dokumen, kemudian mendeskripsikan system setelah itu lakukan persyaratan kebutuhan fungsional dan nonfungsional kemudian buat diagram alur kerja (work flow)',
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'label' => 'Dengan melakukan identifikasi kebutuhan dari pemangku kepentingan kemudian melakukannya dengan wawancara, analisis dokumen yang ada dan juga dilakukan Teknik observasi. Setelah semua terkumpul dapat dibuat daftar kebutuhan awal.',
                    ],
                    [
                        'name' => 'pertanyaan_6',
                        'label' => 'Tahapan dalam pengidentifikasiaan standar SKPL yaitu dengan menganalisis kebutuhan proyek setelah itu mengidentifikasi aspek khusus dengan mempelajari dokumen atau konsul dengan orang yang expert dibidangnya.',
                    ],
                    [
                        'name' => 'pertanyaan_7',
                        'label' => 'Langkah dalam identifikasi kelengkapan berkas / dokumen SKPL yaitu dengan membuat daftar elemen kebutuhan yang diperlukan terlebih dahulu, kemudian melakukan pemeriksaan dengan memeriksa daftar checklist. Setelah itu hasilnya lakukan review oleh pemangku kepentingan. Kemudian buat dokumen sesuai dengan template yang ada dan pastikan melakukan penomoran dengan nomer identifikasi untuk setiap kebutuhan yang ada.',
                    ],
                    [
                        'name' => 'pertanyaan_8',
                        'label' => 'Yang pertama harus paham tentang kebutuhan bisnis dan pengguna, kemudian memilih metode pengujian sesuai dengan uji fungsional, uji integritas dan uji system. Lakukan identifikasi pengguna kemudian dari hasil tersebut dirangcang prototype dan melakukan uji scenario.',
                    ],
                ],
            ],
            'FR IA 07' => [
                'target' => 'asesor',
                'keterangan' => 'FR IA 07 - Ceklis observasi asesor',
                'question_format' => 'textarea', // Format textarea standar
                'variables' => [
                    'user.name',
                    'asesor.name',
                    'jadwal.tanggal_ujian',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Jelaskan tahapan dalam metodologi pengembangan sistem yang dapat digunakan dalam merancang [ DPL1 ] TS',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Bagaimana melakukan elisitasi kebutuhan perangkat lunak yang sesuai dengan kebutuhan stakeholder [ DPL2 ] TMS',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Tindakan apa yang akan dilakukan pada saat wawancara ketika anda dihadapkan pada keterbatasan waktu wawancara untuk menemukan kebutuhan perangkat lunak yang sesuai [DPL3] CMS',
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'label' => 'Bagaimana Langkah-langkah mengklasifikasikan kebutuhan perangkat lunak berdasarkan kategori fungsional atau non-fungsional, yang sesuai dengan kebutuhan [ DPL4 ] JRES',
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'label' => 'Jelaskan bagaimana menjembatani Kebutuhan Perangkat lunak apabila terjadi perbedaan kebutuhan antar stakeholder [ DPL5 ] TrS',
                    ],
                    [
                        'name' => 'pertanyaan_6',
                        'label' => 'Bagaimana Langkah-langkah dalam menyusun dokumen high-level system yang berhubungan antara sistem/perangkat lunak dengan pengguna sesuai dengan standar pengembangan perangkat lunak [DPL 6] JRES',
                    ],
                    [
                        'name' => 'pertanyaan_7',
                        'label' => 'Bagaimana melakukan mengidentifikasi spesifikasi Kebutuhan sistem sesuai dengan standar pengembangan perangkat lunak [ DPL 7 ] TMS',
                    ],
                    [
                        'name' => 'pertanyaan_8',
                        'label' => 'Bagaimana tahapan mengidentifikasikan template/standard spesifikasi kebutuhan perangkat lunak sesuai dengan standar pengembangan perangkat lunak [ DPL 8 ] JRES',
                    ],
                    [
                        'name' => 'pertanyaan_9',
                        'label' => 'Jelaskan bagaimana mengidentifikasi kelengkapan berkas/dokumen spesifikasi kebutuhan perangkat lunak sesuai dengan standar pengembangan perangkat lunak [ DPL 9 ] TRs',
                    ],
                    [
                        'name' => 'pertanyaan_10',
                        'label' => 'Bagaimana Langkah-langkah dalam mengidentifikasi prototipe, test scenario, dan test script [ DPL 10 ] JRES',
                    ],
                    [
                        'name' => 'pertanyaan_11',
                        'label' => 'Jelaskan bagaimana menerapkan architectural style perangkat lunak sesuai dengan standar pengembangan perangkat lunak [ DPL 11 ] TrS',
                    ],
                    [
                        'name' => 'pertanyaan_12',
                        'label' => 'Apabila pada saat memodelkan system perangkat lunak terdapat kebutuhan merubah model system, Langkah apa yang akan kalian lakukan agar kebutuhan user dapat terakomodir dengan baik sesuai dengan standar pengembangan perangkat lunak [ DPL 12 ] CMS',
                    ],
                    [
                        'name' => 'pertanyaan_13',
                        'label' => 'Bagaimana anda memastikan bahwa mekanisme interaksi yang tepat dalam UI sesuai dengan kebutuhan user [ DPL 13 ] TMS',
                    ],
                ],
            ],
        ],
        'ASP' => [
            'FR IA 03' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 03 - Pertanyaan untuk mendukung observasi',
                'question_format' => 'checkbox_ya_tdk', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'asesor.name',
                    'jadwal.tanggal_ujian',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Apakah langkah yang saudara lakukan untuk memilih platform operating system dan bahasa pemrograman pada sebuah perangkat lunak? (JRES)',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Apakah langkah yang anda lakukan untuk menentukan menu pada sebuah aplikasi? (TS)',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Jelaskan langkah penerapan inheritance pada sebuah class? (TS)',
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'label' => 'Jelaskan langkah-langkah dalam membuat dokumen kode program? (TS)',
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'label' => 'Jelaskan pengertian konsep penerapan versi kode program? (JRES)',
                    ],
                    [
                        'name' => 'pertanyaan_6',
                        'label' => 'Jelaskan langkah saudara dalam membuat scenario uji coba? (TS)',
                    ],
                    [
                        'name' => 'pertanyaan_7',
                        'label' => 'Apakah yang Anda lakukan apabila terjadi kegagalan pada saat melakukan instalasi software tools pemrograman android studio? (TS)',
                    ],
                    [
                        'name' => 'pertanyaan_8',
                        'label' => 'Jelaskan langkah yang saudara lakukan untuk melakukan pengecekan fitur-fitur dasar pemrograman pada tools pemrograman android studio? (TS)',
                    ],
                    [
                        'name' => 'pertanyaan_9',
                        'label' => 'Bagaimana Anda akan menangani situasi darurat atau masalah yang mungkin muncul selama proses instalasi sistem operasi? (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_10',
                        'label' => 'Bagaimana Anda akan merencanakan dan mengatur tahapan instalasi perangkat lunak atau aplikasi? (TMS)',
                    ],
                    [
                        'name' => 'pertanyaan_11',
                        'label' => 'Jelaskan spesifikasi komputer yang anda gunakan? (TS)',
                    ],
                    [
                        'name' => 'pertanyaan_12',
                        'label' => 'Bagaimana Anda akan menerapkan pengetahuan dan pengalaman Anda dalam memilih perangkat lunak (software) dan perangkat keras (hardware) untuk sebuah aplikasi ke dalam proyek baru yang sedang Anda kerjakan? (TRS)',
                    ],
                ],
            ],
            'FR IA 06' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 06 - Pertanyaan tulis essai',
                'question_format' => 'checkbox_ya_tdk', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'jadwal.tanggal_ujian',
                    'jadwal.waktu_mulai',
                    'asesor.name',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Jika anda anda memiliki job description sebagai asisten programmer dan diberikan tugas membangun aplikasi mobile, bagaimana langkah anda dapat membangun aplikasi berbasis mobile? (TMS)',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Sebutkan tools Bahasa pemrograman yang sesuai kebutuhan dan lingkungan pengembang dan berikan contoh script untuk menampilkan tulisan "SAYA SEORANG ASISTEN PEMROGRAM" berdasarkan bahasa pemrograman sesuai pilihan anda (TS)',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Jika pada saat uji coba ditemukan "Syntax Error", bagaimana cara anda menemukan dan memperbaiki error tersebut?(CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'label' => 'Dalam skenario di mana sistem operasi pada perangkat yang diberikan tidak kompatibel dengan software tools pemrograman yang dibutuhkan, jelaskan langkah-langkah apa yang akan Anda ambil untuk menyelesaikan masalah tersebut agar dapat melanjutkan proses pengembangan aplikasi mobile. (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'label' => 'Sebagai seorang pengembang perangkat lunak, bagaimana Anda akan memastikan bahwa pemilihan platform operating system dan bahasa pemrograman yang Anda lakukan sesuai dengan kebutuhan proyek pengembangan aplikasi mobile berbasis Android? (JRES)',
                    ],
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

        foreach ($this->bankSoalSchemas as $kodeSkema => $bankSoalConfigs) {
            $skema = Skema::where('kode', $kodeSkema)->first();

            if (!$skema) {
                $this->command->warn("⚠ Skema dengan kode {$kodeSkema} tidak ditemukan!");
                continue;
            }

            foreach ($bankSoalConfigs as $tipe => $config) {
                $this->createBankSoal($skema, $tipe, $config);
                $createdCount++;
            }
        }

        // Summary
        $this->command->info('');
        $this->command->info('=== SELESAI ===');
        $this->command->info("Total {$createdCount} Bank Soal berhasil dibuat/diperbarui!");
        $this->command->info('');
        $this->command->warn('⚠ CATATAN: File template (.docx) perlu diupload manual melalui admin panel!');
    }

    /**
     * Buat atau Update Bank Soal
     */
    private function createBankSoal(Skema $skema, string $tipe, array $config): void
    {
        $questions = $config['questions'];
        $target = $config['target'];
        $keterangan = $config['keterangan'];
        $baseVariables = $config['variables'] ?? [];
        $questionFormat = $config['question_format'] ?? 'textarea'; // Default textarea untuk backward compatibility

        // Generate variables dari pertanyaan
        $variables = array_merge($baseVariables, []);
        $customVariables = [];

        if ($questionFormat === 'checkbox_ya_tdk') {
            // Format checkbox Ya/Tdk terpisah untuk FR IA 03 Asisten Pemrograman
            foreach ($questions as $question) {
                $questionName = $question['name'];

                // Tambahkan variabel untuk textarea tanggapan
                $variables[] = $questionName;
                $customVariables[] = [
                    'name' => $questionName,
                    'role' => $target,
                    'type' => 'textarea',
                    'label' => "{$question['label']} - Tanggapan",
                    'mapping' => null,
                    'options' => null,
                    'required' => '0'
                ];

                // Tambahkan variabel untuk checkbox Ya
                $variables[] = "{$questionName}_ya";
                $customVariables[] = [
                    'name' => "{$questionName}_ya",
                    'role' => $target,
                    'type' => 'checkbox',
                    'label' => "{$question['label']} - Ya",
                    'mapping' => null,
                    'options' => null,
                    'required' => '0'
                ];

                // Tambahkan variabel untuk checkbox Tdk
                $variables[] = "{$questionName}_tdk";
                $customVariables[] = [
                    'name' => "{$questionName}_tdk",
                    'role' => $target,
                    'type' => 'checkbox',
                    'label' => "{$question['label']} - Tdk",
                    'mapping' => null,
                    'options' => null,
                    'required' => '0'
                ];
            }
        } else {
            // Format textarea standar untuk System Analyst dan skema lainnya
            foreach ($questions as $question) {
                $variables[] = $question['name'];
                $customVariables[] = [
                    'name' => $question['name'],
                    'role' => $target,
                    'type' => 'textarea',
                    'label' => $question['label'],
                    'mapping' => null,
                    'options' => null,
                    'required' => '1'
                ];
            }
        }

        // Tambahkan TTD sesuai target
        if ($target === 'asesi') {
            $variables[] = 'ttd_digital_asesi';
            $variables[] = 'ttd_digital_asesor';

            $customVariables[] = [
                'name' => 'ttd_digital_asesi',
                'role' => 'asesi',
                'type' => 'signature_pad',
                'label' => 'Tanda Tangan Asesi',
                'mapping' => null,
                'options' => null,
                'required' => '1'
            ];
        } else {
            $variables[] = 'ttd_digital_asesor';
        }

        $customVariables[] = [
            'name' => 'ttd_digital_asesor',
            'role' => 'asesor',
            'type' => 'signature_pad',
            'label' => 'Tanda Tangan Asesor',
            'mapping' => null,
            'options' => null,
            'required' => '1'
        ];

        // Generate file_path dan original_filename
        $fileSlug = $this->skemaFileSlugs[$skema->kode] ?? strtolower(str_replace(' ', '-', $skema->nama));
        $tipeSlug = strtolower(str_replace(' ', '-', $tipe));
        $filePath = "bank-soal/{$fileSlug}-{$tipeSlug}.docx";
        $originalFilename = "{$skema->nama} {$tipe}.docx";

        BankSoal::updateOrCreate(
            [
                'skema_id' => $skema->id,
                'tipe' => $tipe,
            ],
            [
                'target' => $target,
                'is_active' => true,
                'keterangan' => $keterangan,
                'file_path' => $filePath,
                'original_filename' => $originalFilename,
                'variables' => $variables,
                'custom_variables' => $customVariables,
            ]
        );

        $questionCount = count($questions);
        $formatInfo = $questionFormat === 'checkbox_ya_tdk' ? 'format checkbox Ya/Tdk terpisah' : 'format textarea';
        $ttdInfo = $target === 'asesi' ? '2 TTD (Asesi & Asesor)' : '1 TTD (Asesor)';
        $this->command->info("✓ Bank Soal {$skema->nama} {$tipe} berhasil dibuat/diperbarui dengan {$questionCount} pertanyaan ({$formatInfo}) + {$ttdInfo}!");
    }
}
