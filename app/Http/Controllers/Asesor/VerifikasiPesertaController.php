<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\PendaftaranUjikom;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifikasiPesertaController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListAsesor('verifikasi-peserta');

        // Ambil jadwal berdasarkan asesor yang login (distinct)
        $jadwalList = PendaftaranUjikom::where('asesor_id', Auth::id())
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
