<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Services\SecondRegistrationService;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DaftarUjikomController extends Controller
{
    use MenuTrait;

    protected $secondRegistrationService;

    public function __construct(SecondRegistrationService $secondRegistrationService)
    {
        $this->secondRegistrationService = $secondRegistrationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Cek asesi harus melengkapi profile
        $asesi = User::where('id', Auth::user()->id)->first();
        if (!$asesi->checkProfileLengkapAsesi()) {
            return redirect()->route('asesi.profil-asesi.index')->with('error', 'Asesi harus melengkapi profil');
        }

        // NEW FLOW: Cek apakah sudah pernah mendaftar (untuk info saja)
        $hasPreviousRegistration = Pendaftaran::where('user_id', Auth::id())->exists();
        $registrationInfo = [
            'has_previous_registration' => $hasPreviousRegistration
        ];

        // Ambil jadwal yang tersedia sesuai jurusan asesi
        $jadwal = Jadwal::with('skema', 'tuk')
            ->whereHas('skema', function ($query) use ($asesi) {
                $query->where('bidang', $asesi->jurusan);
            })
            ->where('status', 1)
            ->where('tanggal_maksimal_pendaftaran', '>=', now())
            ->orderBy('tanggal_maksimal_pendaftaran', 'asc')
            ->get();

        $lists = $this->getMenuListAsesi('daftar-ujikom');
        return view('components.pages.asesi.daftar-ujikom.index', compact('lists', 'jadwal', 'registrationInfo'));
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
        $user = User::where('id', Auth::user()->id)->first();

        // Dynamic validation rules based on existing files
        // Accept both images (jpeg, png, jpg) and PDF files
        $rules = [
            'jadwal_id' => 'required|exists:jadwal,id',
            'photo_ktp' => ($user->photo_ktp ? 'nullable' : 'required') . '|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'photo_sertifikat' => ($user->photo_sertifikat ? 'nullable' : 'required') . '|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'photo_ktmkhs' => ($user->photo_ktmkhs ? 'nullable' : 'required') . '|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'photo_administatif' => ($user->photo_administatif ? 'nullable' : 'required') . '|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ];

        $request->validate($rules);

        // Prepare data to update
        $updateData = [];

        // Only update files that are uploaded
        if ($request->hasFile('photo_ktp')) {
            $updateData['photo_ktp'] = $request->file('photo_ktp')->store('photos', 'public');
        }
        if ($request->hasFile('photo_sertifikat')) {
            $updateData['photo_sertifikat'] = $request->file('photo_sertifikat')->store('photos', 'public');
        }
        if ($request->hasFile('photo_ktmkhs')) {
            $updateData['photo_ktmkhs'] = $request->file('photo_ktmkhs')->store('photos', 'public');
        }
        if ($request->hasFile('photo_administatif')) {
            $updateData['photo_administatif'] = $request->file('photo_administatif')->store('photos', 'public');
        }

        // Update user only if there are files to update
        if (!empty($updateData)) {
            $user->update($updateData);
        }

        // NEW FLOW: Langsung buat pendaftaran tanpa pembayaran dulu
        // Pembayaran dibuat setelah kelayakan diapprove
        try {
            $jadwal = Jadwal::findOrFail($request->jadwal_id);
            
            // Cek apakah sudah ada pendaftaran untuk jadwal ini
            $existingRegistration = Pendaftaran::where('user_id', $user->id)
                ->where('jadwal_id', $request->jadwal_id)
                ->first();

            if ($existingRegistration) {
                // Jika ada pendaftaran yang ditolak (status 2 atau 7), hapus dan buat baru
                if (in_array($existingRegistration->status, [2, 7])) {
                    $existingRegistration->delete();
                } else {
                    return redirect()->back()
                        ->with('error', 'Anda sudah mendaftar untuk jadwal ini.');
                }
            }

            // Buat pendaftaran baru
            $pendaftaran = Pendaftaran::create([
                'user_id' => $user->id,
                'jadwal_id' => $request->jadwal_id,
                'skema_id' => $jadwal->skema_id,
                'tuk_id' => $jadwal->tuk_id,
                'status' => 1, // Menunggu Verifikasi Kaprodi
                'keterangan' => 'Pendaftaran baru - menunggu verifikasi'
            ]);

            session()->flash('success', 'Pendaftaran berhasil! Silakan lengkapi formulir APL dan tunggu proses verifikasi.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error saat membuat pendaftaran: ' . $e->getMessage());
        }

        return redirect()->route('asesi.sertifikasi.index')->with('success', 'Berhasil daftar ujikom! Silakan lengkapi formulir APL.');
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
