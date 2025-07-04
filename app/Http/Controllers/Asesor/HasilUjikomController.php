<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\APL2;
use App\Models\Jadwal;
use App\Models\PendaftaranUjikom;
use App\Models\Report;
use App\Models\Response;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasilUjikomController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListAsesor('hasil-ujikom');
        $hasilUjikom = Jadwal::where('status', 3)
            ->with(['skema', 'tuk'])
            ->orderBy('tanggal_ujian', 'asc')
            ->get();

        return view('components.pages.asesor.hasil-ujikom.list', compact('lists', 'hasilUjikom'));
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

        $jadwal = Jadwal::where('status', 3)
            ->with(['skema', 'tuk'])
            ->orderBy('tanggal_ujian', 'asc')
            ->first();

        $asesi = PendaftaranUjikom::where('jadwal_id', $jadwal->id)
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
        $pendaftaranUjikom->asesor_id = Auth::user()->id;
        $pendaftaranUjikom->status = 3;
        $pendaftaranUjikom->save();

        $status = $request->status == 4 ? 2 : 1;

        // Insert ke report
        Report::create([
            'user_id' => $pendaftaranUjikom->asesi_id,
            'pendaftaran_id' => $id,
            'skema_id' => $pendaftaranUjikom->jadwal->skema_id,
            'jadwal_id' => $pendaftaranUjikom->jadwal_id,
            'status' => $status,
        ]);

        return redirect()->route('asesor.hasil-ujikom.show', $id)->with('success', 'Status berhasil diubah');
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

        $jawabanAsesi = Response::where('pendaftaran_id', $id)
            ->with(['pendaftaran', 'pendaftaran.jadwal', 'pendaftaran.jadwal.skema', 'pendaftaran.jadwal.tuk', 'pendaftaran.asesi'])
            ->get();

        return view('components.pages.asesor.hasil-ujikom.jawaban-asesi', compact('lists', 'jawabanAsesi', 'id'));
    }
}
