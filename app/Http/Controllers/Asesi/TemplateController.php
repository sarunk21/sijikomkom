<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Services\TemplateGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    protected $templateGenerator;

    public function __construct(TemplateGeneratorService $templateGenerator)
    {
        $this->templateGenerator = $templateGenerator;
    }

    /**
     * Generate APL 1 untuk pendaftaran yang sedang menunggu ujian
     */
    public function generateApl1(Request $request, $pendaftaranId)
    {
        try {
            \Log::info('Generate APL1 - Pendaftaran ID: ' . $pendaftaranId . ', User ID: ' . Auth::id());

            // Cek apakah pendaftaran milik user yang login
            $pendaftaran = Pendaftaran::where('id', $pendaftaranId)
                ->where('user_id', Auth::id())
                ->where('status', 4) // Status menunggu ujian
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
                ->where('status', 4)
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
