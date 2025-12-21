<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Pembayaran;
use App\Mail\MenungguPembayaranMail;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class KelayankanController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of pendaftaran menunggu approval kelayakan
     */
    public function index(Request $request)
    {
        $lists = $this->getMenuListAdmin('kelayakan');

        $query = Pendaftaran::with([
            'user',
            'jadwal',
            'jadwal.skema',
            'jadwal.tuk',
            'skema',
            'tuk',
            'kelayankanVerifikasi',
            'kelayankanVerifikasi.asesor'
        ]);

        // Filter pendaftaran dengan status 6 (Menunggu Approval Kelayakan)
        $query->where('status', 6);

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

        $pendaftaranList = $query->orderBy('created_at', 'desc')->get();

        // Get skema for filter
        $skemas = \App\Models\Skema::orderBy('nama', 'asc')->get();

        return view('components.pages.admin.kelayakan.list', compact('lists', 'pendaftaranList', 'skemas'));
    }

    /**
     * Approve kelayakan dan buat pembayaran
     */
    public function approve($pendaftaranId)
    {
        try {
            DB::beginTransaction();

            $pendaftaran = Pendaftaran::with('user', 'jadwal')->findOrFail($pendaftaranId);

            // Update status pendaftaran ke Menunggu Pembayaran
            $pendaftaran->update([
                'status' => 8, // Menunggu Pembayaran
                'kelayakan_status' => 1, // Layak
                'kelayakan_verified_at' => now(),
                'kelayakan_verified_by' => auth()->id(),
            ]);

            // Buat pembayaran baru
            $pembayaran = Pembayaran::create([
                'user_id' => $pendaftaran->user_id,
                'jadwal_id' => $pendaftaran->jadwal_id,
                'status' => 1, // Belum Bayar
                'keterangan' => 'Pembayaran Ujikom',
            ]);

            DB::commit();

            // Send email ke asesi
            try {
                Mail::to($pendaftaran->user->email)
                    ->send(new MenungguPembayaranMail($pendaftaran, $pembayaran));
            } catch (\Exception $e) {
                Log::error('Error sending email: ' . $e->getMessage());
            }

            return redirect()->route('admin.kelayakan.index')
                ->with('success', 'Kelayakan telah diapprove! Pembayaran telah dibuat untuk asesi.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approve kelayakan: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject kelayakan
     */
    public function reject(Request $request, $pendaftaranId)
    {
        $request->validate([
            'keterangan' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $pendaftaran = Pendaftaran::with('user')->findOrFail($pendaftaranId);

            // Update status ke Tidak Lolos
            $pendaftaran->update([
                'status' => 7, // Tidak Lolos Kelayakan
                'kelayakan_status' => 2, // Tidak Layak
                'kelayakan_catatan' => $request->keterangan,
                'kelayakan_verified_at' => now(),
                'kelayakan_verified_by' => auth()->id(),
            ]);

            DB::commit();

            // Send email ke asesi
            try {
                Mail::to($pendaftaran->user->email)
                    ->send(new \App\Mail\KelayankanDitolakMail($pendaftaran, $request->keterangan));
            } catch (\Exception $e) {
                Log::error('Error sending email: ' . $e->getMessage());
            }

            return redirect()->route('admin.kelayakan.index')
                ->with('success', 'Kelayakan ditolak dan asesi telah diberitahu.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error reject kelayakan: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

