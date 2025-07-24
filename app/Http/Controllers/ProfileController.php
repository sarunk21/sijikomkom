<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        switch ($user->user_type) {
            case 'admin':
                return redirect()->route('admin.profile.index');
            case 'asesi':
                return redirect()->route('asesi.profil-asesi.index');
            case 'asesor':
                return redirect()->route('asesor.profil-asesor.index');
            case 'kaprodi':
                return redirect()->route('kaprodi.profil-kaprodi.index');
            case 'pimpinan':
                return redirect()->route('pimpinan.profil-pimpinan.index');
            case 'tuk':
                return redirect()->route('tuk.profil-tuk.index');
            default:
                return redirect()->route('login');
        }
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
        $user = Auth::user();

        // Validasi dasar untuk semua user
        $baseValidation = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',id,deleted_at,NULL',
            'nik' => 'required|string|size:16|unique:users,nik,' . $id . ',id,deleted_at,NULL',
            'telephone' => 'required|string|max:15|unique:users,telephone,' . $id . ',id,deleted_at,NULL',
            'alamat' => 'required|string',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'kebangsaan' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'pendidikan' => 'required|string|max:255',
            'photo_diri' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        // Tambahan validasi untuk asesi
        if ($user->user_type == 'asesi') {
            $baseValidation['nim'] = 'required|string|max:10|unique:users,nim,' . $id . ',id,deleted_at,NULL';
            $baseValidation['jurusan'] = 'required|string|max:255';
        }

        $request->validate($baseValidation);

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'nik' => $request->nik,
                'telephone' => $request->telephone,
                'alamat' => $request->alamat,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'kebangsaan' => $request->kebangsaan,
                'pekerjaan' => $request->pekerjaan,
                'pendidikan' => $request->pendidikan,
            ];

            // Tambahkan data khusus untuk asesi
            if ($user->user_type == 'asesi') {
                $data['nim'] = $request->nim;
                $data['jurusan'] = $request->jurusan;
            }

            // Handle photo upload
            if ($request->hasFile('photo_diri')) {
                if ($user->photo_diri) {
                    Storage::delete('public/photos/' . $user->photo_diri);
                }
                $photoPath = $request->file('photo_diri')->store('photos', 'public');
                $data['photo_diri'] = $photoPath;
            }

            $user->update($data);

            // Redirect berdasarkan tipe user
            switch ($user->user_type) {
                case 'admin':
                    return redirect()->route('admin.profile.index')->with('success', 'Profile berhasil diubah');
                case 'asesi':
                    return redirect()->route('asesi.profil-asesi.index')->with('success', 'Profile berhasil diubah');
                case 'asesor':
                    return redirect()->route('asesor.profil-asesor.index')->with('success', 'Profile berhasil diubah');
                case 'kaprodi':
                    return redirect()->route('kaprodi.profil-kaprodi.index')->with('success', 'Profile berhasil diubah');
                case 'pimpinan':
                    return redirect()->route('pimpinan.profil-pimpinan.index')->with('success', 'Profile berhasil diubah');
                case 'tuk':
                    return redirect()->route('tuk.profil-tuk.index')->with('success', 'Profile berhasil diubah');
                default:
                    return redirect()->route('login');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Profile gagal diubah: ' . $e->getMessage());
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
