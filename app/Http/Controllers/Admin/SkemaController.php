<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        $asesor = User::where('user_type', 'asesor')->where('skema_id', null)->get();
        return view('components.pages.admin.skema.create', compact('lists', 'activeMenu', 'asesor'));
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
            'asesor_id' => 'array',
        ]);

        try {
            $skema = Skema::create([
                'nama' => $request->nama,
                'kode' => $request->kode,
                'kategori' => $request->kategori,
                'bidang' => $request->bidang,
            ]);

            foreach ($request->asesor_id as $asesor_id) {
                User::where('id', $asesor_id)->update(['skema_id' => $skema->id]);
            }

            return redirect()->route('admin.skema.index')->with('success', 'Skema berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('admin.skema.create')->withInput()->with('error', 'Skema gagal ditambahkan');
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
        $asesor = User::where('user_type', 'asesor')->get();
        $asesor_skema = User::where('user_type', 'asesor')->where('skema_id', $skema->id)->get();
        return view('components.pages.admin.skema.edit', compact('lists', 'skema', 'asesor', 'asesor_skema'));
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
            'asesor_id' => 'array',
        ]);

        try {
            $skema = Skema::find($id);
            $skema->update($request->all());

            // Kosongkan asesor_id yang lama
            User::where('skema_id', $skema->id)->update(['skema_id' => null]);

            // Tambahkan asesor_id yang baru
            foreach ($request->asesor_id as $asesor_id) {
                User::where('id', $asesor_id)->update(['skema_id' => $skema->id]);
            }

            return redirect()->route('admin.skema.index')->with('success', 'Skema berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('admin.skema.index')->withInput()->with('error', 'Skema gagal diubah');
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
            User::where('skema_id', $skema->id)->update(['skema_id' => null]);
            return redirect()->route('admin.skema.index')->with('success', 'Skema berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.skema.index')->with('error', 'Skema gagal dihapus');
        }
    }
}
