<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Services\EmailService;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;

class VerifikasiPendaftaranController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Detect user type for menu
        $userType = auth()->user()->user_type;
        if ($userType === 'kaprodi') {
            $lists = $this->getMenuListKaprodi('verifikasi-pendaftaran');
            $viewPath = 'components.pages.kaprodi.verifikasi-pendaftaran.list';
        } else {
            $lists = $this->getMenuListAdmin('verifikasi-pendaftaran');
            $viewPath = 'components.pages.admin.verifikasi-pendaftaran.list';
        }

        // Load user with file fields for displaying uploaded documents
        // Only show pendaftaran with status 5 (Menunggu Verifikasi Dokumen - after distribution)
        $query = Pendaftaran::with([
            'jadwal',
            'jadwal.skema',
            'jadwal.tuk',
            'user', // Load all user fields including uploaded files
            'skema',
            'pendaftaranUjikom'
        ])->where('status', 5);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('pendaftaran.created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('pendaftaran.created_at', '<=', $request->end_date);
        }

        if ($request->filled('skema_id')) {
            $query->where('pendaftaran.skema_id', $request->skema_id);
        }

        // Sort by created_at descending (latest first), then by id descending
        $verfikasiPendaftaran = $query->orderBy('pendaftaran.created_at', 'desc')
            ->orderBy('pendaftaran.id', 'desc')
            ->get();

        // Get all skema for filter dropdown
        $skemas = \App\Models\Skema::orderBy('nama', 'asc')->get();

        return view($viewPath, compact('lists', 'verfikasiPendaftaran', 'skemas'));
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        $request->validate([
            'status' => 'required|in:2,6',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $pendaftaran = Pendaftaran::findOrFail($id);
        
        // Status 6: Approve (Menunggu Verifikasi Kelayakan Asesor)
        // Status 2: Reject (Tidak Lolos Verifikasi Dokumen)
        $pendaftaran->status = $request->status;

        // Jika status adalah 2 (ditolak), simpan keterangan
        if ($request->status == 2) {
            $pendaftaran->keterangan = $request->keterangan;
            
            // Kirim email notifikasi jika ditolak
            $emailService = new EmailService();
            $emailService->sendPendaftaranDitolakNotification($pendaftaran);
        } else {
            // Jika status 6 (disetujui), hapus keterangan
            $pendaftaran->keterangan = null;
        }

        $pendaftaran->save();

        // Redirect based on user type
        $userType = auth()->user()->user_type;
        if ($userType === 'kaprodi') {
            return redirect()->route('kaprodi.verifikasi-pendaftaran.index')
                ->with('success', "Status pendaftaran berhasil diubah");
        } else {
            return redirect()->route('admin.verifikasi-pendaftaran.index')
                ->with('success', "Status pendaftaran berhasil diubah");
        }
    }

    /**
     * Download APL 1 DOCX (for Kaprodi/Admin)
     */
    public function previewApl1($pendaftaranId)
    {
        try {
            $pendaftaran = Pendaftaran::with(['user', 'skema', 'jadwal.tuk'])->findOrFail($pendaftaranId);
            
            // Generate DOCX
            $templateGenerator = new \App\Services\TemplateGeneratorService();
            if (!$templateGenerator->checkTemplateExists('APL1', $pendaftaran->skema_id)) {
                return redirect()->back()->with('error', 'Template APL 1 untuk skema ini belum tersedia.');
            }
            
            $result = $templateGenerator->generateApl1($pendaftaran, []);
            
            if (!$result['success']) {
                return redirect()->back()->with('error', 'Gagal generate APL 1: ' . $result['error']);
            }
            
            // Download DOCX langsung (format asli, tidak di-convert)
            return response()->download(
                storage_path('app/public/' . $result['file_path']),
                'APL1_' . $pendaftaran->user->name . '.docx'
            );
            
        } catch (\Exception $e) {
            \Log::error('Download APL1 Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download APL 2 DOCX (for Kaprodi/Admin)
     */
    public function previewApl2($pendaftaranId)
    {
        try {
            $pendaftaran = Pendaftaran::with(['user', 'skema'])->findOrFail($pendaftaranId);
            
            // Cek apakah APL2 sudah diisi
            if (empty($pendaftaran->custom_variables)) {
                return redirect()->back()->with('error', 'APL2 belum diisi oleh asesi.');
            }
            
            // Generate DOCX
            $templateGenerator = new \App\Services\TemplateGeneratorService();
            $result = $templateGenerator->generateApl2($pendaftaran, false);
            
            if (!$result['success']) {
                return redirect()->back()->with('error', 'Gagal generate APL 2: ' . ($result['message'] ?? 'Unknown error'));
            }
            
            // Download DOCX langsung (format asli, tidak di-convert)
            return response()->download(
                $result['file_path'],
                'APL2_' . $pendaftaran->user->name . '.docx'
            );
            
        } catch (\Exception $e) {
            \Log::error('Download APL2 Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
