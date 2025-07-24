<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tuk;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;

class TUKController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tuk = Tuk::orderBy('nama', 'asc')->get();
        $lists = $this->getMenuListAdmin('tuk');
        $activeMenu = 'tuk';
        return view('components.pages.admin.tuk.list', compact('lists', 'activeMenu', 'tuk'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lists = $this->getMenuListAdmin('tuk');
        $activeMenu = 'tuk';
        return view('components.pages.admin.tuk.create', compact('lists', 'activeMenu'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'kode' => 'required|unique:tuk,kode,NULL,id,deleted_at,NULL',
            'kategori' => 'required',
            'alamat' => 'required',
        ]);

        try {
            Tuk::create([
                'nama' => $request->nama,
                'kode' => $request->kode,
                'kategori' => $request->kategori,
                'alamat' => $request->alamat,
            ]);
            return redirect()->route('admin.tuk.index')->with('success', 'TUK berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('admin.tuk.index')->withInput()->with('error', 'TUK gagal ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tuk = Tuk::find($id);
        $activeMenu = 'tuk';
        return view('components.pages.admin.tuk.edit', compact('lists', 'activeMenu', 'tuk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lists = $this->getMenuListAdmin('tuk');
        $tuk = Tuk::find($id);
        $activeMenu = 'tuk';
        return view('components.pages.admin.tuk.edit', compact('lists', 'activeMenu', 'tuk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => 'required',
            'kode' => 'required|unique:tuk,kode,' . $id . ',id,deleted_at,NULL',
            'kategori' => 'required',
            'alamat' => 'required',
        ]);

        try {
            $tuk = Tuk::find($id);
            $tuk->update($request->all());
            return redirect()->route('admin.tuk.index')->with('success', 'TUK berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('admin.tuk.index')->withInput()->with('error', 'TUK gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $tuk = Tuk::find($id);
            $tuk->delete();
            return redirect()->route('admin.tuk.index')->with('success', 'TUK berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.tuk.index')->with('error', 'TUK gagal dihapus');
        }
    }
}
