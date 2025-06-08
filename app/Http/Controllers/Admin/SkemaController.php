<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skema;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;

class SkemaController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $skema = Skema::orderBy('created_at', 'desc')->get();
        $lists = $this->getMenuListAdmin('skema');
        $activeMenu = 'skema';
        return view('components.pages.admin.skema.list', compact('lists', 'activeMenu', 'skema'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lists = $this->getMenuListAdmin('skema');
        $activeMenu = 'skema';
        return view('components.pages.admin.skema.create', compact('lists', 'activeMenu'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'kode' => 'required|unique:skema,kode,NULL,id,deleted_at,NULL',
            'kategori' => 'required',
            'bidang' => 'required',
        ]);

        try {
            Skema::create([
                'nama' => $request->nama,
                'kode' => $request->kode,
                'kategori' => $request->kategori,
                'bidang' => $request->bidang,
            ]);

            return redirect()->route('admin.skema.index')->with('success', 'Skema berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('admin.skema.index')->with('error', 'Skema gagal ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $skema = Skema::find($id);
        return view('components.pages.admin.skema.edit', compact('skema'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lists = $this->getMenuListAdmin('skema');
        $skema = Skema::find($id);
        return view('components.pages.admin.skema.edit', compact('lists', 'skema'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => 'required',
            'kode' => 'required|unique:skema,kode,' . $id,
            'kategori' => 'required',
            'bidang' => 'required',
        ]);

        try {
            $skema = Skema::find($id);
            $skema->update($request->all());
            return redirect()->route('admin.skema.index')->with('success', 'Skema berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('admin.skema.index')->with('error', 'Skema gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $skema = Skema::find($id);
            $skema->delete();
            return redirect()->route('admin.skema.index')->with('success', 'Skema berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.skema.index')->with('error', 'Skema gagal dihapus');
        }
    }
}
