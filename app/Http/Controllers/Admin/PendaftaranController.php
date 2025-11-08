<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Skema;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $lists = $this->getMenuListAdmin('pendaftaran');

        $query = Pendaftaran::with('jadwal', 'jadwal.skema', 'jadwal.tuk', 'user');

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        // Filter by skema
        if ($request->filled('skema_id')) {
            $query->where('skema_id', $request->skema_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pendaftaran = $query->orderBy('created_at', 'desc')->get();
        $skemas = Skema::orderBy('nama', 'asc')->get();

        return view('components.pages.admin.pendaftaran.list', compact('lists', 'pendaftaran', 'skemas'));
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
        try {
            $request->validate([
                'status' => 'required|in:1,2,3,4,5,6',
            ]);

            $pendaftaran = Pendaftaran::find($id);
            $pendaftaran->status = $request->status;
            $pendaftaran->save();

            return redirect()->route('admin.pendaftaran.index')->with('success', 'Pendaftaran berhasil diupdate');
        } catch (\Throwable $th) {
            return redirect()->route('admin.pendaftaran.index')->with('error', 'Pendaftaran gagal diupdate: ' . $th->getMessage());
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
