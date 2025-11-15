<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\APL2;
use App\Models\Pendaftaran;
use App\Models\Response;
use App\Models\TemplateMaster;
use App\Services\TemplateGeneratorService;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Apl2Controller extends Controller
{
    use MenuTrait;

    protected $templateGenerator;

    public function __construct(TemplateGeneratorService $templateGenerator)
    {
        $this->templateGenerator = $templateGenerator;
    }

    /**
     * Display a listing of APL2 forms for asesor
     */
    public function index()
    {
        $asesor = Auth::user();
        $lists = $this->getMenuListAsesor('apl2');

        // Get skema IDs yang dimiliki asesor ini
        $skemaIds = DB::table('asesor_skema')
            ->where('asesor_id', $asesor->id)
            ->pluck('skema_id');

        // Ambil pendaftaran yang sudah mengisi APL2 dan dari skema yang dimiliki asesor
        $pendaftaran = Pendaftaran::whereNotNull('custom_variables')
            ->whereIn('skema_id', $skemaIds)
            ->with(['user', 'skema'])
            ->get();

        return view('components.pages.asesor.apl2.index', compact('lists', 'pendaftaran'));
    }

    /**
     * Show APL2 form for review by asesor
     */
    public function show($pendaftaranId)
    {
        $asesor = Auth::user();
        $lists = $this->getMenuListAsesor('apl2');

        // Get skema IDs yang dimiliki asesor ini
        $skemaIds = DB::table('asesor_skema')
            ->where('asesor_id', $asesor->id)
            ->pluck('skema_id');

        $pendaftaran = Pendaftaran::where('id', $pendaftaranId)
            ->whereIn('skema_id', $skemaIds)
            ->with(['user', 'skema'])
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan atau Anda tidak memiliki akses ke skema ini.');
        }

        // Ambil template APL2 untuk skema ini
        $template = TemplateMaster::where('tipe_template', 'APL2')
            ->where('skema_id', $pendaftaran->skema_id)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            return redirect()->back()->with('error', 'Template APL2 untuk skema ini belum tersedia.');
        }

        return view('components.pages.asesor.apl2.show', compact('lists', 'pendaftaran', 'template'));
    }

    /**
     * Update APL2 assessment by asesor
     */
    public function update(Request $request, $pendaftaranId)
    {
        $request->validate([
            'assessments' => 'required|array',
            'assessments.*' => 'required|in:BK,K', // BK = Belum Kompeten, K = Kompeten
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string',
        ]);

        $asesor = Auth::user();
        
        // Get skema IDs yang dimiliki asesor ini
        $skemaIds = DB::table('asesor_skema')
            ->where('asesor_id', $asesor->id)
            ->pluck('skema_id');

        $pendaftaran = Pendaftaran::where('id', $pendaftaranId)
            ->whereIn('skema_id', $skemaIds)
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan atau Anda tidak memiliki akses ke skema ini.');
        }

        try {
            // Simpan penilaian asesor ke custom variables
            $asesorAssessment = $pendaftaran->asesor_assessment ?? [];

            foreach ($request->assessments as $variableName => $assessment) {
                $asesorAssessment[$variableName] = [
                    'assessment' => $assessment,
                    'notes' => $request->notes[$variableName] ?? '',
                    'asesor_id' => $asesor->id,
                    'asesor_name' => $asesor->name,
                    'assessed_at' => now()->toISOString(),
                ];
            }

            $pendaftaran->asesor_assessment = $asesorAssessment;
            $pendaftaran->save();

            return redirect()->route('asesor.apl2.index')->with('success', 'Penilaian APL2 berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Add asesor digital signature to APL2
     */
    public function addSignature(Request $request)
    {
        $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftaran,id',
            'signature' => 'required|string',
        ]);

        $asesor = Auth::user();
        
        // Get skema IDs yang dimiliki asesor ini
        $skemaIds = DB::table('asesor_skema')
            ->where('asesor_id', $asesor->id)
            ->pluck('skema_id');

        $pendaftaran = Pendaftaran::where('id', $request->pendaftaran_id)
            ->whereIn('skema_id', $skemaIds)
            ->first();

        if (!$pendaftaran) {
            return response()->json(['error' => 'Pendaftaran tidak ditemukan atau Anda tidak memiliki akses ke skema ini.'], 404);
        }

        try {
            // Update semua response dengan signature asesor
            Response::where('pendaftaran_id', $pendaftaran->id)
                ->update([
                    'asesor_signature' => $request->signature,
                    'asesor_signature_timestamp' => now(),
                    'asesor_signature_ip' => $request->ip(),
                ]);

            return response()->json(['success' => 'Tanda tangan asesor berhasil ditambahkan.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Preview APL2 data for asesor
     */
    public function previewApl2Data($pendaftaranId)
    {
        $asesor = Auth::user();
        
        // Get skema IDs yang dimiliki asesor ini
        $skemaIds = DB::table('asesor_skema')
            ->where('asesor_id', $asesor->id)
            ->pluck('skema_id');

        $pendaftaran = Pendaftaran::where('id', $pendaftaranId)
            ->whereIn('skema_id', $skemaIds)
            ->with(['skema', 'user'])
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan atau Anda tidak memiliki akses ke skema ini.');
        }

        try {
            // Ambil template APL2 untuk skema ini
            $template = TemplateMaster::where('tipe_template', 'APL2')
                ->where('skema_id', $pendaftaran->skema_id)
                ->where('is_active', true)
                ->first();

            if (!$template) {
                return redirect()->back()->with('error', 'Template APL2 untuk skema ini belum tersedia.');
            }

            // Siapkan data untuk preview
            $data = $this->templateGenerator->prepareApl2TemplateData($pendaftaran, true); // true untuk asesor view

            return response()->json([
                'success' => true,
                'data' => $data,
                'pendaftaran' => [
                    'id' => $pendaftaran->id,
                    'user_name' => $pendaftaran->user->name ?? 'N/A',
                    'skema_name' => $pendaftaran->skema->nama ?? 'N/A',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat preview: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export APL2 to DOCX for asesor
     */
    public function exportDocx($pendaftaranId)
    {
        $asesor = Auth::user();
        
        // Get skema IDs yang dimiliki asesor ini
        $skemaIds = DB::table('asesor_skema')
            ->where('asesor_id', $asesor->id)
            ->pluck('skema_id');

        $pendaftaran = Pendaftaran::where('id', $pendaftaranId)
            ->whereIn('skema_id', $skemaIds)
            ->with(['skema', 'user'])
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan atau Anda tidak memiliki akses ke skema ini.');
        }

        try {
            // Generate DOCX menggunakan TemplateGeneratorService
            $result = $this->templateGenerator->generateApl2($pendaftaran, true); // true untuk asesor view

            if ($result['success']) {
                return response()->download($result['file_path'], $result['filename']);
            } else {
                return redirect()->back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }

    /**
     * Display APL2 template for asesor
     */
    public function templateIndex()
    {
        $asesor = Auth::user();
        $lists = $this->getMenuListAsesor('apl2-template');

        // Get skema IDs yang dimiliki asesor ini
        $skemaIds = DB::table('asesor_skema')
            ->where('asesor_id', $asesor->id)
            ->pluck('skema_id');

        // Ambil template APL2 yang aktif dari skema yang dimiliki asesor
        $templates = TemplateMaster::where('tipe_template', 'APL2')
            ->where('is_active', true)
            ->whereIn('skema_id', $skemaIds)
            ->with('skema')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('components.pages.asesor.apl2.template.index', compact('lists', 'templates'));
    }
}
