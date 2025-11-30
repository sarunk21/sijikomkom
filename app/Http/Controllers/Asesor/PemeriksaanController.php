<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\AsesiPenilaian;
use App\Models\BankSoal;
use App\Models\FormulirResponse;
use App\Models\Jadwal;
use App\Models\PendaftaranUjikom;
use App\Models\Report;
use App\Models\User;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

class PemeriksaanController extends Controller
{
    use MenuTrait;

    /**
     * Helper: Get jadwal dengan verifikasi skema asesor
     */
    private function getJadwalForAsesor($jadwalId)
    {
        $asesor = Auth::user();
        
        // Cek apakah asesor ditugaskan di jadwal ini
        $hasPendaftaran = DB::table('pendaftaran_ujikom')
            ->where('jadwal_id', $jadwalId)
            ->where('asesor_id', $asesor->id)
            ->exists();

        if (!$hasPendaftaran) {
            // Jika tidak ditugaskan, cek berdasarkan skema
            $skemaIds = DB::table('asesor_skema')
                ->where('asesor_id', $asesor->id)
                ->pluck('skema_id');

            return Jadwal::where('id', $jadwalId)
                ->whereIn('skema_id', $skemaIds)
                ->with('skema')
                ->firstOrFail();
        }

        // Jika ditugaskan, langsung return jadwal
        return Jadwal::where('id', $jadwalId)
            ->with('skema')
            ->firstOrFail();
    }

    /**
     * List semua asesi dalam jadwal yang perlu diperiksa
     */
    public function asesiList($jadwalId)
    {
        $lists = $this->getMenuListAsesor('pemeriksaan');
        $activeMenu = 'pemeriksaan';

        $jadwal = $this->getJadwalForAsesor($jadwalId);
        $asesorId = Auth::id();

        // Get all asesi in this jadwal through pendaftaran_ujikom
        $asesiIds = DB::table('pendaftaran_ujikom')
            ->where('jadwal_id', $jadwalId)
            ->pluck('asesi_id');

        $asesis = User::whereIn('id', $asesiIds)
            ->with(['asesiPenilaian' => function ($query) use ($jadwalId) {
                $query->where('jadwal_id', $jadwalId);
            }])
            ->get();

        // Ensure AsesiPenilaian record exists for each asesi
        foreach ($asesis as $asesi) {
            if (!$asesi->asesiPenilaian->first()) {
                AsesiPenilaian::create([
                    'jadwal_id' => $jadwalId,
                    'user_id' => $asesi->id,
                    'asesor_id' => $asesorId,
                ]);
            }
        }

        // Reload asesis with penilaian
        $asesis = User::whereIn('id', $asesiIds)
            ->with(['asesiPenilaian' => function ($query) use ($jadwalId) {
                $query->where('jadwal_id', $jadwalId);
            }])
            ->get();

        return view('components.pages.asesor.pemeriksaan.asesi-list', compact(
            'lists',
            'activeMenu',
            'jadwal',
            'asesis'
        ));
    }

    /**
     * List formulir yang perlu diperiksa untuk satu asesi
     */
    public function formulirList($jadwalId, $asesiId)
    {
        $lists = $this->getMenuListAsesor('pemeriksaan');
        $activeMenu = 'pemeriksaan';

        try {
            $jadwal = $this->getJadwalForAsesor($jadwalId);
        } catch (\Exception $e) {
            return redirect()->route('asesor.hasil-ujikom.index')
                ->with('error', 'Jadwal tidak ditemukan atau Anda tidak memiliki akses ke jadwal ini.');
        }

        $asesi = User::findOrFail($asesiId);

        // Get all bank soal untuk skema ini (semua target: asesi, asesor, both)
        // Asesor perlu melihat semua formulir untuk review jawaban asesi
        $bankSoals = BankSoal::where('skema_id', $jadwal->skema_id)
            ->where('is_active', true)
            ->orderBy('nama', 'asc')
            ->get();

        // Get responses dari asesi
        $responses = FormulirResponse::where('jadwal_id', $jadwalId)
            ->where('user_id', $asesiId)
            ->get()
            ->keyBy('bank_soal_id');

        // Get or create penilaian record
        $penilaian = AsesiPenilaian::firstOrCreate(
            [
                'jadwal_id' => $jadwalId,
                'user_id' => $asesiId,
            ],
            [
                'asesor_id' => Auth::id(),
            ]
        );

        return view('components.pages.asesor.pemeriksaan.formulir-list', compact(
            'lists',
            'activeMenu',
            'jadwal',
            'asesi',
            'bankSoals',
            'responses',
            'penilaian'
        ));
    }

