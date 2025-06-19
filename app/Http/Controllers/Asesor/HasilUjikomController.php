<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\APL2;
use App\Models\Jadwal;
use App\Models\PendaftaranUjikom;
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
            ->with(['skema'])
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
            ->with(['skema'])
            ->first();

        $asesi = PendaftaranUjikom::where('jadwal_id', $jadwal->id)
            ->with(['jadwal'])
            ->get();

        $apl2 = APL2::where('skema_id', $jadwal->skema_id)->first();

        return view('components.pages.asesor.hasil-ujikom.list-asesi', compact('lists', 'jadwal', 'asesi', 'apl2'));
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
            'status' => 'required|in:2,3',
        ]);

        // Update status ujikom
        $pendaftaranUjikom = PendaftaranUjikom::find($id);
        $pendaftaranUjikom->asesor_id = Auth::user()->id;
        $pendaftaranUjikom->status = $request->status;
        $pendaftaranUjikom->save();

        return redirect()->route('asesor.hasil-ujikom.show', $id)->with('success', 'Status berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
