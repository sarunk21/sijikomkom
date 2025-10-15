<?php

namespace App\Services;

use App\Models\TemplateMaster;
use App\Models\Pendaftaran;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TemplateGeneratorService
{
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

            // Replace variables dalam template
            foreach ($data as $key => $value) {
                // Convert key to template format (e.g., 'user.name' -> 'user.name')
                $templateKey = $key;
                $templateProcessor->setValue($templateKey, $value);
                \Log::info("Setting template variable: {$templateKey} = {$value}");
            }

            // Log untuk debug
            \Log::info('Template variables being set: ' . json_encode($data));

            // Insert TTD digital - prioritas TTD asesi, fallback ke template TTD
            $ttdPath = null;
            if ($pendaftaran->ttd_asesi_path && file_exists(storage_path('app/public/' . $pendaftaran->ttd_asesi_path))) {
                $ttdPath = storage_path('app/public/' . $pendaftaran->ttd_asesi_path);
                \Log::info('Using asesi TTD: ' . $pendaftaran->ttd_asesi_path);
            } elseif ($template->ttd_path && file_exists(storage_path('app/public/' . $template->ttd_path))) {
                $ttdPath = storage_path('app/public/' . $template->ttd_path);
                \Log::info('Using template TTD: ' . $template->ttd_path);
            }

            if ($ttdPath) {
                try {
                    $templateProcessor->setImageValue(
                        'ttd_digital',
                        [
                            'path' => $ttdPath,
                            'width' => 150,
                            'height' => 75,
                            'ratio' => true
                        ]
                    );
                    \Log::info('TTD digital inserted successfully');
                } catch (\Exception $e) {
                    // Jika gagal insert TTD, lanjutkan tanpa TTD
                    \Log::warning('Gagal insert TTD digital: ' . $e->getMessage());
                }
            }

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
                    $data[$variable] = $this->getFieldValue($variable, $pendaftaran, $asesi, $skema, $jadwal);
                }
            }

            // Tambahkan TTD digital - prioritas TTD asesi, fallback ke template TTD
            if (in_array('ttd_digital', $template->variables)) {
                if ($pendaftaran->ttd_asesi_path && file_exists(storage_path('app/public/' . $pendaftaran->ttd_asesi_path))) {
                    $data['ttd_digital'] = '[TTD Asesi]';
                } elseif ($template->ttd_path && file_exists(storage_path('app/public/' . $template->ttd_path))) {
                    $data['ttd_digital'] = '[TTD Template]';
                } else {
                    $data['ttd_digital'] = '[TTD Digital]';
                }
            }

        } catch (\Exception $e) {
            \Log::error('Error preparing template data: ' . $e->getMessage());
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
            // User fields
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
                return $asesi ? ($asesi->tanggal_lahir ?? '') : '';
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
            \Log::info('Checking template exists - Type: ' . $tipeTemplate . ', Skema ID: ' . $skemaId);

            $template = TemplateMaster::active()
                ->byType($tipeTemplate)
                ->where('skema_id', $skemaId)
                ->first();

            \Log::info('Template check result: ' . ($template ? 'Found' : 'Not found'));

            return $template !== null;
        } catch (\Exception $e) {
            \Log::error('Error checking template exists: ' . $e->getMessage());
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
