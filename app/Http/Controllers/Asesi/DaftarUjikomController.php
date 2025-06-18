<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pembayaran;
use App\Models\User;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DaftarUjikomController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Cek asesi harus melengkapi profile
        $asesi = User::where('id', Auth::user()->id)->first();
        if (!$asesi->checkProfileLengkapAsesi()) {
            return redirect()->route('asesi.profil-asesi.index')->with('error', 'Asesi harus melengkapi profil');
        }

        $jadwal = Jadwal::with('skema', 'tuk')
            ->whereHas('skema', function ($query) use ($asesi) {
                $query->where('bidang', $asesi->jurusan);
            })
            ->where('status', 1)
            ->where('tanggal_ujian', '>=', now())
            ->orderBy('tanggal_ujian', 'asc')
            ->get();

        $lists = $this->getMenuListAsesi('daftar-ujikom');
        return view('components.pages.asesi.daftar-ujikom.index', compact('lists', 'jadwal'));
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
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal,id',
            'photo_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'photo_sertifikat' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'photo_ktmkhs' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'photo_administatif' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = User::where('id', Auth::user()->id)->first();

        $photoKtp = $request->file('photo_ktp')->store('photos', 'public');
        $photoSertifikat = $request->file('photo_sertifikat')->store('photos', 'public');
        $photoKtmkhs = $request->file('photo_ktmkhs')->store('photos', 'public');
        $photoAdministatif = $request->file('photo_administatif')->store('photos', 'public');
        $user->update([
            'photo_ktp' => $photoKtp,
            'photo_sertifikat' => $photoSertifikat,
            'photo_ktmkhs' => $photoKtmkhs,
            'photo_administatif' => $photoAdministatif,
        ]);

        // Check apakah asesi sudah pernah daftar ujikom
        $pembayaran = Pembayaran::where('user_id', Auth::user()->id)->first();
        if ($pembayaran) {
            Pembayaran::create([
                'user_id' => Auth::user()->id,
                'jadwal_id' => $request->jadwal_id,
                'status' => 1,
            ]);
        } else {
            Pembayaran::create([
                'user_id' => Auth::user()->id,
                'jadwal_id' => $request->jadwal_id,
                'status' => 2,
            ]);
        }

        return redirect()->route('asesi.daftar-ujikom.index')->with('success', 'Berhasil daftar ujikom');
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
