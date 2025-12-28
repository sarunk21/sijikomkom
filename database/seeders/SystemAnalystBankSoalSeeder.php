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
        'JADE' => 'data-engineer',
        'JADS' => 'data-scientist',
        'CSA' => 'cyber-security-analyst',
        'DMM' => 'designer-multimedia-madya',
        'JWP' => 'junior-web-programmer',
        'PB' => 'pemrograman-basisdata',
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
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
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
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
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
        'AP' => [
            'FR IA 06' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 06 - Pertanyaan tulis essai',
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'jadwal.tanggal_ujian',
                    'jadwal.waktu_mulai',
                    'asesor.name',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Mengapa analisis kebutuhan perangkat keras menjadi penting dalam konteks kebutuhan skalabilitas?',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Apa yang dimaksud dengan DML (Data Manipulation Language) dalam konteks aplikasi SQL, dan mengapa identifikasi fitur pengolahan DML penting?',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Apa yang dimaksud dengan "format basis data," dan mengapa penting untuk dapat menyimpan dan mengubah data ke dalam format tersebut?',
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'label' => 'Apa langkah-langkah utama dalam membuat alur logika pemrograman, dan mengapa penting untuk menetapkan alur mulai dan selesai?',
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'label' => 'Mengapa penambahan komentar pada setiap baris kode, termasuk informasi tentang data, eksepsi, fungsi, prosedur, dan class, menjadi praktik yang baik dalam pemrograman?',
                    ],
                    [
                        'name' => 'pertanyaan_6',
                        'label' => 'Mengapa proses kompilasi menjadi langkah awal dalam melakukan debugging?',
                    ],
                    [
                        'name' => 'pertanyaan_7',
                        'label' => 'Bagaimana Anda mengidentifikasi dampak negatif bottleneck terhadap performa program, dan mengapa penting untuk mengidentifikasi bottleneck tersebut?',
                    ],
                    [
                        'name' => 'pertanyaan_8',
                        'label' => 'Bagaimana Anda mengevaluasi kesesuaian kode dengan ketentuan yang telah dijelaskan dalam spesifikasi proyek?',
                    ],
                    [
                        'name' => 'pertanyaan_9',
                        'label' => 'Dalam pengembangan perangkat lunak, bagaimana Anda menentukan prosedur uji coba aplikasi sesuai dengan tahapan software development life cycle?',
                    ],
                    [
                        'name' => 'pertanyaan_10',
                        'label' => 'Dalam proses pengujian integrasi, jelaskan langkah-langkah yang perlu dilakukan dalam mempersiapkan dokumentasi peralatan dan lingkungan pengujian!',
                    ],
                ],
            ],
        ],
        'JADE' => [
            'FR IA 03' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 03 - Pertanyaan untuk mendukung observasi',
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'asesor.name',
                    'jadwal.tanggal_ujian',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Tindakan apa yang akan saudara lakukan jika basis data yang dibuat tidak sesuai dengan desain? (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Sebutkan dan jelaskan alternatif yang dapat diberikan untuk model integrasi data selain ETL ? (TRS)',
                    ],
                ],
            ],
            'FR IA 06' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 06 - Pertanyaan tulis essai',
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'jadwal.tanggal_ujian',
                    'jadwal.waktu_mulai',
                    'asesor.name',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Jika menggunakan notasi diagram-diagram UML untuk melakukan analisis dan desain kebutuhan dalam proyek data management, bagaimana fungsi diagram berikut ini : class diagram, use case diagram, activity diagram, communication diagram, deployment diagram?',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Mengapa diperlukan pengelolaan kualitas reference & master data dengan pengukuran berikut ini : data quality compliance, data change activity, data ingestion consumption, service level agreement, data steward coverage, total cost of ownership, data sharing volume usage (pilih 3).',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Berikan masing-masing 3 contoh untuk setiap kategori metadata berikut untuk data engineering : descriptive metadata, structural metadata, administrative metadata ?',
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'label' => 'Jika metode subquery ternyata menurunkan perform SQL, teknik apa yang dipilih antara window functions, stored procedure pada trigger, untuk meningkatkan performa pemrosesan data ?',
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'label' => 'Jika berperan sebagai data engineer, untuk mendapatkan informasi mengenai alamat mesin dan peningkatan kapasitas memory untuk digunakan pada data warehouse layer, respon apa yang didapatkan dari setiap jabatan berikut : production DBA, application DBA, procedural & development DBA ?',
                    ],
                    [
                        'name' => 'pertanyaan_6',
                        'label' => 'Jelaskan alasan untuk memilih menggunakan strategi batch data integration orchestration daripada real-time data integration orchestration ?',
                    ],
                    [
                        'name' => 'pertanyaan_7',
                        'label' => 'Bagaimana mengoptimalkan data-processing pipeline jika terdapat kelambatan performance pemrosesan data?',
                    ],
                    [
                        'name' => 'pertanyaan_8',
                        'label' => 'Apa alternatif yang dapat diberikan dalam menangani pengelolaan pengaksesan data untuk mencegah pengaksesan volume data secara masif dari aplikasi pengguna yang berpotensi terjadi pencurian data ?',
                    ],
                ],
            ],
        ],
        'JADS' => [
            'FR IA 03' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 03 - Pertanyaan untuk mendukung observasi',
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'asesor.name',
                    'jadwal.tanggal_ujian',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Apabila data yang diperoleh membutuhkan penyajian komposisi (composition) antar komponen, bagaimana Anda menyajikan visualisasi grafik yang sesuai? (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Jelaskan perbandingan antar elemen yang perlu dianalisis dalam penerapan perancangan boxplot untuk menghasilkan laporan deskripsi statistik pada proses telaah data. (TRS)',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Jelaskan mengenai tahapan pra-proses data berdasarkan studi kasus dataset baru yang Anda peroleh. (TRS)',
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'label' => 'Jelaskan perbandingan jumlah data training dan data testing pada algoritma yang digunakan untuk membangun model klasifikasi. (TRS)',
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'label' => 'Jelaskan hasil performa model yang diperoleh berdasarkan parameter evaluasi pengujian. (CMS)',
                    ],
                ],
            ],
            'FR IA 06' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 06 - Pertanyaan tulis essai',
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'jadwal.tanggal_ujian',
                    'jadwal.waktu_mulai',
                    'asesor.name',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Mengapa pengumpulan data penting dalam Knowledge Discovery in Data Science (KDDS) ?',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Sebutkan minimal 3 (tiga) teknik/metode pengumpulan data pada Data Science ?',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Berikan satu contoh data variabel denga tipe discrete variabel.',
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'label' => 'Setelah melalui proses pengumpulan data, bagaimana cara anda dalam menghitung mean suatu data?',
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'label' => 'Jelaskan mengenai data yang berkualitas.',
                    ],
                    [
                        'name' => 'pertanyaan_6',
                        'label' => 'Apa karakteristik data bertipe ordinal?',
                    ],
                    [
                        'name' => 'pertanyaan_7',
                        'label' => 'Apa perbedaan antara data structured, semi-structed dan unstructured?',
                    ],
                    [
                        'name' => 'pertanyaan_8',
                        'label' => 'Bagaimana cara menangani missing value dan duplikasi data?',
                    ],
                    [
                        'name' => 'pertanyaan_9',
                        'label' => 'Apa yg dimaksud dengan outlier dan bagaimana mendeteksinya?',
                    ],
                    [
                        'name' => 'pertanyaan_10',
                        'label' => 'Jelaskan mengenai teknik normalisasi min-max ?',
                    ],
                    [
                        'name' => 'pertanyaan_11',
                        'label' => 'Berikan contoh data terstruktur dan data tidak terstruktur.',
                    ],
                    [
                        'name' => 'pertanyaan_12',
                        'label' => 'Apa yang dimaksud dengan pelabelan data dalam tahapan klasifikasi data mining?',
                    ],
                    [
                        'name' => 'pertanyaan_13',
                        'label' => 'Jelaskan yang dimaksud dengan data imbalance.',
                    ],
                    [
                        'name' => 'pertanyaan_14',
                        'label' => 'Jelaskan mengenai teknik pemilihan data training dan data testing seperti percentage splitting, random selection, atau cross validation!',
                    ],
                    [
                        'name' => 'pertanyaan_15',
                        'label' => 'Sebutkan teknik menentukan k optimal pada metode clustering k-means?',
                    ],
                    [
                        'name' => 'pertanyaan_16',
                        'label' => 'Apa fungsi data training dalam algoritma klasifikasi?',
                    ],
                    [
                        'name' => 'pertanyaan_17',
                        'label' => 'Jelaskan mengenai akurasi sebagai salah satu parameter evaluasi!',
                    ],
                ],
            ],
        ],
        'CSA' => [
            'FR IA 03' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 03 - Pertanyaan untuk mendukung observasi',
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'asesor.name',
                    'jadwal.tanggal_ujian',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Ketika seorang profesional keamanan sistem dihadapkan pada keadaan di mana laporan berkala hasil pemantuan kinerja dan penerapan peraturan keamanan informasi harus diajukan dalam tenggat waktu yang ketat, apa yang seharusnya anda lakukan dengan tepat? (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Ketika seorang ahli keamanan sistem menghadapi situasi di mana sistem keamanan yang telah direncanakan mengalami kegagalan, bagaimana anda seharusnya mengatasi masalah ini dengan tepat? (TMS)',
                    ],
                ],
            ],
            'FR IA 06' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 06 - Pertanyaan tulis essai',
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'jadwal.tanggal_ujian',
                    'jadwal.waktu_mulai',
                    'asesor.name',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Apa saja standar yang berlaku terkait dengan keamanan informasi di Indonesia yang harus dipegang dan diterapkan oleh seorang Cyber Security Analyst/Cyber Security Incident Analyst?',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Sebutkan minimal 3 (tiga) butir pokok yang biasanya terdapat pada dokumentasi terkait ketentuan hukum yang berlaku tentang keamanan informasi?',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Apa yang Anda ketahui tentang Konsep Dasar Keamanan Informasi yang harus dikuasai oleh seorang Cyber Security Analyst/Cyber Security Incident Analyst? Minimal 2 (dua).',
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'label' => 'Apa saja regulasi atau peraturan yang berlaku terkait dengan keamanan informasi yang perlu diketahui dan diikuti oleh seorang Cyber Security Analyst/Cyber Security Incident Analyst? Minimal 3 (tiga).',
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'label' => 'Bagaimana Anda menerapkan hasil audit atau rekomendasi terkait dengan Konsep Dasar Keamanan Informasi seperti pengelolaan risiko, ketersediaan, integritas dan kerahasiaan, orang, proses dan teknologi, serta keamanan fisik dalam lingkungan kerja Anda?',
                    ],
                    [
                        'name' => 'pertanyaan_6',
                        'label' => 'Bagaimana Anda mengelola dan memastikan ketersediaan dokumen yang terkait dengan regulasi atau peraturan terkait keamanan informasi? Minimal 3 (tiga).',
                    ],
                    [
                        'name' => 'pertanyaan_7',
                        'label' => 'Bagaimana Anda biasanya melakukan pembuatan log untuk mencatat insiden dan resolusinya dalam konteks kontrol akses, patch management, anti-malware, anti-spam, firewall, dan IPS?',
                    ],
                    [
                        'name' => 'pertanyaan_8',
                        'label' => 'Bagaimana Anda biasanya memberikan rekomendasi hasil evaluasi indikasi pelanggaran hukum terkait dengan praktik backup dan enkripsi?',
                    ],
                    [
                        'name' => 'pertanyaan_9',
                        'label' => 'Bagaimana Anda biasanya menyusun kebijakan dan prosedur yang sesuai dengan aspek legal dan peraturan yang berlaku untuk lingkungan jaringan sistem informasi organisasi? Minimal 3 (tiga)',
                    ],
                    [
                        'name' => 'pertanyaan_10',
                        'label' => 'Bagaimana Anda biasanya menerapkan kebijakan dan prosedur yang sesuai dengan aspek legal dan peraturan yang berlaku untuk lingkungan jaringan sistem informasi organisasi dan memastikan persetujuan oleh pimpinan? Minimal 3 (tiga).',
                    ],
                    [
                        'name' => 'pertanyaan_11',
                        'label' => 'Bagaimana Anda biasanya menerapkan hasil audit atau rekomendasi terkait dengan pelaksanaan kebijakan dan prosedur dalam lingkungan jaringan sistem informasi organisasi? Minimal 3 (tiga)',
                    ],
                    [
                        'name' => 'pertanyaan_12',
                        'label' => 'Bagaimana Anda biasanya menerapkan hasil audit atau rekomendasi terkait dengan pelaksanaan kebijakan dan prosedur? Minimal 3 (tiga).',
                    ],
                    [
                        'name' => 'pertanyaan_13',
                        'label' => 'Ketika seorang profesional keamanan sistem melakukan analisis kinerja sistem keamanan dan menemukan beberapa kerentanan kritis yang dapat dieksploitasi oleh penyerang, langkah apa yang seharusnya mereka ambil dengan tepat?',
                    ],
                    [
                        'name' => 'pertanyaan_14',
                        'label' => 'Seorang ahli keamanan jaringan telah menyusun daftar potensi ancaman yang mungkin terjadi dalam sistem. Namun, manfaat apa yang dapat diperoleh organisasi selain dari peningkatan keamanan jaringan langsung?',
                    ],
                    [
                        'name' => 'pertanyaan_15',
                        'label' => 'Seorang profesional keamanan IT telah menyelesaikan penilaian kontrol keamanan di dalam lingkungan jaringan perusahaan. Namun, ketika mengamati hasilnya, dia menemukan beberapa celah keamanan yang signifikan. Langkah apa yang seharusnya dia ambil sebagai tanggapan pertama?',
                    ],
                    [
                        'name' => 'pertanyaan_16',
                        'label' => 'Sebagai seorang administrator sistem, Anda telah merancang rencana pemantauan kinerja sistem untuk menghadapi situasi darurat atau gangguan yang tidak terduga. Mengapa memiliki rencana pemantauan kinerja sistem ini penting dalam manajemen kontingensi?',
                    ],
                    [
                        'name' => 'pertanyaan_17',
                        'label' => 'Sebagai seorang administrator sistem, Anda telah merancang rencana pemantauan kinerja sistem untuk menghadapi situasi darurat atau gangguan yang tidak terduga. Mengapa memiliki rencana pemantauan kinerja sistem ini penting dalam manajemen kontingensi?',
                    ],
                    [
                        'name' => 'pertanyaan_18',
                        'label' => 'Sebagai bagian dari tim keamanan TI, Anda mengetahui bahwa memiliki rencana pemantauan / peninjauan manajemen keamanan yang efektif sangat penting. Namun, Anda mendapatkan umpan balik bahwa beberapa anggota tim meragukan nilai dari langkah ini. Bagaimana Anda akan menjelaskan manfaat utama dari rencana pemantauan / peninjauan ini untuk membantu meredakan keraguan mereka?',
                    ],
                    [
                        'name' => 'pertanyaan_19',
                        'label' => 'Sebagai administrator keamanan, Anda telah menyusun laporan pemantauan/tinjauan manajemen keamanan yang mendokumentasikan temuan dan hasil dari proses pemantauan keamanan. Namun, salah seorang atasan Anda meragukan nilai dari melaporkan hasil pemantauan secara berkala. Bagaimana Anda akan menjelaskan manfaat utama dari laporan ini untuk membantu meredakan keraguan mereka?',
                    ],
                ],
            ],
        ],
        'DMM' => [
            'FR IA 03' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 03 - Pertanyaan untuk mendukung observasi',
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'asesor.name',
                    'jadwal.tanggal_ujian',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Tindakan apa yang akan saudara lakukan jika saudara diberikan tugas untuk memuat project dengan output sebuah aplikasi visual audio? (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Jelaskan langkah-langkah apa yang saudara lakukan untuk menghasilkan sebuah produk aplikasi video audio ? (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Jelaskan apa yang harus saudara lakukan jika terdapat suatu langkah yang tidak sesuai dengan prosedur yang telah ditetapkan (TRS)',
                    ],
                ],
            ],
            'FR IA 06' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 06 - Pertanyaan tulis essai',
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'jadwal.tanggal_ujian',
                    'jadwal.waktu_mulai',
                    'asesor.name',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Sebutkan tahapan tahapan Riset Kreatif Multimedia?',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Bagaimana saudara menentukan kebutuhan kebutuhan Aset Teknik yang dibutuhkan?',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Sebutkan dan jelaskan Arah Kebutuhan Teknik sesuai dengan kebutuhan produk multimedia?',
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'label' => 'Bagaimana saudara membuat arahan visual untuk produk multimedia?',
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'label' => 'Bagaimana anda membuat langkah-langkah aset audio visual esuai tahapan tahapan yang telah ditentukan?',
                    ],
                    [
                        'name' => 'pertanyaan_6',
                        'label' => 'Bagaimana anda mengevaluasi output visual?',
                    ],
                    [
                        'name' => 'pertanyaan_7',
                        'label' => 'Bagaimana anda Mengevaluasi Hasil Pembuatan Aset Audio?',
                    ],
                    [
                        'name' => 'pertanyaan_8',
                        'label' => 'Apa yang menjadi langkah pertama dalam pembuatan pemrograman interaktif berdasarkan langkah kerja yang telah ditetapkan?',
                    ],
                    [
                        'name' => 'pertanyaan_9',
                        'label' => 'Tindakan apa yang anda lakukan untuk memvalidasi hasil evaluasi sesuai dengan Brief?',
                    ],
                ],
            ],
            'FR IA 07' => [
                'target' => 'asesor',
                'keterangan' => 'FR IA 07 - Daftar Pertanyaan Lisan',
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'asesor.name',
                    'jadwal.tanggal_ujian',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Pertanyaan: Sebutkan beberapa metode pengumpulan data yang dapat digunakan dalam Riset Kreatif Multimedia. (KUK 1.1 -JRES)',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Pertanyaan: Bagaimana menentukan TIM secara tepat sesuai dengan creative brief(KUK 3.2 -JRES)',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Pertanyaan: Bagaimana seharusnya seorang profesional multimedia menanggapi umpan balik negatif terhadap hasil produksi aset visual? (CMS 3.1 -CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'label' => 'Pertanyaan: Sebutkan beberapa strategi yang dapat digunakan untuk mengembangkan nuansa audio yang efektif dalam suatu proyek multimedia. (KUK 2.1 -CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'label' => 'Pertanyaan: Bagaimana proses produksi aset audio biasanya dilakukan setelah tahap pengembangan materi dan konten audio? (KUK 1.2 -JRES)',
                    ],
                    [
                        'name' => 'pertanyaan_6',
                        'label' => 'Pertanyaan: Apa saja faktor-faktor yang perlu dipertimbangkan dalam pemilihan teknologi dalam konteks arah kebutuhan teknik? (KUK 2.2 -JRES)',
                    ],
                    [
                        'name' => 'pertanyaan_7',
                        'label' => 'Pertanyaan: Bagaimana analisis risiko berperan dalam menentukan kebutuhan asset teknik? (KUK 2.1 -JRES)',
                    ],
                    [
                        'name' => 'pertanyaan_8',
                        'label' => 'Pertanyaan: Apa yang menjadi langkah pertama dalam pembuatan pemrograman interaktif berdasarkan langkah kerja yang telah ditetapkan? (KUK 2.1-JRES)',
                    ],
                    [
                        'name' => 'pertanyaan_9',
                        'label' => 'Pertanyaan: Apa yang harus dilakukan jika hasil evaluasi tidak sesuai dengan kriteria yang telah ditetapkan dalam brief? (KUK 2.1 -JRES)',
                    ],
                ],
            ],
        ],
        'JWP' => [
            'FR IA 03' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 03 - Pertanyaan untuk mendukung observasi',
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'asesor.name',
                    'jadwal.tanggal_ujian',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Apakah yang Anda lakukan apabila pada saat menginstall tools pemrograman XAMPP, salah satu service tidak dapat aktifkan saat menjalankan XAMPP Control Panel? (JRES)',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Jelaskan langkah yang saudara lakukan untuk mengeksekusi script sederhana menggunakan tools pemrograman berbasis web (TS)',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Tentukan langkah – langkah dalam melakukan debugging, dokumentasi sampai diimplementasikan aplikasi berbasis web? (TS)',
                    ],
                ],
            ],
            'FR IA 06' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 06 - Pertanyaan tulis essai',
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'jadwal.tanggal_ujian',
                    'jadwal.waktu_mulai',
                    'asesor.name',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Apabila saudara ingin menginstall tools pemrograman XAMPP namun service apache tidak dapat dilakukan saat penginstalan, apa yang saudara lakukan? (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Jika Saudara ingin membuat table barang dengan spesifikasi kode barang, nama barang, satuan, dan stok, namun terjadi kesalahan. Apa yang saudara lakukan sehingga tabel barang sesuai yang diinginkan ? (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Jika saudara membutuhkan function dari suatu library namun library tersebut tidak valid lagi untuk kondisi saat ini, apa yang saudara lakukan supaya pekerjaan dapat tetap diselesaikan? (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'label' => 'Sebutkan pertimbangan anda dalam membuat prosedur dan function? (TS)',
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'label' => 'Saat saudara mendefenisikan struktur kontrol, kondisi yang saudara buat tidak sesuai berjalan dengan baik. Apa yang saudara lakukan? (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_6',
                        'label' => 'Sebutkan langkah kerja untuk melakukan perhitungan rata rata dari 3 nilai ? (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_7',
                        'label' => 'Kapankah Saudara membutuhkan debugging tools pada saat saudara melakukan pembuatan program ? (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_8',
                        'label' => 'Mengapa dokumentasi perangkat lunak khususnya pemrograman web memiliki peranan penting ? (CMS)',
                    ],
                ],
            ],
        ],
        'PB' => [
            'FR IA 03' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 03 - Pertanyaan untuk mendukung observasi',
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'asesor.name',
                    'jadwal.tanggal_ujian',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Tindakan apa yang akan saudara lakukan jika aplikasi XAMPP tidak bisa berjalan dengan baik setelah melakukan instalasi? (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Jelaskan apa yang saudara lakukan dalam merancang database ketika ada hubungan antar entitas yang memiliki tingkat hubungan many to many? (CMS)',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Jelaskan apa yang harus saudara lakukan jika terdapat suatu rangkaian query yang dalam prosesnya dapat terjadi kegagalan, dan jika terjadi kegagalan saudara ingin semua proses yang sebelumnya terjadi/berubah kembali ke kondisi awal lagi sebelum rangkaian query tersebut dijakankan? (TRS)',
                    ],
                ],
            ],
            'FR IA 06' => [
                'target' => 'asesi',
                'keterangan' => 'FR IA 06 - Pertanyaan tulis essai',
                'question_format' => 'textarea', // Format checkbox Ya/Tdk terpisah
                'variables' => [
                    'user.name',
                    'jadwal.tanggal_ujian',
                    'jadwal.waktu_mulai',
                    'asesor.name',
                ],
                'questions' => [
                    [
                        'name' => 'pertanyaan_1',
                        'label' => 'Sebutkan tahapan tahapan instalasis aplikasi perangkat lunak XAMPP?',
                    ],
                    [
                        'name' => 'pertanyaan_2',
                        'label' => 'Bagaimana saudara menjalankan MySQL di XAMPP?',
                    ],
                    [
                        'name' => 'pertanyaan_3',
                        'label' => 'Sebutkan dan jelaskan fungsi – fungsi DML?',
                    ],
                    [
                        'name' => 'pertanyaan_4',
                        'label' => 'Bagaimana saudara membuat perintah DML untuk mengisi tabel dengan kondisi hanya sebagian kolom yang diisi data?',
                    ],
                    [
                        'name' => 'pertanyaan_5',
                        'label' => 'Bagaimana anda mengecek index yang telah dibangkitkan pada tabel?',
                    ],
                    [
                        'name' => 'pertanyaan_6',
                        'label' => 'Bagaimana anda menampilkan view tabel yang sudah terbentuk di basis data?',
                    ],
                    [
                        'name' => 'pertanyaan_7',
                        'label' => 'Sebutkan dan jelaskan jenis dan kegunaan fitur operasi relasional dalam pengolahan DML?',
                    ],
                    [
                        'name' => 'pertanyaan_8',
                        'label' => 'Tindakan apa yang anda lakukan jika diminta untuk menggabungkan dua buah tabel yang mana data yang dihasilkan nantinya adalah irisan datanya dan hasilnya disimpan didalam tabel lain?',
                    ],
                    [
                        'name' => 'pertanyaan_9',
                        'label' => 'Tindakan apa yang anda lakukan jika ingin menggunakan nilai dari luar prosedur untuk diproses oleh stored procedure dan memberikan nilai kembali dari prosedur, selain itu bagaimana memastikan bahwa input dan outputnya sudah sesuai?',
                    ],
                    [
                        'name' => 'pertanyaan_10',
                        'label' => 'Jelaskan kapan kondisi yang tepan untuk menggunakan function dan bagaimana memanggil sebuah function dengan satu parameter?',
                    ],
                    [
                        'name' => 'pertanyaan_11',
                        'label' => 'Sebutkan dan jelaskan jenis – jenis trigger dan waktu aktif setiap trigger?',
                    ],
                    [
                        'name' => 'pertanyaan_12',
                        'label' => 'Saudara sebagai seorang pemrogram basis data pada sebuah perusahaan yang menerima permintaan dari tim bisnis untuk membuat sebuah program basis data yang prosesnya saling berkaitan sehingga jika terdiri dari 5 proses transaksi dan ada salah satunya gagal/error maka semua proses dibatalkan dan data kembali ke kondisi awal sebelum 5 proses tadi dijalankan. Data perubahan hanya tersimpan jika semua proses berhasil di eksekusi dan satu kondisi dia dapat kembali ke proses tertentu. Bagaimana anda mendesain agar hal tersebut dapat dilakukan?',
                    ],
                    [
                        'name' => 'pertanyaan_13',
                        'label' => 'Jelaskan perbedaan antara varian dan invarian dalam pemrograman basis data. Berikan contoh konsep varian dan invarian beserta dampaknya terhadap manajemen data.',
                    ],
                    [
                        'name' => 'pertanyaan_14',
                        'label' => 'Buat alur logika pemrograman untuk mengurutkan data dalam tabel basis data.',
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
        $ttdInfo = $target === 'asesi' ? '2 TTD (Asesi & Asesor)' : '1 TTD (Asesor)';
        $this->command->info("✓ Bank Soal {$skema->nama} {$tipe} berhasil dibuat/diperbarui dengan {$questionCount} pertanyaan ({$ttdInfo}!");
    }
}
