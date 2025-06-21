<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use App\Models\Sertif;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class UploadSertifikatController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListAsesi('upload-sertifikat');
        $uploadSertifikat = Sertif::with(['user', 'skema', 'pendaftaran'])->orderBy('created_at', 'desc')->get();

        return view('components.pages.asesi.upload-sertifikat.list', compact('lists', 'uploadSertifikat'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lists = $this->getMenuListAsesi('upload-sertifikat');

        // Ambil semua pendaftaran_id yang sudah ada di tabel sertif
        $usedPendaftaranIds = Sertif::pluck('pendaftaran_id')->toArray();

        // Ambil data pendaftaran yang:
        // - status = 6
        // - belum ada di tabel sertif
        // - milik user yang sedang login
        $pendaftaran = Pendaftaran::where('status', 6)
            ->where('user_id', auth()->id())
            ->whereNotIn('id', $usedPendaftaranIds)
            ->with(['skema', 'tuk', 'jadwal']) // eager load jika ingin ditampilkan di view
            ->orderBy('created_at', 'desc')
            ->get();

        return view('components.pages.asesi.upload-sertifikat.create', compact('lists', 'pendaftaran'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftaran,id',
            'sertifikat' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // max 2MB
        ]);

        try {
            $pendaftaran = Pendaftaran::with('skema')->findOrFail($request->pendaftaran_id);

            // Upload file sertifikat
            $file = $request->file('sertifikat');
            $fileName = 'sertifikat_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads/sertifikat', $fileName, 'public'); // simpan di storage/app/public/uploads/sertifikat

            Sertif::create([
                'pendaftaran_id' => $pendaftaran->id,
                'skema_id' => $pendaftaran->skema_id,
                'user_id' => auth()->id(),
                'sertifikat' => $filePath,
                'status' => 1, // status default "Belum Terverifikasi"
            ]);

            return redirect()->route('asesi.upload-sertifikat.index')->with('success', 'Sertifikat berhasil diupload.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan sertifikat.');
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
        $lists = $this->getMenuListAsesi('upload-sertifikat');
        $uploadSertifikat = Sertif::with(['skema', 'pendaftaran.tuk', 'pendaftaran.jadwal'])->findOrFail($id);

        return view('components.pages.asesi.upload-sertifikat.edit', compact('lists', 'uploadSertifikat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
    
        try {
            $sertif = Sertif::findOrFail($id);
    
            if ($request->hasFile('sertifikat')) {
                // Hapus file lama jika ada
                if ($sertif->sertifikat && \Storage::disk('public')->exists($sertif->sertifikat)) {
                    \Storage::disk('public')->delete($sertif->sertifikat);
                }
    
                $file = $request->file('sertifikat');
                $fileName = 'sertifikat_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('uploads/sertifikat', $fileName, 'public');
    
                $sertif->sertifikat = $filePath;
            }
    
            $sertif->save();
    
            return redirect()->route('asesi.upload-sertifikat.index')->with('success', 'Sertifikat berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui sertifikat.');
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
