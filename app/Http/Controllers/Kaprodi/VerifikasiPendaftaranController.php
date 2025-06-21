<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifikasiPendaftaranController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListKaprodi('verifikasi-pendaftaran');
        $verfikasiPendaftaran = Pendaftaran::with(['jadwal', 'jadwal.skema', 'jadwal.tuk', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('components.pages.kaprodi.verifikasi-pendaftaran.list', compact('lists', 'verfikasiPendaftaran'));
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
        ]);

        $pendaftaran = Pendaftaran::find($id);
        $pendaftaran->status = $request->status;
        $pendaftaran->save();

        return redirect()->route('kaprodi.verifikasi-pendaftaran.index')->with('success', 'Pendaftaran berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
