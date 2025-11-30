<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Jadwal;
use App\Models\Pendaftaran;
use App\Models\PendaftaranUjikom;
use App\Models\Report;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasilUjikomController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $lists = $this->getMenuListAsesor('hasil-ujikom');
        $asesor = Auth::user();

        // Get skema IDs yang dimiliki asesor ini
        $skemaIds = \DB::table('asesor_skema')
            ->where('asesor_id', $asesor->id)
            ->pluck('skema_id');

        // Jika asesor tidak memiliki skema, return empty result
        if ($skemaIds->isEmpty()) {
            $hasilUjikom = collect();
            $skemas = collect();
            return view('components.pages.asesor.hasil-ujikom.list', compact('lists', 'hasilUjikom', 'skemas'));
        }

        // Build query - hanya jadwal dengan status 3 dan skema yang dimiliki asesor
        $query = Jadwal::where('status', 3)
            ->whereIn('skema_id', $skemaIds)
            ->with(['skema', 'tuk']);

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

        $hasilUjikom = $query->orderBy('tanggal_ujian', 'desc')->get();

        // Get skema yang dimiliki asesor untuk filter dropdown
        $skemas = \App\Models\Skema::whereIn('id', $skemaIds)->get();

        return view('components.pages.asesor.hasil-ujikom.list', compact('lists', 'hasilUjikom', 'skemas'));
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
        $lists = $this->getMenuListAsesor('hasil-ujikom');
        $asesor = Auth::user();

        // Get skema IDs yang dimiliki asesor ini
        $skemaIds = \DB::table('asesor_skema')
            ->where('asesor_id', $asesor->id)
            ->pluck('skema_id');

        // Pastikan jadwal adalah milik skema yang dimiliki asesor
        // Tidak perlu filter status karena asesor harus bisa melihat hasil ujian yang sudah selesai juga
        $jadwal = Jadwal::where('id', $id)
            ->whereIn('skema_id', $skemaIds)
            ->with(['skema', 'tuk'])
            ->firstOrFail();

        // Get asesi yang ditugaskan ke asesor ini di jadwal ini
        $asesi = PendaftaranUjikom::where('jadwal_id', $id)
            ->where('asesor_id', $asesor->id) // Hanya asesi yang ditugaskan ke asesor ini
            ->with(['asesi', 'jadwal', 'jadwal.skema', 'jadwal.tuk', 'pendaftaran'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('components.pages.asesor.hasil-ujikom.list-asesi', compact('lists', 'jadwal', 'asesi'));
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
        // Validate request
        $request->validate([
            'status' => 'required|in:4,5',
        ]);

        // Update status ujikom
        $pendaftaranUjikom = PendaftaranUjikom::find($id);

        // Cek apakah sudah pernah dinilai (cek di Report)
        $existingReport = Report::where('user_id', $pendaftaranUjikom->asesi_id)
            ->where('jadwal_id', $pendaftaranUjikom->jadwal_id)
            ->first();

        if ($existingReport) {
            return redirect()->route('asesor.hasil-ujikom.show', $pendaftaranUjikom->jadwal_id)
                ->with('error', 'Asesi ini sudah pernah dinilai sebelumnya dan tidak dapat diubah.');
        }

        $pendaftaranUjikom->asesor_id = Auth::user()->id;
        $pendaftaranUjikom->status = $request->status;

        $request->status == 4 ? $pendaftaranUjikom->keterangan = 'Tidak Kompeten' : $pendaftaranUjikom->keterangan = 'Kompeten';

        $pendaftaranUjikom->save();

        $status = $request->status == 4 ? 2 : 1;

        // Insert ke report
        Report::create([
            'user_id' => $pendaftaranUjikom->asesi_id,
            'pendaftaran_id' => $pendaftaranUjikom->pendaftaran_id,
            'skema_id' => $pendaftaranUjikom->jadwal->skema_id,
            'jadwal_id' => $pendaftaranUjikom->jadwal_id,
            'status' => $status,
        ]);

        return redirect()->route('asesor.hasil-ujikom.show', $pendaftaranUjikom->jadwal_id)->with('success', 'Status berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function showJawabanAsesi(string $id)
    {
        $lists = $this->getMenuListAsesor('hasil-ujikom');

        // Get pendaftaran dengan jawaban bank soal
        $pendaftaran = Pendaftaran::with(['jadwal', 'jadwal.skema', 'jadwal.tuk', 'user'])
            ->find($id);

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan');
        }

        // Get bank soal untuk skema ini
        $bankSoal = BankSoal::where('skema_id', $pendaftaran->skema_id)
            ->where('is_active', true)
            ->get();

        // Jawaban asesi ada di field bank_soal_responses di tabel pendaftaran
        $jawabanAsesi = $pendaftaran->bank_soal_responses ?? [];

        return view('components.pages.asesor.hasil-ujikom.jawaban-asesi', compact('lists', 'pendaftaran', 'bankSoal', 'jawabanAsesi', 'id'));
    }
}
