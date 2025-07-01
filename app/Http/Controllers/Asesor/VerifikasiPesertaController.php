<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\PendaftaranUjikom;
use App\Models\User;
use App\Traits\MenuTrait;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifikasiPesertaController extends Controller
{
    use MenuTrait;

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListAsesor('verifikasi-peserta');

        // Ambil jadwal berdasarkan asesor yang login (distinct)
        $jadwalList = PendaftaranUjikom::where('asesor_id', operator: Auth::id())
            ->with(['jadwal.skema', 'jadwal.tuk'])
            ->select('jadwal_id')
            ->distinct()
            ->get()
            ->map(function ($item) {
                return $item->jadwal;
            });

        return view('components.pages.asesor.verifikasi-peserta.list', compact('lists', 'jadwalList'));
    }

    /**
     * Show list asesi untuk jadwal tertentu
     */
    public function showAsesi($jadwalId)
    {
        $lists = $this->getMenuListAsesor('verifikasi-peserta');

        // Ambil jadwal untuk validasi
        $jadwal = PendaftaranUjikom::where('jadwal_id', $jadwalId)
            ->where('asesor_id', Auth::id())
            ->with(['jadwal.skema', 'jadwal.tuk'])
            ->first();

        if (!$jadwal) {
            return redirect()->route('asesor.verifikasi-peserta.index')
                ->with('error', 'Jadwal tidak ditemukan');
        }

        $asesiList = PendaftaranUjikom::where('jadwal_id', $jadwalId)
            ->where('asesor_id', Auth::id())
            ->with(['asesi', 'pendaftar'])
            ->get();

        return view('components.pages.asesor.verifikasi-peserta.asesi-list', compact('lists', 'jadwal', 'asesiList'));
    }

    /**
     * Update status kehadiran asesor
     */
    public function updateStatus(Request $request, $jadwalId)
    {
        try {
            DB::beginTransaction();

            $jadwal = PendaftaranUjikom::where('jadwal_id', $jadwalId)
                ->where('asesor_id', Auth::id())
                ->first();

            $pendaftaranUjikom = PendaftaranUjikom::where('jadwal_id', $jadwalId)
                ->where('asesor_id', Auth::id())
                ->get();

            $pendaftaran = Pendaftaran::where('jadwal_id', $jadwalId)
                ->whereIn('user_id', $pendaftaranUjikom->pluck('asesi_id'))
                ->get();

            if (!$pendaftaranUjikom) {
                return redirect()->route('asesor.verifikasi-peserta.index')
                    ->with('error', 'Anda tidak terdaftar sebagai asesor untuk jadwal ini');
            }

            if ($request->isMethod('PUT')) {
                foreach ($pendaftaranUjikom as $p) {
                    $p->update([
                        'status' => 1,
                        'keterangan' => $request->keterangan ?? 'Asesor mengkonfirmasi dapat hadir'
                    ]);
                }

                $message = 'Status kehadiran berhasil diperbarui. Anda dapat hadir untuk ujian kompetensi.';
            } elseif ($request->isMethod('DELETE')) {
                foreach ($pendaftaranUjikom as $p) {
                    $p->update([
                        'status' => 8,
                        'keterangan' => $request->keterangan ?? 'Asesor mengkonfirmasi tidak dapat hadir'
                    ]);
                }

                // Update status pendaftaran
                foreach ($pendaftaran as $p) {
                    $p->update([
                        'status' => 7,
                        'keterangan' => $request->keterangan ?? 'Asesor mengkonfirmasi tidak dapat hadir'
                    ]);
                }

                // Kirim email notifikasi ke asesi dan admin
                $this->sendNotificationEmails($jadwal, $request->keterangan);

                $message = 'Status kehadiran berhasil diperbarui. Notifikasi telah dikirim ke asesi dan admin.';
            } else {
                return redirect()->route('asesor.verifikasi-peserta.index')
                    ->with('error', 'Method request tidak valid');
            }

            DB::commit();

            return redirect()->route('asesor.verifikasi-peserta.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('asesor.verifikasi-peserta.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Kirim email notifikasi ke asesi dan admin
     */
    private function sendNotificationEmails($jadwal, $alasan)
    {
        try {
            // Ambil semua asesi yang terdaftar di jadwal ini
            $asesiList = PendaftaranUjikom::where('jadwal_id', $jadwal->id)
                ->where('asesor_id', Auth::id())
                ->with(['asesi', 'pendaftar'])
                ->get();

            // Ambil admin
            $adminList = User::where('user_type', 'admin')->get();

            // Data untuk email
            $emailData = [
                'jadwal' => $jadwal,
                'asesor' => Auth::user(),
                'alasan' => $alasan,
                'tanggal_ujian' => \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('d-m-Y'),
                'skema' => $jadwal->skema->nama,
                'tuk' => $jadwal->tuk->nama
            ];

            // Kirim email ke asesi
            foreach ($asesiList as $pendaftaran) {
                if ($pendaftaran->asesi && $pendaftaran->asesi->email) {
                    $this->emailService->sendAsesorTidakHadirEmail(
                        $pendaftaran->asesi->email,
                        $pendaftaran->asesi->name,
                        $emailData
                    );
                }
            }

            // Kirim email ke admin
            foreach ($adminList as $admin) {
                if ($admin->email) {
                    $this->emailService->sendAsesorTidakHadirAdminEmail(
                        $admin->email,
                        $admin->name,
                        $emailData
                    );
                }
            }

        } catch (\Exception $e) {
            Log::error('Error sending notification emails: ' . $e->getMessage());
        }
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
