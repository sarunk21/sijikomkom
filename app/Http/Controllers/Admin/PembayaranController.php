<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\Skema;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $lists = $this->getMenuListAdmin('pembayaran-asesi');
        $activeMenu = 'pembayaran';

        $query = Pembayaran::where(function($q) {
                $q->where('status', 1)->orWhere('status', 2);
            })
            ->with(['jadwal', 'jadwal.skema', 'jadwal.tuk', 'user']);

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        // Filter by skema
        if ($request->filled('skema_id')) {
            $query->whereHas('jadwal', function($q) use ($request) {
                $q->where('skema_id', $request->skema_id);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pembayaranAsesi = $query->orderBy('created_at', 'desc')->get();
        $skemas = Skema::orderBy('nama', 'asc')->get();

        return view('components.pages.admin.pembayaran.list', compact('lists', 'activeMenu', 'pembayaranAsesi', 'skemas'));
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
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();
            
            $pembayaran = Pembayaran::findOrFail($id);

            if ($request->status == 1) {
                // Approve pembayaran
                // Update SEMUA pendaftaran dengan status 8 (Menunggu Pembayaran) untuk user dan jadwal ini ke status 9 (Menunggu Ujian)
                $updatedCount = Pendaftaran::where('user_id', $pembayaran->user_id)
                    ->where('jadwal_id', $pembayaran->jadwal_id)
                    ->where('status', 8)
                    ->update(['status' => 9]);

                // Jika tidak ada pendaftaran dengan status 8, cek apakah ada pendaftaran lain
                if ($updatedCount == 0) {
                    $existingPendaftaran = Pendaftaran::where('user_id', $pembayaran->user_id)
                        ->where('jadwal_id', $pembayaran->jadwal_id)
                        ->first();

                    // Jika belum ada pendaftaran sama sekali (old flow), create new dengan status 1
                    if (!$existingPendaftaran) {
                        Pendaftaran::create([
                            'jadwal_id' => $pembayaran->jadwal_id,
                            'user_id' => $pembayaran->user_id,
                            'skema_id' => $pembayaran->jadwal->skema_id,
                            'tuk_id' => $pembayaran->jadwal->tuk_id,
                            'status' => 1,
                        ]);
                    }
                }

                // Update status pembayaran ke 4 (Dikonfirmasi)
                $pembayaran->status = 4;
                $pembayaran->keterangan = null; // Hapus keterangan jika ada
                $pembayaran->save();

                DB::commit();

                return redirect()->route('admin.pembayaran-asesi.index')
                    ->with('success', 'Pembayaran berhasil dikonfirmasi dan status pendaftaran telah diupdate');
            }

            if ($request->status == 2) {
                // Reject pembayaran
                if (empty($request->keterangan)) {
                    return redirect()->route('admin.pembayaran-asesi.index')
                        ->with('error', 'Keterangan penolakan harus diisi');
                }

                $pembayaran->status = 3;
                $pembayaran->keterangan = $request->keterangan;
                $pembayaran->save();

                return redirect()->route('admin.pembayaran-asesi.index')
                    ->with('success', 'Pembayaran berhasil ditolak');
            }

            return redirect()->route('admin.pembayaran-asesi.index')
                ->with('error', 'Status tidak valid');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.pembayaran-asesi.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