    /**
     * Review dan validasi jawaban asesi
     */
    public function review($jadwalId, $asesiId, $bankSoalId)
    {
        $lists = $this->getMenuListAsesor('pemeriksaan');
        $activeMenu = 'pemeriksaan';

        $jadwal = $this->getJadwalForAsesor($jadwalId);
        $asesi = User::findOrFail($asesiId);
        $bankSoal = BankSoal::findOrFail($bankSoalId);

        // Get or create response
        $response = FormulirResponse::firstOrCreate(
            [
                'jadwal_id' => $jadwalId,
                'user_id' => $asesiId,
                'bank_soal_id' => $bankSoalId,
            ],
            [
                'status' => 'draft',
                'asesi_responses' => [],
            ]
        );

        // Get custom fields
        // Cek apakah menggunakan field_configurations atau custom_variables
        $fieldSource = $bankSoal->field_configurations ?? $bankSoal->custom_variables ?? [];

        // Jika Bank Soal target nya 'asesor', maka semua field masuk ke asesorFields
        // Jika target nya 'asesi', maka:
        //   - Field role 'asesi' dan 'both' -> asesiFields (untuk ditampilkan jawaban asesi)
        //   - Field role 'asesor' -> asesorFields (untuk diisi asesor)

        if ($bankSoal->target === 'asesor') {
            // Untuk formulir asesor, semua field diisi oleh asesor
            $asesiFields = collect([]);
            $asesorFields = collect($fieldSource);
        } else {
            // Untuk formulir asesi, pisahkan field berdasarkan role
            $asesiFields = collect($fieldSource)
                ->filter(function ($field) {
                    return in_array($field['role'] ?? 'asesi', ['asesi', 'both']);
                });

            $asesorFields = collect($fieldSource)
                ->filter(function ($field) {
                    return ($field['role'] ?? 'asesi') === 'asesor';
                });
        }

        return view('components.pages.asesor.pemeriksaan.review', compact(
            'lists',
            'activeMenu',
            'jadwal',
            'asesi',
            'bankSoal',
            'response',
            'asesiFields',
            'asesorFields'
        ));
    }

    /**
     * Save asesor review (jawaban asesor + validasi)
     */
    public function saveReview(Request $request, $jadwalId, $asesiId, $bankSoalId)
    {
        $response = FormulirResponse::where('jadwal_id', $jadwalId)
            ->where('user_id', $asesiId)
            ->where('bank_soal_id', $bankSoalId)
            ->firstOrFail();

        $response->update([
            'asesor_responses' => $request->asesor_responses ?? [],
            'asesor_validations' => $request->asesor_validations ?? [],
            'is_asesor_completed' => $request->is_asesor_completed ?? false,
            'catatan_asesor' => $request->catatan_asesor,
            'status' => 'reviewed',
            'reviewed_at' => now(),
        ]);

        // Update formulir status in AsesiPenilaian
        $penilaian = AsesiPenilaian::where('jadwal_id', $jadwalId)
            ->where('user_id', $asesiId)
            ->firstOrFail();

        $formulirStatus = $penilaian->formulir_status ?? [];
        $formulirStatus[$bankSoalId] = [
            'is_checked' => $request->is_asesor_completed ?? false,
            'is_valid' => $this->checkAllValid($request->asesor_validations ?? []),
        ];

        $penilaian->update([
            'formulir_status' => $formulirStatus,
        ]);

        return redirect()->route('asesor.pemeriksaan.formulir-list', [$jadwalId, $asesiId])
            ->with('success', 'Review berhasil disimpan');
    }

