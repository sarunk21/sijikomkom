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
        $skema = Skema::with('apl2')->orderBy('nama', 'asc')->get();
        return view('components.pages.admin.apl2.list', compact('lists', 'skema'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $skema_id)
    {
        $lists = $this->getMenuListAdmin('apl-2');
        $skema = Skema::find($skema_id);

        return view('components.pages.admin.apl2.create', compact('lists', 'skema'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'skema_id' => 'required',
            'question_text' => 'required',
        ]);

        try {
            APL2::create([
                'skema_id' => $request->skema_id,
                'question_text' => $request->question_text,
            ]);

            return redirect()->route('admin.apl-2.show', $request->skema_id)->with('success', 'APL02 berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('admin.apl-2.create.question', $request->skema_id)->withInput()->with('error', 'APL02 gagal ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $skema_id)
    {
        $lists = $this->getMenuListAdmin('apl-2');
        $questions = APL2::where('skema_id', $skema_id)->get();
        return view('components.pages.admin.apl2.show', compact('lists', 'questions', 'skema_id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lists = $this->getMenuListAdmin('apl-2');
        $apl2 = APL2::find($id);
        return view('components.pages.admin.apl2.edit', compact('lists', 'apl2'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'skema_id' => 'required',
            'question_text' => 'required',
        ]);

        try {
            $apl2 = APL2::findOrFail($id);
            $apl2->update([
                'skema_id' => $request->skema_id,
                'question_text' => $request->question_text,
            ]);

            return redirect()->route('admin.apl-2.show', $request->skema_id)->with('success', 'APL02 berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('admin.apl-2.show', $request->skema_id)->withInput()->with('error', 'APL02 gagal diperbarui');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $apl2 = APL2::findOrFail($id);
        try {
            $apl2->delete();

            return redirect()->route('admin.apl-2.show', $apl2->skema_id)->with('success', 'APL02 berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.apl-2.show', $apl2->skema_id)->with('error', 'APL02 gagal dihapus');
        }
    }
}
