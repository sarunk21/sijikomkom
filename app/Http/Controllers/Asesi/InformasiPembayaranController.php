<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pembayaran;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;

class InformasiPembayaranController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListAsesi('informasi-pembayaran');
        $pembayaran = Pembayaran::with(['jadwal', 'jadwal.skema', 'jadwal.tuk', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('components.pages.asesi.informasi-pembayaran.list', data: compact('lists', 'pembayaran'));
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
        $pembayaran = Pembayaran::where('id', $id)->first();
        $jadwal = Jadwal::where('status', 1)
            ->orderBy('tanggal_ujian', 'asc')
            ->get();
        $lists = $this->getMenuListAsesi('informasi-pembayaran');
        return view('components.pages.asesi.informasi-pembayaran.edit', compact('lists', 'pembayaran', 'jadwal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $pembayaran = Pembayaran::where('id', $id)->first();
        $buktiPembayaran = $request->file('bukti_pembayaran')->store('photos', 'public');
        $pembayaran->update([
            'bukti_pembayaran' => $buktiPembayaran,
            'status' => 2,
        ]);

        return redirect()->route('asesi.informasi-pembayaran.index')->with('success', 'Berhasil mengubah informasi pembayaran');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
