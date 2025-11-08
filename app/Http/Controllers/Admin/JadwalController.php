<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\APL2;
use App\Models\Jadwal;
use App\Models\PendaftaranUjikom;
use App\Models\Skema;
use App\Models\Tuk;
use App\Services\EmailService;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Build query
        $query = Jadwal::with('skema', 'tuk');

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_ujian', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_ujian', '<=', $request->tanggal_sampai);
        }

        // Filter by skema
        if ($request->filled('skema_id')) {
            $query->where('skema_id', $request->skema_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jadwal = $query->orderBy('tanggal_ujian', 'desc')->get();

        // Get all skema for filter dropdown
        $skemas = Skema::orderBy('nama', 'asc')->get();

        $lists = $this->getMenuListAdmin('jadwal');
        $activeMenu = 'jadwal';
        return view('components.pages.admin.jadwal.list', compact('lists', 'activeMenu', 'jadwal', 'skemas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lists = $this->getMenuListAdmin('jadwal');
        $skema = Skema::orderBy('nama', 'asc')->get();
        $tuk = Tuk::orderBy('nama', 'asc')->get();
        $activeMenu = 'jadwal';
        return view('components.pages.admin.jadwal.create', compact('lists', 'activeMenu', 'skema', 'tuk'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'skema_id' => 'required',
            'tuk_id' => 'required',
            'tanggal_ujian' => 'required',
            'tanggal_selesai' => 'required',
            'tanggal_maksimal_pendaftaran' => 'required',
            'kuota' => 'required',
        ]);

        try {
            // Cek jika soal belum ada, maka return error
            $apl2 = APL2::where('skema_id', $request->skema_id)->first();
            if (!$apl2) {
                return redirect()->route('admin.jadwal.create')->withInput()->with('error', 'Soal APL2 belum ada, silahkan tambahkan soal terlebih dahulu');
            }

            $jadwal = Jadwal::create([
                'skema_id' => $request->skema_id,
                'tuk_id' => $request->tuk_id,
                'tanggal_ujian' => $request->tanggal_ujian,
                'tanggal_selesai' => $request->tanggal_selesai,
                'tanggal_maksimal_pendaftaran' => $request->tanggal_maksimal_pendaftaran,
                'status' => 5,
                'kuota' => $request->kuota,
            ]);

            // Kirim email notifikasi ke kepala TUK
            $emailService = new EmailService();
            $emailService->sendJadwalBaruNotification($jadwal);

            return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('admin.jadwal.create')->withInput()->with('error', 'Jadwal gagal ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jadwal = Jadwal::find($id);
        $skema = Skema::orderBy('nama', 'asc')->get();
        $tuk = Tuk::orderBy('nama', 'asc')->get();
        $activeMenu = 'jadwal';
        return view('components.pages.admin.jadwal.edit', compact('lists', 'activeMenu', 'jadwal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lists = $this->getMenuListAdmin('jadwal');
        $jadwal = Jadwal::find($id);
        $skema = Skema::orderBy('nama', 'asc')->get();
        $tuk = Tuk::orderBy('nama', 'asc')->get();
        $activeMenu = 'jadwal';
        return view('components.pages.admin.jadwal.edit', compact('lists', 'activeMenu', 'jadwal', 'skema', 'tuk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'skema_id' => 'required',
            'tuk_id' => 'required',
            'tanggal_ujian' => 'required',
            'tanggal_selesai' => 'required',
            'tanggal_maksimal_pendaftaran' => 'required',
            'kuota' => 'required',
        ]);

        try {
            $jadwal = Jadwal::find($id);

            $jadwal->update($request->all());

            return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('admin.jadwal.index')->withInput()->with('error', 'Jadwal gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Cek jika jadwal sudah ada pendaftaran, maka tidak bisa dihapus
            if (Jadwal::find($id)->pendaftaran->count() > 0) {
                return redirect()->route('admin.jadwal.index')->with('error', 'Jadwal tidak bisa dihapus karena sudah ada pendaftaran');
            }
            // Cek jika jadwal sudah selesai, maka tidak bisa dihapus
            if (Jadwal::find($id)->status == 4) {
                return redirect()->route('admin.jadwal.index')->with('error', 'Jadwal tidak bisa dihapus karena sudah selesai');
            }

            $jadwal = Jadwal::find($id);
            $jadwal->delete();
            return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.jadwal.index')->with('error', 'Jadwal gagal dihapus');
        }
    }

    /**
     * Get the specified resource from storage.
     */
    public function getJadwalUjian(string $id)
    {
        $pendaftaranUjikom = PendaftaranUjikom::where('jadwal_id', $id)
            ->groupBy('asesor_id')
            ->get();

        return view('components.pages.admin.jadwal.ujian', compact('pendaftaranUjikom'));
    }
}
