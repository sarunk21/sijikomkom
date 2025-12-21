<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\PendaftaranUjikom;
use App\Models\TemplateMaster;
use App\Services\TemplateGeneratorService;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    use MenuTrait;

    protected $templateGenerator;

    public function __construct(TemplateGeneratorService $templateGenerator)
    {
        $this->templateGenerator = $templateGenerator;
    }

    /**
     * Display list of jadwal for review with filters
     */
    public function index(Request $request)
    {
        $lists = $this->getMenuListAsesor('review');

        // Pending confirmations - group by jadwal (hanya jadwal yang belum dimulai)
        $pendingConfirmations = PendaftaranUjikom::where('asesor_id', Auth::id())
            ->where('asesor_confirmed', false)
            ->whereHas('jadwal', function($query) {
                $query->where('status', 1) // Hanya jadwal aktif yang belum dimulai
                    ->where('tanggal_ujian', '>', now());
            })
            ->with(['jadwal', 'jadwal.skema', 'jadwal.tuk'])
            ->get()
            ->groupBy('jadwal_id')
            ->map(function($items) {
                $first = $items->first();
                return [
                    'jadwal_id' => $first->jadwal_id,
                    'jadwal' => $first->jadwal,
                    'jumlah_asesi' => $items->count(),
                    'pendaftaran_ujikom_ids' => $items->pluck('id')->toArray(),
                    'ditugaskan_sejak' => $items->min('created_at')
                ];
            })
            ->sortBy('jadwal.tanggal_ujian')
            ->values();

        // Query 1: Jadwal dengan asesi yang butuh verifikasi kelayakan (status 6)
        $queryKelayakan = PendaftaranUjikom::where('asesor_id', Auth::id())
            ->whereHas('pendaftaran', function($q) {
                $q->where('status', 6); // Menunggu Verifikasi Kelayakan
            })
            ->with(['jadwal.skema', 'jadwal.tuk']);

        // Query 2: Jadwal yang sudah confirmed atau sudah dimulai (untuk review biasa)
        $queryReview = PendaftaranUjikom::where('asesor_id', Auth::id())
            ->where(function($q) {
                $q->where('asesor_confirmed', true)
                  ->orWhereHas('jadwal', function($subQuery) {
                      $subQuery->where('status', '>=', 2); // Status 2 = Pendaftaran, 3 = Berlangsung, 4 = Selesai
                  });
            })
            ->with(['jadwal.skema', 'jadwal.tuk']);

        // Apply filters untuk kedua query
        if ($request->filled('tanggal_dari')) {
            $queryKelayakan->whereHas('jadwal', function($q) use ($request) {
                $q->where('tanggal_ujian', '>=', $request->tanggal_dari);
            });
            $queryReview->whereHas('jadwal', function($q) use ($request) {
                $q->where('tanggal_ujian', '>=', $request->tanggal_dari);
            });
        }

        if ($request->filled('tanggal_sampai')) {
            $queryKelayakan->whereHas('jadwal', function($q) use ($request) {
                $q->where('tanggal_ujian', '<=', $request->tanggal_sampai);
            });
            $queryReview->whereHas('jadwal', function($q) use ($request) {
                $q->where('tanggal_ujian', '<=', $request->tanggal_sampai);
            });
        }

        if ($request->filled('skema_id')) {
            $queryKelayakan->whereHas('jadwal.skema', function($q) use ($request) {
                $q->where('id', $request->skema_id);
            });
            $queryReview->whereHas('jadwal.skema', function($q) use ($request) {
                $q->where('id', $request->skema_id);
            });
        }

        // Get distinct jadwal untuk kelayakan
        $jadwalKelayakan = $queryKelayakan->select('jadwal_id')
            ->distinct()
            ->get()
            ->map(function($item) {
                return PendaftaranUjikom::where('jadwal_id', $item->jadwal_id)
                    ->where('asesor_id', Auth::id())
                    ->with(['jadwal.skema', 'jadwal.tuk'])
                    ->first();
            })
            ->filter()
            ->sortByDesc('jadwal_id');

        // Get distinct jadwal untuk review
        $jadwalList = $queryReview->select('jadwal_id')
            ->distinct()
            ->get()
            ->map(function($item) {
                return PendaftaranUjikom::where('jadwal_id', $item->jadwal_id)
                    ->where('asesor_id', Auth::id())
                    ->with(['jadwal.skema', 'jadwal.tuk'])
                    ->first();
            })
            ->filter()
            ->sortByDesc('jadwal_id');

        // Get all skema for filter
        $skemas = DB::table('skema')
            ->whereIn('id', function($query) {
                $query->select('skema_id')
                    ->from('jadwal')
                    ->whereIn('id', function($q) {
                        $q->select('jadwal_id')
                            ->from('pendaftaran_ujikom')
                            ->where('asesor_id', Auth::id());
                    });
            })
            ->get();

        return view('components.pages.asesor.review.index', compact('lists', 'jadwalList', 'jadwalKelayakan', 'skemas', 'pendingConfirmations'));
    }

    /**
     * Show list asesi untuk jadwal tertentu
     */
    public function showAsesi($jadwalId)
    {
        $lists = $this->getMenuListAsesor('review');

        // Validate jadwal
        $jadwal = PendaftaranUjikom::where('jadwal_id', $jadwalId)
            ->where('asesor_id', Auth::id())
            ->with(['jadwal.skema', 'jadwal.tuk'])
            ->first();

        if (!$jadwal) {
            return redirect()->route('asesor.review.index')
                ->with('error', 'Jadwal tidak ditemukan');
        }

        // Get asesi list with pendaftaran data
        $asesiList = PendaftaranUjikom::where('jadwal_id', $jadwalId)
            ->where('asesor_id', Auth::id())
            ->with(['asesi', 'pendaftaran'])
            ->get()
            ->map(function($item) {
                // Check if APL1 and APL2 are filled
                $pendaftaran = Pendaftaran::where('user_id', $item->asesi_id)
                    ->where('jadwal_id', $item->jadwal_id)
                    ->first();

                $item->has_apl1 = $pendaftaran && !empty($pendaftaran->custom_variables);
                $item->has_apl2 = $pendaftaran && !empty($pendaftaran->custom_variables);
                $item->pendaftaran_id = $pendaftaran->id ?? null;
                $item->pendaftaran_status = $pendaftaran->status ?? null;
                $item->kelayakan_status = $pendaftaran->kelayakan_status ?? 0;
                $item->kelayakan_catatan = $pendaftaran->kelayakan_catatan ?? null;

                return $item;
            });

        return view('components.pages.asesor.review.asesi-list', compact('lists', 'jadwal', 'asesiList'));
    }

    /**
     * Show review form for APL1
     */
    public function reviewApl1($pendaftaranId)
    {
        $lists = $this->getMenuListAsesor('review');

        $pendaftaran = Pendaftaran::with(['user', 'skema', 'jadwal'])
            ->find($pendaftaranId);

        if (!$pendaftaran) {
            return redirect()->route('asesor.review.index')
                ->with('error', 'Pendaftaran tidak ditemukan.');
        }

        // Verify asesor is assigned
        $assigned = PendaftaranUjikom::where('jadwal_id', $pendaftaran->jadwal_id)
            ->where('asesi_id', $pendaftaran->user_id)
            ->where('asesor_id', Auth::id())
            ->exists();

        if (!$assigned) {
            return redirect()->route('asesor.review.index')
                ->with('error', 'Anda tidak berhak mereview asesi ini.');
        }

        // Get template APL1
        $template = TemplateMaster::where('tipe_template', 'APL1')
            ->where('skema_id', $pendaftaran->skema_id)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            return redirect()->back()
                ->with('error', 'Template APL1 untuk skema ini belum tersedia.');
        }

        // Get Bank Soal APL1 for this skema (if exists)
        $bankSoal = \App\Models\BankSoal::where('skema_id', $pendaftaran->skema_id)
            ->where('is_active', true)
            ->first();

        return view('components.pages.asesor.review.apl1', compact('lists', 'pendaftaran', 'template', 'bankSoal'));
    }

    /**
     * Store review for APL1
     */
    public function storeReviewApl1(Request $request, $pendaftaranId)
    {
        $pendaftaran = Pendaftaran::find($pendaftaranId);

        if (!$pendaftaran) {
            return redirect()->route('asesor.review.index')
                ->with('error', 'Pendaftaran tidak ditemukan.');
        }

        try {
            // Save asesor's custom variables for APL1
            if ($request->has('asesor_variables')) {
                $asesorData = $pendaftaran->asesor_data ?? [];

                foreach ($request->asesor_variables as $key => $value) {
                    $asesorData[$key] = $value;
                }

                $pendaftaran->asesor_data = $asesorData;
            }

            // Save signature if provided
            if ($request->filled('signature_data')) {
                $pendaftaran->ttd_asesor_path = $this->saveSignature($request->signature_data, 'asesor', $pendaftaranId);
            }

            $pendaftaran->save();

            return redirect()->route('asesor.review.show-asesi', $pendaftaran->jadwal_id)
                ->with('success', 'Review APL1 berhasil disimpan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate APL1 for asesor view
     */
    public function generateApl1($pendaftaranId)
    {
        $pendaftaran = Pendaftaran::with(['skema', 'user'])->find($pendaftaranId);

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan.');
        }

        try {
            $result = $this->templateGenerator->generateApl1($pendaftaran, []); // empty array for default behavior

            if ($result['success']) {
                $filePath = storage_path('app/public/' . $result['file_path']);
                return response()->download($filePath, $result['file_name']);
            } else {
                return redirect()->back()->with('error', $result['error'] ?? 'Terjadi kesalahan saat generate');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat generate: ' . $e->getMessage());
        }
    }

    /**
     * Show review form for APL2
     */
    public function reviewApl2($pendaftaranId)
    {
        $lists = $this->getMenuListAsesor('review');

        $pendaftaran = Pendaftaran::with(['user', 'skema', 'jadwal'])
            ->find($pendaftaranId);

        if (!$pendaftaran) {
            return redirect()->route('asesor.review.index')
                ->with('error', 'Pendaftaran tidak ditemukan.');
        }

        // Verify asesor is assigned
        $assigned = PendaftaranUjikom::where('jadwal_id', $pendaftaran->jadwal_id)
            ->where('asesi_id', $pendaftaran->user_id)
            ->where('asesor_id', Auth::id())
            ->exists();

        if (!$assigned) {
            return redirect()->route('asesor.review.index')
                ->with('error', 'Anda tidak berhak mereview asesi ini.');
        }

        // Get template APL2
        $template = TemplateMaster::where('tipe_template', 'APL2')
            ->where('skema_id', $pendaftaran->skema_id)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            return redirect()->back()
                ->with('error', 'Template APL2 untuk skema ini belum tersedia.');
        }

        // Get Bank Soal APL2 for this skema
        $bankSoal = \App\Models\BankSoal::where('skema_id', $pendaftaran->skema_id)
            ->where('is_active', true)
            ->first();

        return view('components.pages.asesor.review.apl2', compact('lists', 'pendaftaran', 'template', 'bankSoal'));
    }

    /**
     * Store review for APL2
     */
    public function storeReviewApl2(Request $request, $pendaftaranId)
    {
        $pendaftaran = Pendaftaran::find($pendaftaranId);

        if (!$pendaftaran) {
            return redirect()->route('asesor.review.index')
                ->with('error', 'Pendaftaran tidak ditemukan.');
        }

        try {
            // Save asesor's custom variables (for fields with role = 'asesor' or 'both')
            if ($request->has('asesor_variables')) {
                $asesorData = $pendaftaran->asesor_data ?? [];

                foreach ($request->asesor_variables as $key => $value) {
                    $asesorData[$key] = $value;
                }

                $pendaftaran->asesor_data = $asesorData;
            }

            // Save assessments (BK/K ratings)
            if ($request->has('assessments')) {
                $asesorAssessment = $pendaftaran->asesor_assessment ?? [];

                foreach ($request->assessments as $variableName => $assessment) {
                    $asesorAssessment[$variableName] = [
                        'assessment' => $assessment,
                        'notes' => $request->notes[$variableName] ?? '',
                        'asesor_id' => Auth::id(),
                        'asesor_name' => Auth::user()->name,
                        'assessed_at' => now()->toISOString(),
                    ];
                }

                $pendaftaran->asesor_assessment = $asesorAssessment;
            }

            // Save asesor signature if provided
            if ($request->filled('signature_data')) {
                $pendaftaran->ttd_asesor_path = $this->saveSignature($request->signature_data, 'asesor', $pendaftaranId);
            }

            $pendaftaran->save();

            return redirect()->route('asesor.review.show-asesi', $pendaftaran->jadwal_id)
                ->with('success', 'Review APL2 berhasil disimpan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate APL2 for asesor view
     */
    public function generateApl2($pendaftaranId)
    {
        $pendaftaran = Pendaftaran::with(['skema', 'user'])->find($pendaftaranId);

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan.');
        }

        try {
            $result = $this->templateGenerator->generateApl2($pendaftaran, true); // true = asesor view

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
     * Approve kelayakan (quick action from asesi list)
     */
    public function approveKelayakan($pendaftaranId)
    {
        try {
            DB::beginTransaction();

            $pendaftaran = Pendaftaran::with(['user', 'jadwal'])->findOrFail($pendaftaranId);

            // Cek apakah pendaftaran dalam status yang tepat (status 6: Menunggu Verifikasi Kelayakan)
            if ($pendaftaran->status != 6) {
                return redirect()->back()->with('error', 'Pendaftaran tidak dalam status yang tepat untuk verifikasi kelayakan.');
            }

            // Update status ke Menunggu Pembayaran
            $pendaftaran->update([
                'status' => 8, // Menunggu Pembayaran
                'kelayakan_status' => 1, // Layak
                'kelayakan_verified_at' => now(),
                'kelayakan_verified_by' => Auth::id(),
            ]);

            // Create Pembayaran record
            \App\Models\Pembayaran::create([
                'user_id' => $pendaftaran->user_id,
                'jadwal_id' => $pendaftaran->jadwal_id,
                'status' => 1, // Belum Bayar
            ]);

            DB::commit();

            // Send email (optional) - TODO: Implement sendMenungguPembayaranNotification method
            // try {
            //     $emailService = new \App\Services\EmailService();
            //     $emailService->sendMenungguPembayaranNotification($pendaftaran);
            // } catch (\Exception $e) {
            //     \Log::error('Error sending email: ' . $e->getMessage());
            // }

            return redirect()->back()->with('success', 'Kelayakan disetujui! Asesi dapat melakukan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Approve Kelayakan Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject kelayakan (quick action from asesi list)
     */
    public function rejectKelayakan(Request $request, $pendaftaranId)
    {
        $request->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $pendaftaran = Pendaftaran::with('user')->findOrFail($pendaftaranId);

            // Cek apakah pendaftaran dalam status yang tepat
            if ($pendaftaran->status != 6) {
                return redirect()->back()->with('error', 'Pendaftaran tidak dalam status yang tepat untuk verifikasi kelayakan.');
            }

            // Update status ke Tidak Lolos Kelayakan
            $pendaftaran->update([
                'status' => 7, // Tidak Lolos Kelayakan
                'kelayakan_status' => 2, // Tidak Layak
                'kelayakan_catatan' => $request->catatan,
                'kelayakan_verified_at' => now(),
                'kelayakan_verified_by' => Auth::id(),
            ]);

            // Delete PendaftaranUjikom
            PendaftaranUjikom::where('pendaftaran_id', $pendaftaranId)
                ->where('asesor_id', Auth::id())
                ->delete();

            DB::commit();

            // Send email (optional) - TODO: Implement sendKelayankanDitolakNotification method
            // try {
            //     $emailService = new \App\Services\EmailService();
            //     $emailService->sendKelayankanDitolakNotification($pendaftaran);
            // } catch (\Exception $e) {
            //     \Log::error('Error sending email: ' . $e->getMessage());
            // }

            return redirect()->back()->with('success', 'Kelayakan ditolak. Asesi telah diberitahu.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Reject Kelayakan Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Helper function to save signature
     */
    private function saveSignature($signatureData, $type, $pendaftaranId)
    {
        if (strpos($signatureData, 'data:image') === 0) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));
            $filename = $type . '_signature_' . $pendaftaranId . '_' . time() . '.png';
            $path = 'signatures/' . $filename;

            Storage::disk('public')->put($path, $imageData);

            return $path;
        }

        return null;
    }
}
