<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use App\Models\Skema;
use App\Models\APL2;
use Illuminate\Http\Request;

class APL2Controller extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListAdmin('apl-2');
        $apl2 = APL2::with('skema')->orderBy('created_at', 'desc')->get();
        return view('components.pages.admin.apl2.list', compact('lists', 'apl2'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lists = $this->getMenuListAdmin('apl-2');
        $skema = Skema::orderBy('created_at', 'desc')->get();
        return view('components.pages.admin.apl2.create', compact('lists', 'skema'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'skema_id' => 'required',
            'link_ujikom_asesor' => 'required',
            'link_ujikom_asesi' => 'required',
        ]);

        try {
            APL2::create([
                'skema_id' => $request->skema_id,
                'link_ujikom_asesor' => $request->link_ujikom_asesor,
                'link_ujikom_asesi' => $request->link_ujikom_asesi,
            ]);

            return redirect()->route('admin.apl-2.index')->with('success', 'APL02 berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('admin.apl-2.create')->withInput()->with('error', 'APL02 gagal ditambahkan');
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
        $lists = $this->getMenuListAdmin('apl-2');
        $apl2 = APL2::find($id);
        $skema = Skema::orderBy('created_at', 'desc')->get();
        return view('components.pages.admin.apl2.edit', compact('lists', 'apl2', 'skema'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'skema_id' => 'required',
            'link_ujikom_asesor' => 'required',
            'link_ujikom_asesi' => 'required',
        ]);

        try {
            $apl2 = APL2::findOrFail($id);
            $apl2->update([
                'skema_id' => $request->skema_id,
                'link_ujikom_asesor' => $request->link_ujikom_asesor,
                'link_ujikom_asesi' => $request->link_ujikom_asesi,
            ]);
    
            return redirect()->route('admin.apl-2.index')->with('success', 'APL02 berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('admin.apl-2.edit', $id)->withInput()->with('error', 'APL02 gagal diperbarui');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $apl2 = APL2::find($id);
            $apl2->delete();
    
            return redirect()->route('admin.apl-2.index')->with('success', 'APL02 berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.apl-2.index')->with('error', 'APL02 gagal dihapus');
        }
    }
}
