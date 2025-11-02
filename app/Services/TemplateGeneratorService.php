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

            // Siapkan data untuk mengganti variable
            $data = $this->prepareApl2TemplateData($pendaftaran, $asesorView);

            // Replace variables dalam template
            foreach ($data as $key => $value) {
                // Convert key to template format dengan dollar sign (e.g., 'nama_asesi' -> '${nama_asesi}')
                $templateKey = '${' . $key . '}';
                $templateProcessor->setValue($templateKey, $value);
                \Illuminate\Support\Facades\Log::info("Setting template variable: {$templateKey} = {$value}");
            }

            // Insert TTD digital
            $this->insertApl2Signatures($templateProcessor, $pendaftaran, $asesorView);

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
                    // Coba berbagai format placeholder
                    $placeholders = ['ttd_digital', '${ttd_digital}', 'ttd_digital}', '{ttd_digital'];
                    $success = false;
                    
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
                            $success = true;
                            break;
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::debug("Failed with placeholder {$placeholder}: " . $e->getMessage());
                            continue;
                        }
                    }
                    
                    if (!$success) {
                        \Illuminate\Support\Facades\Log::warning('Could not insert TTD with any placeholder format');
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Error inserting TTD: ' . $e->getMessage());
                }
            } else {
                \Illuminate\Support\Facades\Log::warning('TTD path not found for pendaftaran: ' . $pendaftaran->id);
            }

            // Replace variables dalam template
            foreach ($data as $key => $value) {
                // Skip ttd_digital karena sudah di-handle sebagai image
                if ($key === 'ttd_digital') {
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

        // Ambil template untuk mendapatkan custom variables
        $template = TemplateMaster::active()
            ->byType('APL2')
            ->where('skema_id', $pendaftaran->skema_id)
            ->first();

        if (!$template) {
            throw new \Exception("Template APL2 untuk skema {$skema->nama} tidak ditemukan.");
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

                // Generate checkbox untuk template Word dengan format tabel
                if ($answer === 'K') {
                    $kCheckboxContent .= "√\n"; // Centang di kolom K
                    $bkCheckboxContent .= "\n"; // Kosong di kolom BK
                    $radioKCheckboxContent .= "√\n"; // Centang khusus untuk radio K
                    $radioBkCheckboxContent .= "\n"; // Kosong khusus untuk radio BK
                } else {
                    $kCheckboxContent .= "\n"; // Kosong di kolom K
                    $bkCheckboxContent .= "√\n"; // Centang di kolom BK
                    $radioKCheckboxContent .= "\n"; // Kosong khusus untuk radio K
                    $radioBkCheckboxContent .= "√\n"; // Centang khusus untuk radio BK
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

        return $data;
    }

    /**
     * Insert signatures untuk APL2
     */
    private function insertApl2Signatures($templateProcessor, $pendaftaran, $asesorView = false)
    {
        try {
            // TTD Asesi
            if ($pendaftaran->ttd_asesi_path && file_exists(storage_path('app/public/' . $pendaftaran->ttd_asesi_path))) {
                $ttdAsesiPath = storage_path('app/public/' . $pendaftaran->ttd_asesi_path);
                \Illuminate\Support\Facades\Log::info('Inserting TTD Asesi from: ' . $ttdAsesiPath);
                
                try {
                    $templateProcessor->setImageValue(
                        'ttd_asesi',
                        [
                            'path' => $ttdAsesiPath,
                            'width' => 150,
                            'height' => 75,
                            'ratio' => true
                        ]
                    );
                    \Illuminate\Support\Facades\Log::info('TTD Asesi inserted successfully');
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to insert TTD Asesi: ' . $e->getMessage());
                    // Fallback
                    try {
                        $templateProcessor->setImageValue(
                            '${ttd_asesi}',
                            [
                                'path' => $ttdAsesiPath,
                                'width' => 150,
                                'height' => 75,
                                'ratio' => true
                            ]
                        );
                        \Illuminate\Support\Facades\Log::info('TTD Asesi inserted with fallback format');
                    } catch (\Exception $e2) {
                        \Illuminate\Support\Facades\Log::warning('Failed fallback insert TTD Asesi: ' . $e2->getMessage());
                    }
                }
            }

            // TTD Asesor (jika ada)
            if ($asesorView) {
                $responses = \App\Models\Response::where('pendaftaran_id', $pendaftaran->id)->first();
                if ($responses && $responses->asesor_signature) {
                    // Convert base64 signature to image and save temporarily
                    $signatureData = $responses->asesor_signature;
                    if (strpos($signatureData, 'data:image') === 0) {
                        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));
                        $tempPath = storage_path('app/temp/asesor_signature_' . $pendaftaran->id . '.png');

                        // Ensure temp directory exists
                        if (!file_exists(dirname($tempPath))) {
                            mkdir(dirname($tempPath), 0755, true);
                        }

                        file_put_contents($tempPath, $imageData);

                        try {
                            $templateProcessor->setImageValue(
                                'ttd_asesor',
                                [
                                    'path' => $tempPath,
                                    'width' => 150,
                                    'height' => 75,
                                    'ratio' => true
                                ]
                            );
                            \Illuminate\Support\Facades\Log::info('TTD Asesor inserted successfully');
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::warning('Failed to insert TTD Asesor: ' . $e->getMessage());
                            // Fallback
                            try {
                                $templateProcessor->setImageValue(
                                    '${ttd_asesor}',
                                    [
                                        'path' => $tempPath,
                                        'width' => 150,
                                        'height' => 75,
                                        'ratio' => true
                                    ]
                                );
                            } catch (\Exception $e2) {
                                \Illuminate\Support\Facades\Log::warning('Failed fallback insert TTD Asesor: ' . $e2->getMessage());
                            }
                        }

                        // Clean up temp file
                        if (file_exists($tempPath)) {
                            unlink($tempPath);
                        }
                    }
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

            // Prepare data berdasarkan variables yang dipilih di template
            $data = [];
            foreach ($template->variables as $variable) {
                // Cek apakah ini custom variable dari asesi
                if ($pendaftaran->custom_variables && isset($pendaftaran->custom_variables[$variable])) {
                    $data[$variable] = $pendaftaran->custom_variables[$variable];
                } else {
                    $value = $this->getFieldValue($variable, $pendaftaran, $asesi, $skema, $jadwal);
                    $data[$variable] = $value ?: '';
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
    private function getFieldValue($variable, $pendaftaran, $asesi, $skema, $jadwal)
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
