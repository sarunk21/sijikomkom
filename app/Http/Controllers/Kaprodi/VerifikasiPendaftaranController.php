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
        $lists = $this->getMenuListKaprodi('verifikasi-pendaftaran');

        $query = Pendaftaran::with(['jadwal', 'jadwal.skema', 'jadwal.tuk', 'user', 'skema', 'pendaftaranUjikom']);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('skema_id')) {
            $query->where('skema_id', $request->skema_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $verfikasiPendaftaran = $query->orderBy('created_at', 'desc')->get();

        // Get all skema for filter dropdown
        $skemas = \App\Models\Skema::orderBy('nama', 'asc')->get();

        return view('components.pages.kaprodi.verifikasi-pendaftaran.list', compact('lists', 'verfikasiPendaftaran', 'skemas'));
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
            'status' => 'required|in:1,2,3',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $pendaftaran = Pendaftaran::findOrFail($id);
        $pendaftaran->status = $request->status;

        // Jika status adalah 2 (verifikasi dengan keterangan), simpan keterangan
        if ($request->status == 2) {
            $pendaftaran->keterangan = $request->keterangan;
        } else {
            // Jika status bukan 2, hapus keterangan
            $pendaftaran->keterangan = null;
        }

        $pendaftaran->save();

        // Kirim email notifikasi jika status berubah menjadi ditolak (status 2)
        if ($request->status == 2) {
            $emailService = new EmailService();
            $emailService->sendPendaftaranDitolakNotification($pendaftaran);
        }

        return redirect()->route('kaprodi.verifikasi-pendaftaran.index')
            ->with('success', "Status pendaftaran berhasil diubah");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
