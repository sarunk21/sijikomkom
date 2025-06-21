<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;

class ReportHasilUjiController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListKaprodi('report-hasil-uji');

        $reports = Jadwal::where('status', 4)
            ->with(['skema', 'tuk'])
            ->orderBy('tanggal_ujian', 'asc')
            ->get();

        return view('components.pages.kaprodi.report-hasil-uji.list', compact('lists', 'reports'));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
