<?php

namespace App\Services;

use App\Models\TemplateMaster;
use App\Models\Pendaftaran;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TemplateGeneratorService
{
    /**
     * Generate APL 2 document dari template
     */
    public function generateApl2(Pendaftaran $pendaftaran, $asesorView = false)
    {
        try {
            // Cari template APL 2 untuk skema yang sesuai
            $template = TemplateMaster::active()
                ->byType('APL2')
                ->where('skema_id', $pendaftaran->skema_id)
                ->first();

            if (!$template) {
                throw new \Exception("Template APL 2 untuk skema {$pendaftaran->skema->nama} tidak ditemukan.");
            }

            // Path ke file template
            $templatePath = storage_path('app/public/' . $template->file_path);

            if (!file_exists($templatePath)) {
                throw new \Exception("File template tidak ditemukan: {$templatePath}");
            }

            // Load template processor
            $templateProcessor = new TemplateProcessor($templatePath);

            // PENTING: Insert TTD digital DULU sebelum replace text
            // Karena setValue bisa menghapus placeholder image
            $this->insertApl2Signatures($templateProcessor, $pendaftaran, $asesorView);

            // Siapkan data untuk mengganti variable
            $data = $this->prepareApl2TemplateData($pendaftaran, $asesorView);

            // Replace variables dalam template
            foreach ($data as $key => $value) {
                // Skip variables yang sudah di-handle sebagai image
                $imageVariables = ['ttd_asesi', 'ttd_digital_asesi', 'ttd_asesor', 'ttd_digital_asesor'];
                if (in_array($key, $imageVariables)) {
                    continue; // Skip, sudah di-insert sebagai gambar
                }

                // Ensure value is string
                $stringValue = is_array($value) ? json_encode($value) : (string)$value;

                // PhpWord TemplateProcessor menggunakan nama variable tanpa ${}
                // karena sudah ditambahkan secara internal
                try {
                    $templateProcessor->setValue($key, $stringValue);
                } catch (\Exception $e) {
                    // Log jika gagal, tapi lanjutkan
                    \Illuminate\Support\Facades\Log::debug("Failed to set variable {$key}: " . $e->getMessage());
                }

                \Illuminate\Support\Facades\Log::info("Set template variable: {$key} = " . substr($stringValue, 0, 100));
            }

            // Generate nama file output
            $asesiName = $pendaftaran->user ? ($pendaftaran->user->name ?? 'Unknown') : 'Unknown';
            $skemaName = $pendaftaran->skema ? ($pendaftaran->skema->nama ?? 'Unknown') : 'Unknown';
            $viewType = $asesorView ? 'Asesor' : 'Asesi';

            $outputFileName = 'APL2_' . $viewType . '_' . Str::slug($asesiName) . '_' .
                             Str::slug($skemaName) . '_' .
                             now()->format('Ymd_His') . '.docx';

            // Path untuk menyimpan file hasil
            $outputPath = 'generated/apl2/' . $outputFileName;
            $fullOutputPath = storage_path('app/public/' . $outputPath);

            // Pastikan folder exists
            $outputDir = dirname($fullOutputPath);
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // Simpan file hasil
            $templateProcessor->saveAs($fullOutputPath);

            return [
                'success' => true,
                'file_path' => $fullOutputPath,
                'filename' => $outputFileName,
                'download_url' => asset('storage/' . $outputPath)
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate APL 1 document dari template
     */
    public function generateApl1(Pendaftaran $pendaftaran, array $customData = [])
    {
        try {
            // Cari template APL 1 untuk skema yang sesuai
            $template = TemplateMaster::active()
                ->byType('APL1')
                ->where('skema_id', $pendaftaran->skema_id)
                ->first();

            if (!$template) {
                throw new \Exception("Template APL 1 untuk skema {$pendaftaran->skema->nama} tidak ditemukan.");
            }

            // Path ke file template
            $templatePath = storage_path('app/public/' . $template->file_path);

            if (!file_exists($templatePath)) {
                throw new \Exception("File template tidak ditemukan: {$templatePath}");
            }

            // Load template processor
            $templateProcessor = new TemplateProcessor($templatePath);

            // Siapkan data untuk mengganti variable
            $data = $this->prepareTemplateData($pendaftaran, $customData);

            // Insert TTD digital DULU sebelum replace text variables
            $ttdPath = null;
            if ($pendaftaran->ttd_asesi_path && file_exists(storage_path('app/public/' . $pendaftaran->ttd_asesi_path))) {
                $ttdPath = storage_path('app/public/' . $pendaftaran->ttd_asesi_path);
                \Illuminate\Support\Facades\Log::info('Using asesi TTD: ' . $pendaftaran->ttd_asesi_path);
            } elseif ($template->ttd_path && file_exists(storage_path('app/public/' . $template->ttd_path))) {
                $ttdPath = storage_path('app/public/' . $template->ttd_path);
                \Illuminate\Support\Facades\Log::info('Using template TTD: ' . $template->ttd_path);
            }

            if ($ttdPath) {
                try {
                    // Collect all signature_pad field names dari template
                    $signaturePadFields = ['ttd_digital']; // Default

                    if ($template->field_configurations) {
                        foreach ($template->field_configurations as $fieldConfig) {
                            if (isset($fieldConfig['type']) && $fieldConfig['type'] === 'signature_pad') {
                                $signaturePadFields[] = $fieldConfig['name'];
                            }
                        }
                    }

                    // Juga check custom_variables
                    if (isset($template->custom_variables) && is_array($template->custom_variables)) {
                        foreach ($template->custom_variables as $customVar) {
                            if (isset($customVar['type']) && $customVar['type'] === 'signature_pad' && isset($customVar['name'])) {
                                $signaturePadFields[] = $customVar['name'];
                            }
                        }
                    }

                    // Remove duplicates
                    $signaturePadFields = array_unique($signaturePadFields);

                    // Try to insert TTD for each signature pad field
                    foreach ($signaturePadFields as $fieldName) {
                        // Coba berbagai format placeholder untuk setiap field
                        $placeholders = [$fieldName, '${' . $fieldName . '}', $fieldName . '}', '{' . $fieldName];

                        foreach ($placeholders as $placeholder) {
                            try {
                                $templateProcessor->setImageValue(
                                    $placeholder,
                                    [
                                        'path' => $ttdPath,
                                        'width' => 150,
                                        'height' => 75,
                                        'ratio' => true
                                    ]
                                );
                                \Illuminate\Support\Facades\Log::info("TTD digital inserted successfully with placeholder: {$placeholder}");
                                break; // Success, move to next field
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::debug("Failed with placeholder {$placeholder}: " . $e->getMessage());
                                continue;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Error inserting TTD: ' . $e->getMessage());
                }
            } else {
                \Illuminate\Support\Facades\Log::warning('TTD path not found for pendaftaran: ' . $pendaftaran->id);
            }

            // Collect signature_pad field names untuk skip saat replace text
            $skipFields = ['ttd_digital'];
            if (isset($signaturePadFields)) {
                $skipFields = array_merge($skipFields, $signaturePadFields);
            }
            $skipFields = array_unique($skipFields);

            // Replace variables dalam template
            foreach ($data as $key => $value) {
                // Skip signature_pad fields karena sudah di-handle sebagai image
                if (in_array($key, $skipFields)) {
                    continue;
                }

                // Convert key to template format dengan dollar sign (e.g., 'nama_asesi' -> '${nama_asesi}')
                $templateKey = '${' . $key . '}';
                $templateProcessor->setValue($templateKey, $value);
                \Illuminate\Support\Facades\Log::info("Setting template variable: {$templateKey} = {$value}");
            }

            // Log untuk debug
            \Illuminate\Support\Facades\Log::info('Template variables being set: ' . json_encode($data));

            // Generate nama file output
            $asesiName = $pendaftaran->user ? ($pendaftaran->user->name ?? 'Unknown') : 'Unknown';
            $skemaName = $pendaftaran->skema ? ($pendaftaran->skema->nama ?? 'Unknown') : 'Unknown';

            $outputFileName = 'APL1_' . Str::slug($asesiName) . '_' .
                             Str::slug($skemaName) . '_' .
                             now()->format('Ymd_His') . '.docx';

            // Path untuk menyimpan file hasil
            $outputPath = 'generated/apl1/' . $outputFileName;
            $fullOutputPath = storage_path('app/public/' . $outputPath);

            // Pastikan folder exists
            $outputDir = dirname($fullOutputPath);
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // Simpan file hasil
            $templateProcessor->saveAs($fullOutputPath);

            return [
                'success' => true,
                'file_path' => $outputPath,
                'file_name' => $outputFileName,
                'download_url' => asset('storage/' . $outputPath)
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Prepare data untuk template APL2
     */
    public function prepareApl2TemplateData(Pendaftaran $pendaftaran, $asesorView = false)
    {
        $asesi = $pendaftaran->user;
        $skema = $pendaftaran->skema;
        $jadwal = $pendaftaran->jadwal;

        // Data dasar
        $data = [
            'nama_asesi' => $asesi ? ($asesi->name ?? '') : '',
            'email_asesi' => $asesi ? ($asesi->email ?? '') : '',
            'telephone_asesi' => $asesi ? ($asesi->telephone ?? '') : '',
            'alamat_asesi' => $asesi ? ($asesi->alamat ?? '') : '',
            'nik_asesi' => $asesi ? ($asesi->nik ?? '') : '',
            'nama_skema' => $skema ? ($skema->nama ?? '') : '',
            'kode_skema' => $skema ? ($skema->kode ?? '') : '',
            'kategori_skema' => $skema ? ($skema->kategori ?? '') : '',
            'bidang_skema' => $skema ? ($skema->bidang ?? '') : '',
            'tanggal_ujian' => $jadwal ? ($jadwal->tanggal_ujian ?? '') : '',
            'waktu_mulai' => $jadwal ? ($jadwal->waktu_mulai ?? '') : '',
            'waktu_selesai' => $jadwal ? ($jadwal->waktu_selesai ?? '') : '',
            'lokasi_ujian' => ($jadwal && $jadwal->tuk) ? ($jadwal->tuk->nama ?? '') : '',
            'tanggal_generate' => now()->format('d/m/Y'),
            'waktu_generate' => now()->format('H:i:s'),
            'nomor_pendaftaran' => $pendaftaran->id ?? '',
        ];

        // Tambahkan mapping untuk variable yang umum digunakan di template
        $data['nama_lengkap'] = $data['nama_asesi'];
        $data['email'] = $data['email_asesi'];
        $data['no_hp'] = $data['telephone_asesi'];
        $data['alamat'] = $data['alamat_asesi'];
        $data['nik'] = $data['nik_asesi'];

        // Mapping untuk format user.xxx
        $data['user.name'] = $data['nama_asesi'];
        $data['user_name'] = $data['nama_asesi'];
        $data['user.email'] = $data['email_asesi'];
        $data['user_email'] = $data['email_asesi'];
        $data['user.telephone'] = $data['telephone_asesi'];
        $data['user_telephone'] = $data['telephone_asesi'];
        $data['user.alamat'] = $data['alamat_asesi'];
        $data['user_alamat'] = $data['alamat_asesi'];
        $data['user.nik'] = $data['nik_asesi'];
        $data['user_nik'] = $data['nik_asesi'];

        // Mapping untuk skema
        $data['skema.nama'] = $data['nama_skema'];
        $data['skema_nama'] = $data['nama_skema'];
        $data['skema.kode'] = $data['kode_skema'];
        $data['skema_kode'] = $data['kode_skema'];

        // Ambil template untuk mendapatkan custom variables
        $template = TemplateMaster::active()
            ->byType('APL2')
            ->where('skema_id', $pendaftaran->skema_id)
            ->first();

        if (!$template) {
            throw new \Exception("Template APL2 untuk skema {$skema->nama} tidak ditemukan.");
        }

        // Ensure custom_variables is an array (fallback if cast doesn't work)
        if (is_string($template->custom_variables)) {
            $template->custom_variables = json_decode($template->custom_variables, true) ?? [];
        }

        // Tambahkan data asesi untuk SEMUA view
        // Nama asesi harus selalu muncul di bagian asesi
        $asesiName = $asesi ? ($asesi->name ?? '') : '';
        $data['nama_asesi_display'] = $asesiName;

        // Tambahkan data asesor jika asesorView = true
        if ($asesorView && $pendaftaran->asesor_data) {
            foreach ($pendaftaran->asesor_data as $key => $value) {
                $data[$key] = $value;
            }
        }

        // Tambahkan data nama asesor dan tanda tangan
        if ($asesorView) {
            // Ambil data asesor dari jadwal
            $asesor = null;

            if ($pendaftaran->jadwal) {
                // Load relasi asesorSkema jika belum loaded
                if (!$pendaftaran->jadwal->relationLoaded('asesorSkema')) {
                    $pendaftaran->jadwal->load('asesorSkema.asesor');
                }

                $asesor = $pendaftaran->jadwal->asesorSkema->first();
            }

            if ($asesor && $asesor->asesor) {
                $asesorName = $asesor->asesor->name ?? '';
                $asesorNik = $asesor->asesor->nik ?? '';
                $data['nama_asesor'] = $asesorName;
                $data['asesor_name'] = $asesorName;
                $data['asesor.name'] = $asesorName;
                $data['no_reg_asesor'] = $asesorNik; // Gunakan NIK sebagai No. Reg
                $data['asesor_no_reg'] = $asesorNik;
                $data['asesor_nik'] = $asesorNik;
                $data['nik_asesor'] = $asesorNik;
                $data['ttd_digital_asesor'] = $asesorName;
                $data['ttd_asesor'] = '[Tanda Tangan Digital Asesor]';
                \Illuminate\Support\Facades\Log::info('Asesor data added: ' . $asesorName . ' (NIK: ' . $asesorNik . ')');
            } else {
                $data['nama_asesor'] = '';
                $data['asesor_name'] = '';
                $data['asesor.name'] = '';
                $data['no_reg_asesor'] = '';
                $data['asesor_no_reg'] = '';
                $data['asesor_nik'] = '';
                $data['nik_asesor'] = '';
                $data['ttd_digital_asesor'] = '';
                $data['ttd_asesor'] = '';
                \Illuminate\Support\Facades\Log::warning('Asesor data not found for jadwal: ' . ($pendaftaran->jadwal_id ?? 'null'));
            }
        } else {
            // Untuk view asesi, set empty values untuk data asesor
            $data['nama_asesor'] = '';
            $data['asesor_name'] = '';
            $data['asesor.name'] = '';
            $data['no_reg_asesor'] = '';
            $data['asesor_no_reg'] = '';
            $data['ttd_digital_asesor'] = '';
            $data['ttd_asesor'] = '';
        }

        // Generate konten dari custom variables
        $questionsContent = '';
        $answersContent = '';
        $bkKContent = '';
        $kCheckboxContent = '';
        $bkCheckboxContent = '';
        $radioKCheckboxContent = '';
        $radioBkCheckboxContent = '';
        $asesorAssessmentContent = '';

        // Hitung jumlah pertanyaan radio (BK/K)
        $radioQuestions = [];
        $otherQuestions = [];

        if ($template->custom_variables && count($template->custom_variables) > 0) {
            foreach ($template->custom_variables as $index => $variable) {
                // NOTE: For document generation, we include ALL fields regardless of role
                // The role distinction is only for the web form (which fields to show for editing)
                // In the generated document, asesor should see all data including asesi's K/BK answers

                if ($variable['type'] === 'radio' && isset($variable['options'])) {
                    $radioQuestions[] = $variable;
                } else {
                    $otherQuestions[] = $variable;
                }
            }
        }

        // Generate konten untuk pertanyaan radio (BK/K) - format tabel
        if (count($radioQuestions) > 0) {
            foreach ($radioQuestions as $index => $variable) {
                $questionNumber = $index + 1;
                $variableName = $variable['name'];
                $variableLabel = $variable['label'];

                // Ambil jawaban dari custom variables pendaftaran
                $answer = $pendaftaran->custom_variables[$variableName] ?? '';

                // Format sesuai struktur tabel yang diinginkan
                $questionsContent .= "{$questionNumber}. {$variableLabel}\n";

                // Set jawaban untuk variable itu sendiri
                $data[$variableName] = $answer; // pertanyaan_1 = "K"

                // Generate checkbox individual per pertanyaan untuk flexible placement
                // Format: ${nama_variable_k} dan ${nama_variable_bk}
                if ($answer === 'K') {
                    $data[$variableName . '_k'] = '√'; // Centang K untuk pertanyaan ini
                    $data[$variableName . '_bk'] = ''; // Kosong BK untuk pertanyaan ini
                    $kCheckboxContent .= "√\n"; // Legacy support - kolom K
                    $bkCheckboxContent .= "\n"; // Legacy support - kolom BK
                } elseif ($answer === 'BK') {
                    $data[$variableName . '_k'] = ''; // Kosong K untuk pertanyaan ini
                    $data[$variableName . '_bk'] = '√'; // Centang BK untuk pertanyaan ini
                    $kCheckboxContent .= "\n"; // Legacy support - kolom K
                    $bkCheckboxContent .= "√\n"; // Legacy support - kolom BK
                } else {
                    $data[$variableName . '_k'] = ''; // Belum dijawab
                    $data[$variableName . '_bk'] = ''; // Belum dijawab
                    $kCheckboxContent .= "\n";
                    $bkCheckboxContent .= "\n";
                }

                // Konten jawaban asesi
                if ($answer) {
                    $answersContent .= "{$questionNumber}. Penilaian Asesi: {$answer}\n\n";
                }

                // Konten penilaian asesor (jika ada)
                if ($asesorView && isset($pendaftaran->asesor_assessment[$variableName])) {
                    $asesorData = $pendaftaran->asesor_assessment[$variableName];
                    $asesorAssessmentContent .= "{$questionNumber}. Penilaian Asesor: {$asesorData['assessment']}\n";
                    if (!empty($asesorData['notes'])) {
                        $asesorAssessmentContent .= "   Catatan: {$asesorData['notes']}\n";
                    }
                    $asesorAssessmentContent .= "   Asesor: {$asesorData['asesor_name']}\n";
                    $asesorAssessmentContent .= "   Tanggal: " . date('d/m/Y H:i', strtotime($asesorData['assessed_at'])) . "\n\n";
                }
            }
        }

        // Generate konten untuk pertanyaan lain (text, textarea, dll)
        if (count($otherQuestions) > 0) {
            foreach ($otherQuestions as $index => $variable) {
                $questionNumber = count($radioQuestions) + $index + 1;
                $variableName = $variable['name'];
                $variableLabel = $variable['label'];
                $variableType = $variable['type'];

                // Ambil jawaban dari custom variables pendaftaran
                $answer = $pendaftaran->custom_variables[$variableName] ?? '';

                // Set jawaban untuk variable itu sendiri (jika bukan signature_pad)
                if ($variableType !== 'signature_pad') {
                    $data[$variableName] = $answer;
                }

                // Format pertanyaan
                $questionsContent .= "{$questionNumber}. {$variableLabel}\n";
                if ($variableType === 'file') {
                    $questionsContent .= "   Bukti yang diperlukan: Upload file bukti\n";
                }
                $questionsContent .= "\n";

                // Konten jawaban asesi
                if ($answer) {
                    $answersContent .= "{$questionNumber}. Jawaban: {$answer}\n\n";
                }

                // Konten penilaian asesor (jika ada)
                if ($asesorView && isset($pendaftaran->asesor_assessment[$variableName])) {
                    $asesorData = $pendaftaran->asesor_assessment[$variableName];
                    $asesorAssessmentContent .= "{$questionNumber}. Penilaian Asesor: {$asesorData['assessment']}\n";
                    if (!empty($asesorData['notes'])) {
                        $asesorAssessmentContent .= "   Catatan: {$asesorData['notes']}\n";
                    }
                    $asesorAssessmentContent .= "   Asesor: {$asesorData['asesor_name']}\n";
                    $asesorAssessmentContent .= "   Tanggal: " . date('d/m/Y H:i', strtotime($asesorData['assessed_at'])) . "\n\n";
                }
            }
        }

        $data['soal_apl2'] = $questionsContent;
        $data['bukti_apl2'] = ''; // File bukti akan ditangani terpisah
        $data['bk_k_checkbox'] = $bkKContent;
        $data['k_checkbox'] = $kCheckboxContent; // Centang untuk kolom K
        $data['bk_checkbox'] = $bkCheckboxContent; // Centang untuk kolom BK
        $data['radio_k_checkbox'] = $radioKCheckboxContent; // Centang khusus untuk radio K
        $data['radio_bk_checkbox'] = $radioBkCheckboxContent; // Centang khusus untuk radio BK

        // Tambahkan semua custom variables individual ke data untuk mapping langsung
        if ($template->custom_variables && count($template->custom_variables) > 0) {
            foreach ($template->custom_variables as $variable) {
                $variableName = $variable['name'];
                $variableRole = $variable['role'] ?? 'asesi';

                // NOTE: Include ALL variables in document generation regardless of role
                // The role distinction is only for the web form (editing), not the generated document

                // Skip signature_pad karena sudah ditangani terpisah sebagai image
                if (isset($variable['type']) && $variable['type'] === 'signature_pad') {
                    // Set placeholder text untuk signature pad
                    if ($variableName === 'ttd_digital_asesi' || strpos($variableName, 'ttd') !== false) {
                        $data[$variableName] = '[Tanda Tangan Digital]';
                    }
                    continue;
                }

                // Ambil nilai dari custom_variables pendaftaran atau asesor_data
                if ($asesorView && $variableRole === 'asesor' && isset($pendaftaran->asesor_data[$variableName])) {
                    $data[$variableName] = $pendaftaran->asesor_data[$variableName];
                } elseif (isset($pendaftaran->custom_variables[$variableName])) {
                    $data[$variableName] = $pendaftaran->custom_variables[$variableName];
                } else {
                    $data[$variableName] = ''; // Default kosong jika belum diisi
                }
            }
        }

        // Mapping tambahan untuk tanda tangan digital asesi
        // Ini akan di-replace dengan gambar sebenarnya di insertApl2Signatures
        $data['ttd_digital_asesi'] = $asesiName; // Untuk ditampilkan sebagai text jika gambar gagal
        $data['ttd_asesi'] = '[Tanda Tangan Digital Asesi]';
        $data['tanda_tangan_asesi'] = $asesiName;
        $data['signature_asesi'] = $asesiName;

        return $data;
    }

    /**
     * Insert signatures untuk APL2
     */
    private function insertApl2Signatures($templateProcessor, $pendaftaran, $asesorView = false)
    {
        try {
            // TTD Asesi - SELALU diinsert untuk SEMUA view
            if ($pendaftaran->ttd_asesi_path && file_exists(storage_path('app/public/' . $pendaftaran->ttd_asesi_path))) {
                $ttdAsesiPath = storage_path('app/public/' . $pendaftaran->ttd_asesi_path);
                \Illuminate\Support\Facades\Log::info('Inserting TTD Asesi from: ' . $ttdAsesiPath);

                // Try multiple placeholder formats - PhpWord tidak pakai ${}
                // Tambahkan lebih banyak variasi untuk memastikan TTD bisa diinsert
                $placeholders = [
                    'ttd_digital_asesi',
                    'ttd_asesi',
                    'tanda_tangan_asesi',
                    'signature_asesi',
                    'ttd_digital',
                    'tanggal_tangan_asesi' // typo yang mungkin ada di template
                ];
                $insertedCount = 0;

                foreach ($placeholders as $placeholder) {
                    try {
                        $templateProcessor->setImageValue(
                            $placeholder,
                            [
                                'path' => $ttdAsesiPath,
                                'width' => 150,
                                'height' => 75,
                                'ratio' => true
                            ]
                        );
                        \Illuminate\Support\Facades\Log::info("TTD Asesi inserted successfully with placeholder: {$placeholder}");
                        $insertedCount++;
                        // Jangan break, coba semua kemungkinan placeholder
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::debug("Failed with placeholder {$placeholder}: " . $e->getMessage());
                        continue;
                    }
                }

                if ($insertedCount === 0) {
                    \Illuminate\Support\Facades\Log::warning('TTD Asesi tidak bisa diinsert ke template dengan semua placeholder yang dicoba');
                } else {
                    \Illuminate\Support\Facades\Log::info("TTD Asesi berhasil diinsert dengan {$insertedCount} placeholder");
                }
            } else {
                \Illuminate\Support\Facades\Log::warning('TTD Asesi not found or file does not exist: ' . ($pendaftaran->ttd_asesi_path ?? 'null'));
            }

            // TTD Asesor (jika ada dan asesorView = true)
            if ($asesorView) {
                $ttdAsesorPath = null;

                // Cari TTD Asesor dari pendaftaran
                if ($pendaftaran->ttd_asesor_path && file_exists(storage_path('app/public/' . $pendaftaran->ttd_asesor_path))) {
                    $ttdAsesorPath = storage_path('app/public/' . $pendaftaran->ttd_asesor_path);
                    \Illuminate\Support\Facades\Log::info('TTD Asesor found from pendaftaran: ' . $ttdAsesorPath);
                }

                // Jika belum ada, coba cari dari relasi asesor di jadwal
                if (!$ttdAsesorPath && $pendaftaran->jadwal) {
                    if (!$pendaftaran->jadwal->relationLoaded('asesorSkema')) {
                        $pendaftaran->jadwal->load('asesorSkema.asesor');
                    }

                    $asesor = $pendaftaran->jadwal->asesorSkema->first();
                    if ($asesor && $asesor->asesor && $asesor->asesor->ttd_path) {
                        $ttdAsesorPath = storage_path('app/public/' . $asesor->asesor->ttd_path);
                        if (file_exists($ttdAsesorPath)) {
                            \Illuminate\Support\Facades\Log::info('TTD Asesor found from asesor profile: ' . $ttdAsesorPath);
                        } else {
                            $ttdAsesorPath = null;
                        }
                    }
                }

                if ($ttdAsesorPath) {
                    // Try multiple placeholder formats
                    $placeholders = [
                        'ttd_digital_asesor',
                        'ttd_asesor',
                        'tanda_tangan_asesor',
                        'signature_asesor',
                        'tanggal_tangan_asesor' // typo yang mungkin ada di template
                    ];
                    $insertedCount = 0;

                    foreach ($placeholders as $placeholder) {
                        try {
                            $templateProcessor->setImageValue(
                                $placeholder,
                                [
                                    'path' => $ttdAsesorPath,
                                    'width' => 150,
                                    'height' => 75,
                                    'ratio' => true
                                ]
                            );
                            \Illuminate\Support\Facades\Log::info("TTD Asesor inserted successfully with placeholder: {$placeholder}");
                            $insertedCount++;
                            // Jangan break, coba semua kemungkinan placeholder
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::debug("Failed with placeholder {$placeholder}: " . $e->getMessage());
                            continue;
                        }
                    }

                    if ($insertedCount === 0) {
                        \Illuminate\Support\Facades\Log::warning('TTD Asesor tidak bisa diinsert ke template dengan semua placeholder yang dicoba');
                    } else {
                        \Illuminate\Support\Facades\Log::info("TTD Asesor berhasil diinsert dengan {$insertedCount} placeholder");
                    }
                } else {
                    \Illuminate\Support\Facades\Log::warning('TTD Asesor not found. Path: ' . ($pendaftaran->ttd_asesor_path ?? 'null'));
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal insert signature APL2: ' . $e->getMessage());
        }
    }

    /**
     * Prepare data untuk template
     */
    public function prepareTemplateData(Pendaftaran $pendaftaran, array $customData = [])
    {
        $asesi = $pendaftaran->user;
        $skema = $pendaftaran->skema;
        $jadwal = $pendaftaran->jadwal;

        // Data default dengan error handling
        $data = [];

        try {
            // Ambil template untuk mendapatkan variables yang dipilih
            $template = TemplateMaster::active()
                ->byType('APL1')
                ->where('skema_id', $pendaftaran->skema_id)
                ->first();

            if (!$template || !$template->variables) {
                throw new \Exception('Template atau variables tidak ditemukan');
            }

            // Ensure variables is an array (fallback if cast doesn't work)
            if (is_string($template->variables)) {
                $template->variables = json_decode($template->variables, true) ?? [];
            }
            if (is_string($template->field_configurations)) {
                $template->field_configurations = json_decode($template->field_configurations, true) ?? [];
            }
            if (is_string($template->custom_variables)) {
                $template->custom_variables = json_decode($template->custom_variables, true) ?? [];
            }

            // Collect signature_pad field names untuk special handling
            $signaturePadFields = [];
            if ($template->field_configurations && is_array($template->field_configurations)) {
                foreach ($template->field_configurations as $fieldConfig) {
                    if (isset($fieldConfig['type']) && $fieldConfig['type'] === 'signature_pad') {
                        $signaturePadFields[] = $fieldConfig['name'];
                    }
                }
            }
            if (isset($template->custom_variables) && is_array($template->custom_variables)) {
                foreach ($template->custom_variables as $customVar) {
                    if (isset($customVar['type']) && $customVar['type'] === 'signature_pad' && isset($customVar['name'])) {
                        $signaturePadFields[] = $customVar['name'];
                    }
                }
            }

            // Prepare data berdasarkan variables yang dipilih di template
            $data = [];

            // PERTAMA: Ambil SEMUA data dari custom_variables pendaftaran (ini yang paling penting)
            // Karena data yang diinput user disimpan di sini, baik dari custom_variables maupun dynamic_fields
            if ($pendaftaran->custom_variables && is_array($pendaftaran->custom_variables)) {
                foreach ($pendaftaran->custom_variables as $key => $value) {
                    // Skip signature_pad fields - akan di-handle sebagai image
                    if (in_array($key, $signaturePadFields)) {
                        continue;
                    }

                    // Handle array values (checkbox)
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }

                    $data[$key] = $value;
                }
            }

            // KEDUA: Loop melalui template->variables untuk field yang belum ada di custom_variables
            // Ambil dari database fields (user, skema, jadwal, dll)
            foreach ($template->variables as $variable) {
                // Skip signature_pad fields - akan di-handle sebagai image, bukan text
                if (in_array($variable, $signaturePadFields)) {
                    continue;
                }

                // Jika sudah ada di data (dari custom_variables), skip
                if (isset($data[$variable])) {
                    continue;
                }

                // Ambil dari database fields
                $value = $this->getFieldValue($variable, $pendaftaran, $asesi, $skema, $jadwal);
                if ($value) {
                    $data[$variable] = $value;
                }
            }

            // TTD digital - jika tidak bisa sebagai gambar, tampilkan text info
            if (in_array('ttd_digital', $template->variables)) {
                if ($pendaftaran->ttd_asesi_path && file_exists(storage_path('app/public/' . $pendaftaran->ttd_asesi_path))) {
                    $data['ttd_digital'] = '[Tanda Tangan Digital Tersimpan - Lihat file asli di sistem]';
                } else {
                    $data['ttd_digital'] = '[Belum Ada Tanda Tangan Digital]';
                }
            }

            // KETIGA: Pastikan field dari field_configurations juga diambil (untuk memastikan tidak ada yang terlewat)
            if ($template->field_configurations && is_array($template->field_configurations)) {
                foreach ($template->field_configurations as $fieldConfig) {
                    $fieldName = $fieldConfig['name'] ?? null;
                    if (!$fieldName) {
                        continue;
                    }

                    // Skip signature_pad fields karena sudah di-handle sebagai image
                    if (isset($fieldConfig['type']) && $fieldConfig['type'] === 'signature_pad') {
                        continue;
                    }

                    // Jika data sudah ada, skip (jangan overwrite)
                    if (isset($data[$fieldName])) {
                        continue;
                    }

                    // Ambil data dari custom_variables pendaftaran (fallback)
                    if ($pendaftaran->custom_variables && isset($pendaftaran->custom_variables[$fieldName])) {
                        $fieldValue = $pendaftaran->custom_variables[$fieldName];
                        // Handle array values (checkbox)
                        if (is_array($fieldValue)) {
                            $fieldValue = implode(', ', $fieldValue);
                        }
                        $data[$fieldName] = $fieldValue;
                    }
                }
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error preparing template data: ' . $e->getMessage());
            throw $e;
        }

        // Merge dengan custom data
        $data = array_merge($data, $customData);

        return $data;
    }

    /**
     * Get field value berdasarkan variable name
     */
    private function getFieldValue($variable, $pendaftaran, $asesi, $skema, $jadwal, $asesor = null)
    {
        switch ($variable) {
            // Custom variables (yang kita buat di seeder)
            case 'nama_lengkap':
                return $asesi ? ($asesi->name ?? '') : '';
            case 'email_pribadi':
                return $asesi ? ($asesi->email ?? '') : '';
            case 'no_hp':
                return $asesi ? ($asesi->telephone ?? '') : '';
            case 'alamat':
                return $asesi ? ($asesi->alamat ?? '') : '';
            case 'nik':
                return $asesi ? ($asesi->nik ?? '') : '';
            case 'nim':
                return $asesi ? ($asesi->nim ?? '') : '';
            case 'tempat_lahir':
                return $asesi ? ($asesi->tempat_lahir ?? '') : '';
            case 'tanggal_lahir':
                if ($asesi && $asesi->tanggal_lahir) {
                    if (is_string($asesi->tanggal_lahir)) {
                        return $asesi->tanggal_lahir;
                    }
                    return $asesi->tanggal_lahir->format('d/m/Y');
                }
                return '';
            case 'jenis_kelamin':
                return $asesi ? ($asesi->jenis_kelamin ?? '') : '';
            case 'kebangsaan':
                return $asesi ? ($asesi->kebangsaan ?? '') : '';
            case 'pekerjaan':
                return $asesi ? ($asesi->pekerjaan ?? '') : '';
            case 'pendidikan':
                return $asesi ? ($asesi->pendidikan ?? '') : '';
            case 'jurusan':
                return $asesi ? ($asesi->jurusan ?? '') : '';
            case 'pengalaman_kerja':
                return '';
            case 'motivasi_sertifikasi':
                return '';

            // Legacy user fields (untuk kompatibilitas)
            case 'user.name':
                return $asesi ? ($asesi->name ?? '') : '';
            case 'user.email':
                return $asesi ? ($asesi->email ?? '') : '';
            case 'user.telephone':
                return $asesi ? ($asesi->telephone ?? '') : '';
            case 'user.alamat':
                return $asesi ? ($asesi->alamat ?? '') : '';
            case 'user.nik':
                return $asesi ? ($asesi->nik ?? '') : '';
            case 'user.nim':
                return $asesi ? ($asesi->nim ?? '') : '';
            case 'user.tempat_lahir':
                return $asesi ? ($asesi->tempat_lahir ?? '') : '';
            case 'user.tanggal_lahir':
                if ($asesi && $asesi->tanggal_lahir) {
                    if (is_string($asesi->tanggal_lahir)) {
                        return $asesi->tanggal_lahir;
                    }
                    return $asesi->tanggal_lahir->format('d/m/Y');
                }
                return '';
            case 'user.jenis_kelamin':
                return $asesi ? ($asesi->jenis_kelamin ?? '') : '';
            case 'user.kebangsaan':
                return $asesi ? ($asesi->kebangsaan ?? '') : '';
            case 'user.pekerjaan':
                return $asesi ? ($asesi->pekerjaan ?? '') : '';
            case 'user.pendidikan':
                return $asesi ? ($asesi->pendidikan ?? '') : '';
            case 'user.jurusan':
                return $asesi ? ($asesi->jurusan ?? '') : '';

            // Skema fields
            case 'skema.nama':
                return $skema ? ($skema->nama ?? '') : '';
            case 'skema.kode':
                return $skema ? ($skema->kode ?? '') : '';
            case 'skema.kategori':
                return $skema ? ($skema->kategori ?? '') : '';
            case 'skema.bidang':
                return $skema ? ($skema->bidang ?? '') : '';

            // Asesor fields
            case 'asesor.name':
                return $asesor ? ($asesor->name ?? '') : '';
            case 'asesor.email':
                return $asesor ? ($asesor->email ?? '') : '';
            case 'asesor.telephone':
                return $asesor ? ($asesor->telephone ?? '') : '';
            case 'asesor.nik':
                return $asesor ? ($asesor->nik ?? '') : '';
            case 'asesor.nip':
                return $asesor ? ($asesor->nip ?? '') : '';
            case 'asesor.tempat_lahir':
                return $asesor ? ($asesor->tempat_lahir ?? '') : '';
            case 'asesor.tanggal_lahir':
                if ($asesor && $asesor->tanggal_lahir) {
                    if (is_string($asesor->tanggal_lahir)) {
                        return $asesor->tanggal_lahir;
                    }
                    return $asesor->tanggal_lahir->format('d/m/Y');
                }
                return '';
            case 'asesor.jenis_kelamin':
                return $asesor ? ($asesor->jenis_kelamin ?? '') : '';
            case 'asesor.alamat':
                return $asesor ? ($asesor->alamat ?? '') : '';
            case 'asesor.pendidikan':
                return $asesor ? ($asesor->pendidikan ?? '') : '';
            case 'asesor.tanda_tangan':
                return $asesor ? ($asesor->tanda_tangan ?? '') : '';

            // Jadwal fields
            case 'jadwal.tanggal_ujian':
                return $jadwal ? ($jadwal->tanggal_ujian ?? '') : '';
            case 'jadwal.waktu_mulai':
                return $jadwal ? ($jadwal->waktu_mulai ?? '') : '';
            case 'jadwal.waktu_selesai':
                return $jadwal ? ($jadwal->waktu_selesai ?? '') : '';
            case 'jadwal.tuk.nama':
                return ($jadwal && $jadwal->tuk) ? ($jadwal->tuk->nama ?? '') : '';

            // System fields
            case 'system.tanggal_generate':
                return now()->format('d/m/Y');
            case 'system.waktu_generate':
                return now()->format('H:i:s');
            case 'system.nomor_pendaftaran':
                return $pendaftaran->id ?? '';

            // TTD digital
            case 'ttd_digital':
                return '[TTD Digital]';

            default:
                // Custom variable - return as is atau dari custom data
                return '';
        }
    }

    /**
     * Generate PDF dari DOCX
     */
    public function generatePdfFromDocx($docxPath, $pdfPath = null)
    {
        try {
            // Set LibreOffice path (jika tersedia)
            Settings::setPdfRendererName(Settings::PDF_RENDERER_DOMPDF);
            Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));

            // Load document
            $phpWord = IOFactory::load($docxPath);

            // Generate PDF path jika tidak disediakan
            if (!$pdfPath) {
                $pdfPath = str_replace('.docx', '.pdf', $docxPath);
            }

            // Convert to PDF
            $xmlWriter = IOFactory::createWriter($phpWord, 'PDF');
            $xmlWriter->save($pdfPath);

            return [
                'success' => true,
                'pdf_path' => $pdfPath
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get template variables yang tersedia
     */
    public function getAvailableVariables($tipeTemplate = 'APL1')
    {
        $defaultVariables = [
            // Data Asesi
            'nama_asesi' => 'Nama Lengkap Asesi',
            'email_asesi' => 'Email Asesi',
            'telephone_asesi' => 'Nomor Telepon Asesi',
            'alamat_asesi' => 'Alamat Asesi',
            'nik_asesi' => 'NIK Asesi',

            // Data Skema
            'nama_skema' => 'Nama Skema Sertifikasi',
            'kode_skema' => 'Kode Skema',
            'kategori_skema' => 'Kategori Skema',
            'bidang_skema' => 'Bidang Skema',

            // Data Jadwal
            'tanggal_ujian' => 'Tanggal Ujian',
            'waktu_mulai' => 'Waktu Mulai Ujian',
            'waktu_selesai' => 'Waktu Selesai Ujian',
            'lokasi_ujian' => 'Lokasi Ujian (TUK)',

            // Data Sistem
            'tanggal_generate' => 'Tanggal Generate Dokumen',
            'waktu_generate' => 'Waktu Generate Dokumen',
            'nomor_pendaftaran' => 'Nomor Pendaftaran',
        ];

        return $defaultVariables;
    }

    /**
     * Check apakah template tersedia
     */
    public function checkTemplateExists($tipeTemplate, $skemaId)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Checking template exists - Type: ' . $tipeTemplate . ', Skema ID: ' . $skemaId);

            $template = TemplateMaster::active()
                ->byType($tipeTemplate)
                ->where('skema_id', $skemaId)
                ->first();

            \Illuminate\Support\Facades\Log::info('Template check result: ' . ($template ? 'Found' : 'Not found'));

            return $template !== null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error checking template exists: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate template file
     */
    public function validateTemplate($templatePath)
    {
        try {
            $templateProcessor = new TemplateProcessor($templatePath);

            // Get variables dari template
            $templateVariables = [];
            $variables = $templateProcessor->getVariables();

            foreach ($variables as $variable) {
                $templateVariables[] = $variable;
            }

            return [
                'success' => true,
                'variables' => $templateVariables
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
