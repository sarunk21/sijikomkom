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
        // Jika Bank Soal target nya 'asesor', maka semua field masuk ke asesorFields
        // Jika target nya 'asesi', maka:
        //   - Field role 'asesi' dan 'both' -> asesiFields (untuk ditampilkan jawaban asesi)
        //   - Field role 'asesor' -> asesorFields (untuk diisi asesor)

        if ($bankSoal->target === 'asesor') {
            // Untuk formulir asesor, semua field diisi oleh asesor
            $asesiFields = collect([]);
            $asesorFields = collect($bankSoal->field_configurations ?? []);
        } else {
            // Untuk formulir asesi, pisahkan field berdasarkan role
            $asesiFields = collect($bankSoal->field_configurations ?? [])
                ->filter(function ($field) {
                    return in_array($field['role'] ?? 'asesi', ['asesi', 'both']);
                });

            $asesorFields = collect($bankSoal->field_configurations ?? [])
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
            'all_data' => $data
        ]);

        // Replace variables
        foreach ($data as $key => $value) {
            $templateProcessor->setValue($key, $value);
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

        // Database fields from variables (auto-fill dari DB)
        if ($bankSoal->variables && is_array($bankSoal->variables)) {
            foreach ($bankSoal->variables as $variable) {
                $data[$variable] = $this->getFieldValue($variable, $asesi, $jadwal);
            }
        }

        // Database fields from field_mappings (custom mapping)
        if ($bankSoal->field_mappings) {
            foreach ($bankSoal->field_mappings as $variable => $dbField) {
                $data[$variable] = $this->getFieldValue($dbField, $asesi, $jadwal);
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

        return $data;
    }

    /**
     * Helper: Get field value from database
     */
    private function getFieldValue($dbField, $asesi, $jadwal)
    {
        $parts = explode('.', $dbField);

        if ($parts[0] === 'user') {
            return data_get($asesi, implode('.', array_slice($parts, 1)));
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