    /**
     * Form FR AI 07 (Penilaian Asesor)
     * DEPRECATED: FR AI 07 sekarang sudah masuk ke Bank Soal (Analis Program)
     */
    // public function frAi07($jadwalId, $asesiId)
    // {
    //     $lists = $this->getMenuListAsesor('pemeriksaan');
    //     $activeMenu = 'pemeriksaan';

    //     $jadwal = $this->getJadwalForAsesor($jadwalId);
    //     $asesi = User::findOrFail($asesiId);

    //     $penilaian = AsesiPenilaian::where('jadwal_id', $jadwalId)
    //         ->where('user_id', $asesiId)
    //         ->firstOrFail();

    //     return view('components.pages.asesor.pemeriksaan.fr-ai-07', compact(
    //         'lists',
    //         'activeMenu',
    //         'jadwal',
    //         'asesi',
    //         'penilaian'
    //     ));
    // }

    /**
     * Save FR AI 07
     * DEPRECATED: FR AI 07 sekarang sudah masuk ke Bank Soal (Analis Program)
     */
    // public function saveFrAi07(Request $request, $jadwalId, $asesiId)
    // {
    //     $penilaian = AsesiPenilaian::where('jadwal_id', $jadwalId)
    //         ->where('user_id', $asesiId)
    //         ->firstOrFail();

    //     $penilaian->update([
    //         'fr_ai_07_data' => $request->fr_ai_07_data,
    //         'fr_ai_07_completed' => true,
    //     ]);

    //     return redirect()->route('asesor.pemeriksaan.formulir-list', [$jadwalId, $asesiId])
    //         ->with('success', 'FR AI 07 berhasil disimpan');
    // }

    /**
     * Form penilaian BK/K
     */
    public function penilaian($jadwalId, $asesiId)
    {
        $lists = $this->getMenuListAsesor('pemeriksaan');
        $activeMenu = 'pemeriksaan';

        $jadwal = $this->getJadwalForAsesor($jadwalId);
        $asesi = User::findOrFail($asesiId);

        $penilaian = AsesiPenilaian::where('jadwal_id', $jadwalId)
            ->where('user_id', $asesiId)
            ->firstOrFail();

        // Check if sudah dinilai
        if ($penilaian->hasil_akhir !== 'belum_dinilai') {
            return redirect()->route('asesor.pemeriksaan.formulir-list', [$jadwalId, $asesiId])
                ->with('info', 'Asesi ini sudah dinilai sebelumnya.');
        }

        // Check if can give hasil akhir
        if (!$penilaian->canGiveHasilAkhir()) {
            return redirect()->route('asesor.pemeriksaan.formulir-list', [$jadwalId, $asesiId])
                ->with('error', 'Belum bisa memberikan penilaian. Pastikan semua formulir sudah diperiksa dan FR AI 07 sudah diisi.');
        }

        return view('components.pages.asesor.pemeriksaan.penilaian', compact(
            'lists',
            'activeMenu',
            'jadwal',
            'asesi',
            'penilaian'
        ));
    }

