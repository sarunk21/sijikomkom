<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use App\Models\PembayaranAsesor;
use App\Models\User;
use App\Models\Skema;

class PembayaranAsesorController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $lists = $this->getMenuListAdmin('pembayaran-asesor', 'pembayaran-asesor');

        $query = PembayaranAsesor::where('status', 1)
            ->with(['jadwal', 'jadwal.skema', 'jadwal.tuk', 'asesor']);

        // Filter by date range (using jadwal's tanggal_ujian)
        if ($request->filled('tanggal_dari')) {
            $query->whereHas('jadwal', function($q) use ($request) {
                $q->where('tanggal_ujian', '>=', $request->tanggal_dari);
            });
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereHas('jadwal', function($q) use ($request) {
                $q->where('tanggal_ujian', '<=', $request->tanggal_sampai);
            });
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

        $pembayaranAsesor = $query->orderBy('created_at', 'desc')->get();
        $skemas = Skema::orderBy('nama', 'asc')->get();

        return view('components.pages.admin.pembayaranasesor.list', compact('lists', 'pembayaranAsesor', 'skemas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lists = $this->getMenuListAdmin('pembayaran-asesor', 'pembayaran-asesor');
        $asesor = User::where('user_type', 'asesor')->get();
        $jadwal = Jadwal::where('status', 4)->orderBy('tanggal_ujian', 'asc')->get();
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

            // Pastikan folder bukti_pembayaran ada
            $folderPath = storage_path('app/public/bukti_pembayaran');
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0755, true);
            }

            $buktiPembayaran->storeAs('bukti_pembayaran', $buktiPembayaran->hashName(), 'public');

            // cek apakah jadwal sudah ada pembayaran
            $pembayaranAsesor = PembayaranAsesor::where('jadwal_id', $request->jadwal_id)->first();
            if ($pembayaranAsesor) {
                return redirect()->route('admin.pembayaran-asesor.create')->with('error', 'Jadwal sudah ada pembayaran');
            }

            $pembayaranAsesor = PembayaranAsesor::create([
                'asesor_id' => $request->asesor_id,
                'jadwal_id' => $request->jadwal_id,
                'bukti_pembayaran' => $buktiPembayaran->hashName(),
                'status' => 3,
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
        $pembayaranAsesor = PembayaranAsesor::with(['jadwal', 'asesor'])->findOrFail($id);

        // Check if file exists
        $filePath = storage_path('app/public/bukti_pembayaran/' . $pembayaranAsesor->bukti_pembayaran);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File bukti pembayaran tidak ditemukan.');
        }

        return response()->file($filePath);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lists = $this->getMenuListAdmin('pembayaran-asesor', 'pembayaran-asesor');
        $pembayaranAsesor = PembayaranAsesor::where('id', $id)->first();

        return view('components.pages.admin.pembayaranasesor.create', compact('lists', 'pembayaranAsesor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'asesor_id' => 'required|exists:users,id',
            'jadwal_id' => 'required|exists:jadwal,id',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $buktiPembayaran = $request->file('bukti_pembayaran');

        // Pastikan folder bukti_pembayaran ada
        $folderPath = storage_path('app/public/bukti_pembayaran');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        $buktiPembayaran->storeAs('bukti_pembayaran', $buktiPembayaran->hashName(), 'public');

        $pembayaranAsesor = PembayaranAsesor::where('jadwal_id', $request->jadwal_id)->first();
        $pembayaranAsesor->asesor_id = $request->asesor_id;
        $pembayaranAsesor->bukti_pembayaran = $buktiPembayaran->hashName();
        $pembayaranAsesor->status = 2;
        $pembayaranAsesor->save();

        return redirect()->route('admin.pembayaran-asesor.index')->with('success', 'Status pembayaran asesor berhasil diubah');
    }

    /**
     * Download bukti pembayaran
     */
    public function download(string $id)
    {
        $pembayaranAsesor = PembayaranAsesor::with(['jadwal', 'asesor'])->findOrFail($id);

        // Check if file exists
        $filePath = storage_path('app/public/bukti_pembayaran/' . $pembayaranAsesor->bukti_pembayaran);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File bukti pembayaran tidak ditemukan.');
        }

        return response()->download($filePath, 'bukti_pembayaran_' . $pembayaranAsesor->asesor->name . '_' . $pembayaranAsesor->jadwal->tanggal_ujian . '.jpg');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
