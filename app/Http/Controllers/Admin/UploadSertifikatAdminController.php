<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use App\Models\Sertif;
use Illuminate\Http\Request;

class UploadSertifikatAdminController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListAdmin('upload-sertifikat-admin');
        $uploadSertifikat = Sertif::with(['user', 'skema', 'pendaftaran'])->orderBy('created_at', 'desc')->get();

        return view('components.pages.admin.upload-sertifikat-admin.list', compact('lists', 'uploadSertifikat'));
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
            'status' => 'required|in:1,2,3', // 1: Belum Terverifikasi, 2: Terverifikasi, 3: Tidak Terverifikasi
        ]);

        try {
            $sertif = Sertif::findOrFail($id);
            $sertif->update(['status' => $request->status]);
            return redirect()->route('admin.upload-sertifikat-admin.index')->with('success', 'Status sertifikat berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('admin.upload-sertifikat-admin.index')->withInput()->with('error', 'Gagal memperbarui status sertifikat');
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