    /**
     * Save penilaian BK/K
     */
    public function savePenilaian(Request $request, $jadwalId, $asesiId)
    {
        $request->validate([
            'hasil_akhir' => 'required|in:kompeten,belum_kompeten',
            'catatan_asesor' => 'nullable|string',
        ]);

        $penilaian = AsesiPenilaian::where('jadwal_id', $jadwalId)
            ->where('user_id', $asesiId)
            ->firstOrFail();

        // Check if sudah dinilai
        if ($penilaian->hasil_akhir !== 'belum_dinilai') {
            return redirect()->route('asesor.pemeriksaan.formulir-list', [$jadwalId, $asesiId])
                ->with('error', 'Penilaian sudah pernah diberikan sebelumnya dan tidak dapat diubah.');
        }

        if (!$penilaian->canGiveHasilAkhir()) {
            return redirect()->back()->with('error', 'Belum memenuhi syarat untuk memberikan penilaian.');
        }

        $penilaian->update([
            'hasil_akhir' => $request->hasil_akhir,
            'catatan_asesor' => $request->catatan_asesor,
            'penilaian_at' => now(),
            'asesor_id' => Auth::id(),
        ]);

        // Update status di pendaftaran_ujikom untuk sinkronisasi dengan sistem lama
        $pendaftaranUjikom = PendaftaranUjikom::where('jadwal_id', $jadwalId)
            ->where('asesi_id', $asesiId)
            ->first();

        if ($pendaftaranUjikom) {
            $status = $request->hasil_akhir === 'kompeten' ? 5 : 4;
            $keterangan = $request->hasil_akhir === 'kompeten' ? 'Kompeten' : 'Tidak Kompeten';
            
            $pendaftaranUjikom->update([
                'status' => $status,
                'keterangan' => $keterangan,
                'asesor_id' => Auth::id(),
            ]);

            // Insert ke report jika belum ada
            $existingReport = Report::where('pendaftaran_id', $pendaftaranUjikom->id)
                ->where('user_id', $asesiId)
                ->first();

            if (!$existingReport) {
                Report::create([
                    'user_id' => $asesiId,
                    'pendaftaran_id' => $pendaftaranUjikom->id,
                    'skema_id' => $pendaftaranUjikom->jadwal->skema_id,
                    'jadwal_id' => $jadwalId,
                    'status' => $request->hasil_akhir === 'kompeten' ? 1 : 2,
                ]);
            }
        }

        return redirect()->route('asesor.hasil-ujikom.show', $jadwalId)
            ->with('success', 'Penilaian berhasil disimpan');
    }

