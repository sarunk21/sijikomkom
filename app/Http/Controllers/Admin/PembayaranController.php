<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;

class PembayaranController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListAdmin('pembayaran-asesi');
        $activeMenu = 'pembayaran';
        $pembayaranAsesi = Pembayaran::where('status', 1)->orWhere('status', 2)->with(['jadwal', 'user'])->get();
        return view('components.pages.admin.pembayaran.list', compact('lists', 'activeMenu', 'pembayaranAsesi'));
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
            'status' => 'required|in:1,2',
        ]);

        try {
            $pembayaran = Pembayaran::find($id);

            if ($request->status == 1) {
                Pendaftaran::create([
                    'jadwal_id' => $pembayaran->jadwal_id,
                    'user_id' => $pembayaran->user_id,
                    'skema_id' => $pembayaran->jadwal->skema_id,
                    'tuk_id' => $pembayaran->jadwal->tuk_id,
                    'status' => 1,
                ]);

                $pembayaran->status = 4;
                $pembayaran->save();
            }

            if ($request->status == 2) {
                $pembayaran->status = 3;
                $pembayaran->save();
            }

            return redirect()->route('admin.pembayaran-asesi.index')->with('success', 'Pembayaran berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->route('admin.pembayaran-asesi.index')->with('error', 'Pembayaran gagal diupdate: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
