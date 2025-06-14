<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $lists = $this->getMenuListAdmin('profile');
        return view('components.pages.profile', compact('lists', 'user'));
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',id,deleted_at,NULL',
            'nik' => 'required|string|size:16|unique:users,nik,' . $id . ',id,deleted_at,NULL',
            'telephone' => 'required|string|max:15|unique:users,telephone,' . $id . ',id,deleted_at,NULL',
            'alamat' => 'required|string',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'kebangsaan' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'pendidikan' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tanda_tangan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'Nama harus diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'nik.required' => 'NIK harus diisi',
            'nik.size' => 'NIK harus 16 digit',
            'nik.unique' => 'NIK sudah digunakan',
            'telephone.required' => 'Nomor telepon harus diisi',
            'telephone.max' => 'Nomor telepon maksimal 15 digit',
            'telephone.unique' => 'Nomor telepon sudah digunakan',
            'alamat.required' => 'Alamat harus diisi',
            'tempat_lahir.required' => 'Tempat lahir harus diisi',
            'tempat_lahir.max' => 'Tempat lahir maksimal 255 karakter',
            'tanggal_lahir.required' => 'Tanggal lahir harus diisi',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',
            'jenis_kelamin.required' => 'Jenis kelamin harus dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid',
            'kebangsaan.required' => 'Kebangsaan harus diisi',
            'kebangsaan.max' => 'Kebangsaan maksimal 255 karakter',
            'pekerjaan.required' => 'Pekerjaan harus diisi',
            'pekerjaan.max' => 'Pekerjaan maksimal 255 karakter',
            'pendidikan.required' => 'Pendidikan harus diisi',
            'pendidikan.max' => 'Pendidikan maksimal 255 karakter',
            'photo.image' => 'File foto harus berupa gambar',
            'photo.mimes' => 'Format foto harus JPG, JPEG, atau PNG',
            'photo.max' => 'Ukuran foto maksimal 2MB',
            'tanda_tangan.image' => 'File tanda tangan harus berupa gambar',
            'tanda_tangan.mimes' => 'Format tanda tangan harus JPG, JPEG, atau PNG',
            'tanda_tangan.max' => 'Ukuran tanda tangan maksimal 2MB',
        ]);

        try {
            $user = Auth::user();
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

            // Handle photo upload
            if ($request->hasFile('photo')) {
                if ($user->photo) {
                    Storage::delete('public/' . $user->photo);
                }
                $photoPath = $request->file('photo')->store('photos', 'public');
                $data['photo'] = $photoPath;
            }

            // Handle tanda tangan upload
            if ($request->hasFile('tanda_tangan')) {
                if ($user->tanda_tangan) {
                    Storage::delete('public/' . $user->tanda_tangan);
                }
                $tandaTanganPath = $request->file('tanda_tangan')->store('tanda-tangan', 'public');
                $data['tanda_tangan'] = $tandaTanganPath;
            }

            $user->update($data);
            return redirect()->route('admin.profile.index')->with('success', 'Profile berhasil diubah');
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