    /**
     * Generate template untuk formulir tertentu
     */
    public function generateTemplate($jadwalId, $asesiId, $bankSoalId)
    {
        $jadwal = $this->getJadwalForAsesor($jadwalId);
        $asesi = User::findOrFail($asesiId);
        $bankSoal = BankSoal::findOrFail($bankSoalId);

        $response = FormulirResponse::where('jadwal_id', $jadwalId)
            ->where('user_id', $asesiId)
            ->where('bank_soal_id', $bankSoalId)
            ->firstOrFail();

        // Check if template file exists
        // File disimpan di storage/app/public/bank-soal/xxx.docx
        $templatePath = storage_path('app/public/' . $bankSoal->file_path);

        if (!$bankSoal->file_path || !file_exists($templatePath)) {
            return redirect()->back()->with('error', 'File template tidak ditemukan: ' . $bankSoal->file_path);
        }

        // Load template
        $templateProcessor = new TemplateProcessor($templatePath);

        // Get template variables untuk debugging
        $templateVariables = $templateProcessor->getVariables();

        // Prepare data for replacement
        $data = $this->prepareTemplateData($jadwal, $asesi, $response, $bankSoal);

        // Log data untuk debugging
        \Log::info('Template Generation Data', [
            'bank_soal' => $bankSoal->nama,
            'asesi' => $asesi->name,
            'template_file' => $bankSoal->file_path,
            'template_variables_in_docx' => $templateVariables,
            'bank_soal_variables' => $bankSoal->variables,
            'bank_soal_field_mappings' => $bankSoal->field_mappings,
            'data_keys' => array_keys($data),
            'validations' => $response->asesor_validations ?? [],
        ]);
        
        \Log::info('Data values for replacement', [
            'asesor.name' => $data['asesor.name'] ?? 'NOT SET',
            'asesor_name' => $data['asesor_name'] ?? 'NOT SET',
            'nama_asesor' => $data['nama_asesor'] ?? 'NOT SET',
            'user.name' => $data['user.name'] ?? 'NOT SET',
            'user_name' => $data['user_name'] ?? 'NOT SET',
            'nama_asesi' => $data['nama_asesi'] ?? 'NOT SET',
            'skema.nama' => $data['skema.nama'] ?? 'NOT SET',
            'skema_nama' => $data['skema_nama'] ?? 'NOT SET',
            'nama_skema' => $data['nama_skema'] ?? 'NOT SET',
            'jadwal.tanggal_ujian' => $data['jadwal.tanggal_ujian'] ?? 'NOT SET',
        ]);

        // PENTING: Insert signature images DULU sebelum setValue
        // Karena setValue bisa menghapus placeholder image
        $this->insertSignatureImages($templateProcessor, $response);

        // Replace variables dari template DOCX
        // PHPWord's getVariables() mungkin tidak detect semua variable jika ada special chars
        // Jadi kita loop semua data yang kita punya
        $signatureFields = ['ttd_digital_asesi', 'ttd_digital_asesor', 'ttd_asesi', 'ttd_asesor'];
        
        foreach ($templateVariables as $templateVar) {
            // Skip signature fields
            if (in_array($templateVar, $signatureFields)) {
                continue;
            }
            
            // Check if we have this variable in our data
            if (isset($data[$templateVar])) {
                $value = $data[$templateVar];
                $stringValue = is_array($value) ? json_encode($value) : (string)$value;
                try {
                    $templateProcessor->setValue($templateVar, $stringValue);
                    \Log::info("Successfully replaced: $templateVar = $stringValue");
                } catch (\Exception $e) {
                    \Log::warning("Failed to replace $templateVar: " . $e->getMessage());
                }
            } else {
                // Variable not found in data, set empty or log warning
                \Log::warning("Template variable '$templateVar' not found in data");
                try {
                    $templateProcessor->setValue($templateVar, '');
                } catch (\Exception $e) {
                    // Ignore
                }
            }
        }
        
        // Also try to replace all data keys (in case getVariables missed some)
        foreach ($data as $key => $value) {
            // Skip signature fields
            if (in_array($key, $signatureFields)) {
                continue;
            }
            
            $stringValue = is_array($value) ? json_encode($value) : (string)$value;
            
            try {
                $templateProcessor->setValue($key, $stringValue);
            } catch (\Exception $e) {
                // Silently ignore, might not be in template
            }
        }

        // Generate filename
        $filename = $this->generateFilename($bankSoal, $asesi);
        $outputPath = storage_path('app/public/generated/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }

        // Save generated document
        $templateProcessor->saveAs($outputPath);

        // Download
        return response()->download($outputPath)->deleteFileAfterSend(true);
    }

    /**
     * Helper: Check if all validations are valid
     */
    private function checkAllValid($validations)
    {
        if (empty($validations)) {
            return true; // Default sesuai
        }

        foreach ($validations as $validation) {
            if (isset($validation['is_valid']) && !$validation['is_valid']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Helper: Prepare template data
     */
    private function prepareTemplateData($jadwal, $asesi, $response, $bankSoal)
    {
        $data = [];
        
        // Get current asesor data
        $asesor = Auth::user();

        // Database fields from variables (auto-fill dari DB)
        if ($bankSoal->variables && is_array($bankSoal->variables)) {
            foreach ($bankSoal->variables as $variable) {
                $data[$variable] = $this->getFieldValue($variable, $asesi, $jadwal, $asesor);
            }
        }

        // Database fields from field_mappings (custom mapping)
        if ($bankSoal->field_mappings) {
            foreach ($bankSoal->field_mappings as $variable => $dbField) {
                $data[$variable] = $this->getFieldValue($dbField, $asesi, $jadwal, $asesor);
            }
        }

        // Custom variables from asesi responses
        if ($response->asesi_responses) {
            foreach ($response->asesi_responses as $fieldName => $value) {
                $data[$fieldName] = $value;
            }
        }

        // Custom variables from asesor responses
        if ($response->asesor_responses) {
            foreach ($response->asesor_responses as $fieldName => $value) {
                $data[$fieldName] = $value;
            }
        }

        // Process validations for checkbox
        if ($response->asesor_validations) {
            foreach ($response->asesor_validations as $fieldName => $validation) {
                // For checkbox fields, set checked based on validation
                if (isset($validation['is_valid'])) {
                    // Use X and space instead of checkbox symbols to avoid encoding issues
                    $data[$fieldName . '_ya'] = $validation['is_valid'] ? 'X' : '';
                    $data[$fieldName . '_tdk'] = !$validation['is_valid'] ? 'X' : '';
                }
            }
        }
        
        // Explicit mapping for common asesor fields
        $asesorName = $asesor->name ?? '';
        $asesorEmail = $asesor->email ?? '';
        $asesorNoReg = $asesor->asesor->no_reg ?? '';
        
        $data['asesor.name'] = $asesorName;
        $data['asesor_name'] = $asesorName;
        $data['asesorname'] = $asesorName;
        $data['nama_asesor'] = $asesorName;
        
        $data['asesor.email'] = $asesorEmail;
        $data['asesor_email'] = $asesorEmail;
        $data['asesoremail'] = $asesorEmail;
        $data['email_asesor'] = $asesorEmail;
        
        $data['asesor.no_reg'] = $asesorNoReg;
        $data['asesor_no_reg'] = $asesorNoReg;
        $data['asesornoReg'] = $asesorNoReg;
        $data['no_reg_asesor'] = $asesorNoReg;
        
        // Mapping untuk asesi juga
        $data['user.name'] = $asesi->name ?? '';
        $data['user_name'] = $asesi->name ?? '';
        $data['username'] = $asesi->name ?? '';
        $data['nama_asesi'] = $asesi->name ?? '';
        $data['asesi_name'] = $asesi->name ?? '';
        
        $data['user.email'] = $asesi->email ?? '';
        $data['user_email'] = $asesi->email ?? '';
        $data['useremail'] = $asesi->email ?? '';
        $data['email_asesi'] = $asesi->email ?? '';
        $data['asesi_email'] = $asesi->email ?? '';
        
        // Mapping untuk skema
        $skemaNama = $jadwal->skema->nama ?? '';
        $skemaKode = $jadwal->skema->kode ?? '';
        $skemaNomor = $jadwal->skema->nomor ?? '';
        
        $data['skema.nama'] = $skemaNama;
        $data['skema_nama'] = $skemaNama;
        $data['skemanama'] = $skemaNama;
        $data['nama_skema'] = $skemaNama;
        
        $data['skema.kode'] = $skemaKode;
        $data['skema_kode'] = $skemaKode;
        $data['skemakode'] = $skemaKode;
        $data['kode_skema'] = $skemaKode;
        
        $data['skema.nomor'] = $skemaNomor;
        $data['skema_nomor'] = $skemaNomor;
        $data['skemanomor'] = $skemaNomor;
        $data['nomor_skema'] = $skemaNomor;
        
        // Mapping untuk jadwal
        $data['jadwal.tanggal_ujian'] = $jadwal->tanggal_ujian ?? '';
        $data['jadwal_tanggal_ujian'] = $jadwal->tanggal_ujian ?? '';
        $data['tanggal_ujian'] = $jadwal->tanggal_ujian ?? '';

        return $data;
    }

    /**
     * Helper: Get field value from database
     */
    private function getFieldValue($dbField, $asesi, $jadwal, $asesor = null)
    {
        $parts = explode('.', $dbField);

        if ($parts[0] === 'user') {
            return data_get($asesi, implode('.', array_slice($parts, 1)));
        } elseif ($parts[0] === 'asesor') {
            // Support for asesor fields
            if ($asesor) {
                return data_get($asesor, implode('.', array_slice($parts, 1)));
            }
            return '';
        } elseif ($parts[0] === 'skema') {
            return data_get($jadwal->skema, implode('.', array_slice($parts, 1)));
        } elseif ($parts[0] === 'jadwal') {
            return data_get($jadwal, implode('.', array_slice($parts, 1)));
        } elseif ($parts[0] === 'system') {
            if ($parts[1] === 'current_date') {
                return now()->format('d/m/Y');
            } elseif ($parts[1] === 'current_datetime') {
                return now()->format('d/m/Y H:i:s');
            }
        }

        return '';
    }

    /**
     * Helper: Insert signature images into template
     */
    private function insertSignatureImages($templateProcessor, $response)
    {
        // Get signature data from responses
        $asesiResponses = $response->asesi_responses ?? [];
        $asesorResponses = $response->asesor_responses ?? [];
        
        \Log::info('Checking signatures', [
            'asesi_response_keys' => array_keys($asesiResponses),
            'asesor_response_keys' => array_keys($asesorResponses),
        ]);
        
        // Try to insert TTD Asesi
        $ttdAsesi = $asesiResponses['ttd_digital_asesi'] ?? 
                    $asesiResponses['ttd_asesi'] ?? 
                    $asesorResponses['ttd_digital_asesi'] ?? 
                    $asesorResponses['ttd_asesi'] ?? null;
        
        if ($ttdAsesi && str_starts_with($ttdAsesi, 'data:image')) {
            \Log::info('Found TTD Asesi, inserting...');
            $this->insertSignature($templateProcessor, 'ttd_digital_asesi', $ttdAsesi);
            $this->insertSignature($templateProcessor, 'ttd_asesi', $ttdAsesi);
        } else {
            \Log::warning('TTD Asesi not found or invalid format');
        }
        
        // Try to insert TTD Asesor  
        $ttdAsesor = $asesorResponses['ttd_digital_asesor'] ?? 
                     $asesorResponses['ttd_asesor'] ?? 
                     $asesiResponses['ttd_digital_asesor'] ?? 
                     $asesiResponses['ttd_asesor'] ?? null;
        
        if ($ttdAsesor && str_starts_with($ttdAsesor, 'data:image')) {
            \Log::info('Found TTD Asesor, inserting...');
            $this->insertSignature($templateProcessor, 'ttd_digital_asesor', $ttdAsesor);
            $this->insertSignature($templateProcessor, 'ttd_asesor', $ttdAsesor);
        } else {
            \Log::warning('TTD Asesor not found or invalid format', [
                'ttd_value' => $ttdAsesor ? substr($ttdAsesor, 0, 50) : 'null'
            ]);
        }
        
        \Log::info('Signature insertion completed for FR template');
    }
    
    /**
     * Helper: Insert single signature into template
     */
    private function insertSignature($templateProcessor, $placeholder, $base64Image)
    {
        try {
            // Convert base64 to temporary file
            $imageData = explode(',', $base64Image);
            if (count($imageData) < 2) {
                \Log::warning("Invalid base64 image format for {$placeholder}");
                return;
            }
            
            $encodedImage = $imageData[1];
            $decodedImage = base64_decode($encodedImage);
            
            if ($decodedImage === false) {
                \Log::warning("Failed to decode base64 image for {$placeholder}");
                return;
            }
            
            // Create temp file
            $tempPath = storage_path('app/temp/' . uniqid('sig_') . '.png');
            
            // Ensure directory exists
            $tempDir = dirname($tempPath);
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            file_put_contents($tempPath, $decodedImage);
            
            // Try multiple placeholder formats
            $placeholders = [
                $placeholder,
                '${' . $placeholder . '}',
            ];
            
            foreach ($placeholders as $ph) {
                try {
                    // Remove ${} if present
                    $cleanPlaceholder = str_replace(['${', '}'], '', $ph);
                    
                    $templateProcessor->setImageValue(
                        $cleanPlaceholder,
                        [
                            'path' => $tempPath,
                            'width' => 150,
                            'height' => 75,
                            'ratio' => true
                        ]
                    );
                    
                    \Log::info("Successfully inserted signature image for placeholder: {$cleanPlaceholder}");
                    
                    // Don't break - try all placeholders in case template has multiple
                } catch (\Exception $e) {
                    \Log::debug("Failed to insert signature with placeholder {$ph}: " . $e->getMessage());
                }
            }
            
            // Clean up temp file
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
            
        } catch (\Exception $e) {
            \Log::error("Error inserting signature {$placeholder}: " . $e->getMessage());
        }
    }

    /**
     * Helper: Generate filename
     */
    private function generateFilename($bankSoal, $asesi)
    {
        $timestamp = now()->format('YmdHis');
        $nama = str_replace(' ', '_', $asesi->name);
        $soal = str_replace(' ', '_', $bankSoal->nama);

        return "{$soal}_{$nama}_{$timestamp}.docx";
    }
}
