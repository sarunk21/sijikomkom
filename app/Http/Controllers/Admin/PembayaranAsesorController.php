<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use App\Models\PembayaranAsesor;
use App\Models\User;

class PembayaranAsesorController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListAdmin('pembayaran-asesor', 'pembayaran-asesor');
        $pembayaranAsesor = PembayaranAsesor::where('status', 1)->get();
        return view('components.pages.admin.pembayaranasesor.list', compact('lists', 'pembayaranAsesor'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lists = $this->getMenuListAdmin('pembayaran-asesor', 'pembayaran-asesor');
        $asesor = User::where('user_type', 'asesor')->get();
        $jadwal = Jadwal::where('status', 3)->get();
        return view('components.pages.admin.pembayaranasesor.create', compact('lists', 'asesor', 'jadwal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'asesor_id' => 'required|exists:users,id',
                'jadwal_id' => 'required|exists:jadwal,id',
                'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $buktiPembayaran = $request->file('bukti_pembayaran');
            $buktiPembayaran->storeAs('public/bukti_pembayaran', $buktiPembayaran->hashName());

            $pembayaranAsesor = PembayaranAsesor::create([
                'asesor_id' => $request->asesor_id,
                'jadwal_id' => $request->jadwal_id,
                'bukti_pembayaran' => $buktiPembayaran->hashName(),
                'status' => 1,
            ]);

            return redirect()->route('admin.pembayaran-asesor.index')->with('success', 'Pembayaran asesor berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('admin.pembayaran-asesor.create')->withInput()->with('error', $th->getMessage());
        }
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
