<?php

namespace App\Http\Controllers\Tuk;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;

class KonfirmasiJadwalController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListKepalaTuk('report-pimpinan');

        $konfirmasiJadwal = Jadwal::where('status', 5)
            ->with(['skema', 'tuk'])
            ->orderBy('tanggal_ujian', 'asc')
            ->get();

        return view('components.pages.tuk.konfirmasi-jadwal.list', compact('lists', 'konfirmasiJadwal'));
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
            'status' => 'required|in:1,2,3,4,5,6',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $jadwal = Jadwal::findOrFail($id);
        $jadwal->status = $request->status;
        
        if ($request->status == 6) {
            $jadwal->keterangan = $request->keterangan;
        } else {
            $jadwal->keterangan = null;
        }

        $jadwal->save();

        return redirect()->route('tuk.konfirmasi-jadwal.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
