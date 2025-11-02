<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Services\TemplateGeneratorService;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    use MenuTrait;
    
    protected $templateGenerator;

    public function __construct(TemplateGeneratorService $templateGenerator)
    {
        $this->templateGenerator = $templateGenerator;
    }

    /**
     * Show halaman form APL 1 dengan custom variables
     */
    public function showApl1Form($pendaftaranId)
    {
        try {
            $pendaftaran = Pendaftaran::where('id', $pendaftaranId)
                ->where('user_id', Auth::id())
                ->whereIn('status', [3, 4, 5])
                ->with(['skema', 'user', 'jadwal.tuk'])
                ->first();

            if (!$pendaftaran) {
                return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan atau tidak dalam status yang tepat.');
            }

            // Get template untuk melihat custom variables
            $template = \App\Models\TemplateMaster::active()
                ->byType('APL1')
                ->where('skema_id', $pendaftaran->skema_id)
                ->first();

            if (!$template) {
                return redirect()->back()->with('error', 'Template APL 1 untuk skema "' . $pendaftaran->skema->nama . '" belum tersedia. Silakan hubungi administrator.');
            }

            // Filter custom variables (yang tidak ada di database fields)
            $databaseFields = [
                'user.name', 'user.email', 'user.telephone', 'user.alamat', 'user.nik', 'user.nim',
                'user.tempat_lahir', 'user.tanggal_lahir', 'user.jenis_kelamin', 'user.kebangsaan',
                'user.pekerjaan', 'user.pendidikan', 'user.jurusan',
                'skema.nama', 'skema.kode', 'skema.kategori', 'skema.bidang',
                'jadwal.tanggal_ujian', 'jadwal.waktu_mulai', 'jadwal.waktu_selesai', 'jadwal.tuk.nama',
                'system.tanggal_generate', 'system.waktu_generate', 'system.nomor_pendaftaran',
                'ttd_digital'
            ];

            $customVariables = [];
            $existingData = [];
            $dynamicFields = [];

            if ($template->variables) {
                foreach ($template->variables as $variable) {
                    if (!in_array($variable, $databaseFields)) {
                        // Cek apakah data sudah ada di custom_variables pendaftaran
                        if ($pendaftaran->custom_variables && isset($pendaftaran->custom_variables[$variable])) {
                            $existingData[$variable] = $pendaftaran->custom_variables[$variable];
                        } else {
                            $customVariables[] = $variable;
                        }
                    }
                }
            }

            // Handle dynamic field configurations
            if ($template->field_configurations) {
                foreach ($template->field_configurations as $fieldConfig) {
                    $fieldName = $fieldConfig['name'];
                    
                    // Cek apakah field sudah diisi di custom_variables
                    if ($pendaftaran->custom_variables && isset($pendaftaran->custom_variables[$fieldName])) {
                        $existingData[$fieldName] = $pendaftaran->custom_variables[$fieldName];
                    } else {
                        $dynamicFields[] = $fieldConfig;
                    }
                }
            }

            // Cek apakah semua custom variables sudah terisi DAN TTD sudah ada
            $allCustomVariablesFilled = empty($customVariables) && empty($dynamicFields) && !empty($pendaftaran->ttd_asesi_path);

            $lists = $this->getMenuListAsesi('sertifikasi');
            $activeMenu = 'sertifikasi';

            return view('components.pages.asesi.apl1.form', compact(
                'pendaftaran',
                'template',
                'customVariables',
                'existingData',
                'dynamicFields',
                'allCustomVariablesFilled',
                'lists',
                'activeMenu'
            ));

        } catch (\Exception $e) {
            \Log::error('TemplateController showApl1Form error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Store custom variables untuk APL 1
     */
    public function storeApl1CustomData(Request $request, $pendaftaranId)
    {
        $pendaftaran = Pendaftaran::where('id', $pendaftaranId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan atau bukan milik Anda.');
        }

        if (!in_array($pendaftaran->status, [3, 4, 5])) {
            return redirect()->back()->with('error', 'Pendaftaran belum dalam status yang tepat.');
        }

        // Validasi: signature required hanya jika belum ada TTD sebelumnya
        $rules = [
            'custom_variables' => 'nullable|array',
            'custom_variables.*' => 'nullable|string|max:255',
            'dynamic_fields' => 'nullable|array',
            'dynamic_fields.*' => 'nullable',
        ];

        // Jika belum ada TTD, maka signature_data wajib diisi
        if (empty($pendaftaran->ttd_asesi_path)) {
            $rules['signature_data'] = 'required|string';
        } else {
            $rules['signature_data'] = 'nullable|string';
        }

        $request->validate($rules);

        try {
            // Ambil custom variables yang sudah ada
            $existingCustomVariables = $pendaftaran->custom_variables ?? [];
            
            $customVariables = $existingCustomVariables;
            
            if ($request->custom_variables) {
                foreach ($request->custom_variables as $key => $value) {
                    if (!empty(trim($value))) {
                        $customVariables[$key] = trim($value);
                    }
                }
            }

            // Merge dynamic fields
            if ($request->dynamic_fields) {
                foreach ($request->dynamic_fields as $key => $value) {
                    if (is_array($value)) {
                        $customVariables[$key] = implode(', ', $value);
                    } else {
                        $customVariables[$key] = $value;
                    }
                }
            }

            // Simpan signature digital
            $ttdAsesiPath = $pendaftaran->ttd_asesi_path; // Keep existing if not updating
            if ($request->signature_data) {
                // Hapus TTD lama jika ada
                if ($pendaftaran->ttd_asesi_path && Storage::disk('public')->exists($pendaftaran->ttd_asesi_path)) {
                    Storage::disk('public')->delete($pendaftaran->ttd_asesi_path);
                }

                // Simpan signature digital sebagai file PNG
                $signatureData = $request->signature_data;
                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));

                $ttdFileName = 'ttd_asesi_' . $pendaftaranId . '_' . time() . '.png';
                $ttdAsesiPath = 'ttd_asesi/' . $ttdFileName;

                Storage::disk('public')->put($ttdAsesiPath, $image);
            }

            // Update pendaftaran
            $pendaftaran->update([
                'custom_variables' => $customVariables,
                'ttd_asesi_path' => $ttdAsesiPath,
            ]);

            return redirect()->route('asesi.template.apl1-form', $pendaftaranId)
                ->with('success', 'Data berhasil disimpan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download/Generate APL 1 untuk pendaftaran yang sedang menunggu ujian
     */
    public function downloadApl1(Request $request, $pendaftaranId)
    {
        try {
            \Log::info('Generate APL1 - Pendaftaran ID: ' . $pendaftaranId . ', User ID: ' . Auth::id());

            // Cek apakah pendaftaran milik user yang login
            $pendaftaran = Pendaftaran::where('id', $pendaftaranId)
                ->where('user_id', Auth::id())
                ->whereIn('status', [3, 4, 5]) // Status yang bisa generate APL1
                ->with(['user', 'skema', 'jadwal.tuk'])
                ->first();

            if (!$pendaftaran) {
                \Log::warning('Pendaftaran tidak ditemukan untuk generate - ID: ' . $pendaftaranId . ', User: ' . Auth::id());
                return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan atau tidak dalam status yang tepat untuk generate APL 1.');
            }

            \Log::info('Pendaftaran found for generate: ' . $pendaftaran->id);

            // Cek apakah sudah ada template APL 1 untuk skema ini
            if (!$this->templateGenerator->checkTemplateExists('APL1', $pendaftaran->skema_id)) {
                \Log::warning('Template APL1 tidak ditemukan untuk skema: ' . $pendaftaran->skema_id);
                return redirect()->back()->with('error', 'Template APL 1 untuk skema ini belum tersedia. Silakan hubungi administrator.');
            }

            \Log::info('Template APL1 exists for skema: ' . $pendaftaran->skema_id);

            // Generate APL 1
            $result = $this->templateGenerator->generateApl1($pendaftaran, $request->all());

            if ($result['success']) {
                \Log::info('APL1 generated successfully: ' . $result['file_path']);
                return response()->download(
                    storage_path('app/public/' . $result['file_path']),
                    $result['file_name']
                );
            } else {
                \Log::error('APL1 generation failed: ' . $result['error']);
                return redirect()->back()->with('error', 'Gagal generate APL 1: ' . $result['error']);
            }

        } catch (\Exception $e) {
            \Log::error('Generate APL1 Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Preview data yang akan digunakan untuk generate APL 1
     */
    public function previewApl1Data($pendaftaranId)
    {
        try {
            \Log::info('Preview APL1 Data - Pendaftaran ID: ' . $pendaftaranId . ', User ID: ' . Auth::id());

            $pendaftaran = Pendaftaran::where('id', $pendaftaranId)
                ->where('user_id', Auth::id())
                ->whereIn('status', [3, 4, 5])
                ->with(['user', 'skema', 'jadwal.tuk'])
                ->first();

            if (!$pendaftaran) {
                \Log::warning('Pendaftaran tidak ditemukan - ID: ' . $pendaftaranId . ', User: ' . Auth::id());
                return response()->json(['error' => 'Pendaftaran tidak ditemukan'], 404);
            }

            \Log::info('Pendaftaran found: ' . $pendaftaran->id);

            // Get template data
            $data = $this->templateGenerator->prepareTemplateData($pendaftaran);

            \Log::info('Template data prepared successfully');

            return response()->json([
                'success' => true,
                'data' => $data,
                'pendaftaran' => $pendaftaran
            ]);

        } catch (\Exception $e) {
            \Log::error('Preview APL1 Data Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
