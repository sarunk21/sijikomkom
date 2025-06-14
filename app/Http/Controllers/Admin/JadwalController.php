<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Skema;
use App\Models\Tuk;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jadwal = Jadwal::orderBy('created_at', 'desc')->get();
        $lists = $this->getMenuListAdmin('jadwal');
        $activeMenu = 'jadwal';
        return view('components.pages.admin.jadwal.list', compact('lists', 'activeMenu', 'jadwal'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lists = $this->getMenuListAdmin('jadwal');
        $skema = Skema::orderBy('created_at', 'desc')->get();
        $tuk = Tuk::orderBy('created_at', 'desc')->get();
        $activeMenu = 'jadwal';
        return view('components.pages.admin.jadwal.create', compact('lists', 'activeMenu', 'skema', 'tuk'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'skema_id' => 'required',
            'tuk_id' => 'required',
            'tanggal_ujian' => 'required',
            'status' => 'required',
            'kuota' => 'required',
        ]);

        try {
            $jadwal = Jadwal::create([
                'skema_id' => $request->skema_id,
                'tuk_id' => $request->tuk_id,
                'tanggal_ujian' => $request->tanggal_ujian,
                'status' => $request->status,
                'kuota' => $request->kuota,
            ]);

            return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('admin.jadwal.index')->withInput()->with('error', 'Jadwal gagal ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jadwal = Jadwal::find($id);
        $skema = Skema::orderBy('created_at', 'desc')->get();
        $tuk = Tuk::orderBy('created_at', 'desc')->get();
        $activeMenu = 'jadwal';
        return view('components.pages.admin.jadwal.edit', compact('lists', 'activeMenu', 'jadwal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lists = $this->getMenuListAdmin('jadwal');
        $jadwal = Jadwal::find($id);
        $skema = Skema::orderBy('created_at', 'desc')->get();
        $tuk = Tuk::orderBy('created_at', 'desc')->get();
        $activeMenu = 'jadwal';
        return view('components.pages.admin.jadwal.edit', compact('lists', 'activeMenu', 'jadwal', 'skema', 'tuk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'skema_id' => 'required',
            'tuk_id' => 'required',
            'tanggal_ujian' => 'required',
            'status' => 'required',
            'kuota' => 'required',
        ]);

        try {
            $jadwal = Jadwal::find($id);
            $jadwal->update($request->all());
            return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('admin.jadwal.index')->withInput()->with('error', 'Jadwal gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $jadwal = Jadwal::find($id);
            $jadwal->delete();
            return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.jadwal.index')->with('error', 'Jadwal gagal dihapus');
        }
    }
}
