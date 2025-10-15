<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Skema;
use App\Models\AsesorSkema;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkemaController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $skema = Skema::with('asesors')->orderBy('nama', 'asc')->get();
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
        $asesors = User::where('user_type', 'asesor')
            ->orderBy('name', 'asc')
            ->get();
        return view('components.pages.admin.skema.create', compact('lists', 'activeMenu', 'asesors'));
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
            'asesors' => 'nullable|array',
            'asesors.*' => 'exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $skema = Skema::create([
                'nama' => $request->nama,
                'kode' => $request->kode,
                'kategori' => $request->kategori,
                'bidang' => $request->bidang,
            ]);

            // Assign asesor ke skema (many-to-many)
            if ($request->has('asesors') && is_array($request->asesors)) {
                foreach ($request->asesors as $asesorId) {
                    AsesorSkema::create([
                        'skema_id' => $skema->id,
                        'asesor_id' => $asesorId,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.skema.index')->with('success', 'Skema berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.skema.create')->withInput()->with('error', 'Skema gagal ditambahkan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $skema = Skema::with('asesors')->find($id);
        $lists = $this->getMenuListAdmin('skema');
        $activeMenu = 'skema';
        $asesors = User::where('user_type', 'asesor')
            ->orderBy('name', 'asc')
            ->get();
        return view('components.pages.admin.skema.edit', compact('lists', 'activeMenu', 'skema', 'asesors'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lists = $this->getMenuListAdmin('skema');
        $activeMenu = 'skema';
        $skema = Skema::with('asesors')->find($id);
        $asesors = User::where('user_type', 'asesor')
            ->orderBy('name', 'asc')
            ->get();
        return view('components.pages.admin.skema.edit', compact('lists', 'activeMenu', 'skema', 'asesors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => 'required',
            'kode' => 'required|unique:skema,kode,' . $id . ',id,deleted_at,NULL',
            'kategori' => 'required',
            'bidang' => 'required',
            'asesors' => 'nullable|array',
            'asesors.*' => 'exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $skema = Skema::find($id);
            $skema->update([
                'nama' => $request->nama,
                'kode' => $request->kode,
                'kategori' => $request->kategori,
                'bidang' => $request->bidang,
            ]);

            // Update asesor assignments (many-to-many)
            AsesorSkema::where('skema_id', $skema->id)->delete();

            if ($request->has('asesors') && is_array($request->asesors)) {
                foreach ($request->asesors as $asesorId) {
                    AsesorSkema::create([
                        'skema_id' => $skema->id,
                        'asesor_id' => $asesorId,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.skema.index')->with('success', 'Skema berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.skema.edit', $id)->withInput()->with('error', 'Skema gagal diubah: ' . $e->getMessage());
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
