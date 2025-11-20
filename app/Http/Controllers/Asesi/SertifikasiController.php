<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\TemplateMaster;
use App\Services\TemplateGeneratorService;
use Illuminate\Support\Facades\Storage;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SertifikasiController extends Controller
{
    use MenuTrait;

    protected $templateGenerator;

    public function __construct(TemplateGeneratorService $templateGenerator)
    {
        $this->templateGenerator = $templateGenerator;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $asesi = Auth::user();
        $pendaftaran = Pendaftaran::where('user_id', $asesi->id)
            ->with(['jadwal', 'jadwal.skema', 'jadwal.tuk'])
            ->orderBy('created_at', 'desc')
            ->get();
        $lists = $this->getMenuListAsesi('sertifikasi');

        return view('components.pages.asesi.sertifikasi.list', compact('pendaftaran', 'lists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Store APL2 data
     */
    public function storeApl2(Request $request, string $id)
    {
        $asesi = Auth::user();
        $pendaftaran = Pendaftaran::with('jadwal')
            ->where('id', $id)
            ->where('user_id', $asesi->id)
            ->first();

        if (!$pendaftaran) {
            return redirect()->route('asesi.sertifikasi.index')->with('error', 'Pendaftaran tidak ditemukan.');
        }

        // TEMPORARY DISABLED - Cek apakah ujian sudah dimulai
        // Validasi ini dinonaktifkan sementara untuk debugging
        // if ($pendaftaran->jadwal) {
        //     $tanggalUjian = $pendaftaran->jadwal->tanggal_ujian;
        //     $waktuMulai = $pendaftaran->jadwal->waktu_mulai;

        //     if ($tanggalUjian && $waktuMulai) {
        //         try {
        //             $waktuMulaiUjian = \Carbon\Carbon::parse($tanggalUjian . ' ' . $waktuMulai);

        //             if (now()->greaterThanOrEqualTo($waktuMulaiUjian)) {
        //                 return redirect()->route('asesi.sertifikasi.index')->with('error', 'APL2 tidak dapat diedit karena ujian sudah dimulai.');
        //             }
        //         } catch (\Exception $e) {
        //             \Log::warning('Failed to parse exam time: ' . $e->getMessage());
        //         }
        //     }
        // }

        // Cek apakah APL2 sudah direview asesor
        $isReviewedByAsesor = !empty($pendaftaran->asesor_assessment);

        if ($isReviewedByAsesor) {
            return redirect()->route('asesi.sertifikasi.index')->with('error', 'APL2 sudah direview oleh asesor dan tidak dapat diedit lagi.');
        }

        $rules = [];

        // Validasi custom variables dari template APL2
        if ($request->has('custom_variables')) {
            foreach ($request->input('custom_variables', []) as $variable_name => $value) {
                $rules['custom_variables.' . $variable_name] = 'nullable|string';
            }
        }

        // Validasi signature data: required hanya jika belum ada TTD sebelumnya
        if (empty($pendaftaran->ttd_asesi_path)) {
            $rules['signature_data'] = 'required|string';
        } else {
            $rules['signature_data'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        try {
            // Simpan custom variables ke pendaftaran
            if (isset($validated['custom_variables'])) {
                $customVariables = $pendaftaran->custom_variables ?? [];

                foreach ($validated['custom_variables'] as $variable_name => $value) {
                    $customVariables[$variable_name] = $value;
                }

                $pendaftaran->custom_variables = $customVariables;
            }

            // Simpan signature digital jika ada
            if (isset($validated['signature_data']) && !empty($validated['signature_data'])) {
                // Hapus TTD lama jika ada
                if ($pendaftaran->ttd_asesi_path && Storage::disk('public')->exists($pendaftaran->ttd_asesi_path)) {
                    Storage::disk('public')->delete($pendaftaran->ttd_asesi_path);
                }

                // Simpan signature digital sebagai file PNG
                $signatureData = $validated['signature_data'];
                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));

                $ttdFileName = 'ttd_asesi_' . $id . '_' . time() . '.png';
                $ttdAsesiPath = 'ttd_asesi/' . $ttdFileName;

                Storage::disk('public')->put($ttdAsesiPath, $image);
                $pendaftaran->ttd_asesi_path = $ttdAsesiPath;
            }
            // Jika tidak ada signature_data baru, keep TTD yang lama

            $pendaftaran->save();

            return redirect()->route('asesi.sertifikasi.apl2', $id)->with('success', 'Data APL2 berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $asesi = Auth::user();
        $pendaftaran = Pendaftaran::where('id', $id)
            ->where('user_id', $asesi->id)
            ->with(['skema', 'user', 'jadwal.tuk'])
            ->first();

        if (!$pendaftaran) {
            return redirect()->route('asesi.sertifikasi.index')->with('error', 'Pendaftaran tidak ditemukan.');
        }

        // Cek apakah APL2 sudah direview asesor
        $isReviewedByAsesor = !empty($pendaftaran->asesor_assessment);

        if ($isReviewedByAsesor) {
            return redirect()->route('asesi.sertifikasi.index')->with('error', 'APL2 sudah direview oleh asesor dan tidak dapat diedit lagi.');
        }

        // Ambil template APL2 untuk skema ini
        $template = TemplateMaster::where('tipe_template', 'APL2')
            ->where('skema_id', $pendaftaran->skema_id)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            return redirect()->route('asesi.sertifikasi.index')->with('error', 'Template APL2 untuk skema ini belum tersedia.');
        }

        // Cek apakah semua custom variables sudah terisi
        $allCustomVariablesFilled = false;
        $customVariablesNeeded = [];
        
        if ($template->custom_variables && count($template->custom_variables) > 0) {
            foreach ($template->custom_variables as $customVar) {
                $fieldName = $customVar['name'];
                
                // Skip signature_pad karena sudah ditangani di section terpisah
                if (isset($customVar['type']) && $customVar['type'] === 'signature_pad') {
                    continue;
                }
                
                // Cek apakah field sudah diisi
                if (!$pendaftaran->custom_variables || !isset($pendaftaran->custom_variables[$fieldName])) {
                    $customVariablesNeeded[] = $customVar;
                }
            }
            
            // Jika tidak ada yang perlu diisi lagi DAN TTD sudah ada, berarti sudah lengkap
            $allCustomVariablesFilled = empty($customVariablesNeeded) && !empty($pendaftaran->ttd_asesi_path);
        } else {
            // Jika tidak ada custom variables, cek TTD saja
            $allCustomVariablesFilled = !empty($pendaftaran->ttd_asesi_path);
        }

        $lists = $this->getMenuListAsesi('sertifikasi');

        return view('components.pages.asesi.sertifikasi.apl2-form', compact('pendaftaran', 'lists', 'template', 'allCustomVariablesFilled', 'customVariablesNeeded'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Generate APL2 document for asesi
     */
    public function generateApl2(string $id)
    {
        $asesi = Auth::user();
        $pendaftaran = Pendaftaran::where('id', $id)
            ->where('user_id', $asesi->id)
            ->with(['skema', 'user'])
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan.');
        }

        // Cek apakah APL2 sudah diisi
        if (empty($pendaftaran->custom_variables)) {
            return redirect()->back()->with('error', 'APL2 belum diisi. Silakan isi APL2 terlebih dahulu.');
        }

        try {
            // Generate DOCX menggunakan TemplateGeneratorService
            $result = $this->templateGenerator->generateApl2($pendaftaran, false); // false untuk asesi view

            if ($result['success']) {
                return response()->download($result['file_path'], $result['filename']);
            } else {
                return redirect()->back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat generate: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
