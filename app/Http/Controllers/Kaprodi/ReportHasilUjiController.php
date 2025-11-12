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
    public function index(Request $request)
    {
        $lists = $this->getMenuListKaprodi('report-hasil-uji');

        $query = Jadwal::where('status', 4)
            ->with(['skema', 'tuk']);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_ujian', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_ujian', '<=', $request->end_date);
        }

        if ($request->filled('skema_id')) {
            $query->where('skema_id', $request->skema_id);
        }

        if ($request->filled('tuk_id')) {
            $query->where('tuk_id', $request->tuk_id);
        }

        $reports = $query->orderBy('tanggal_ujian', 'asc')->get();

        // Get all skema and tuk for filter dropdowns
        $skemas = \App\Models\Skema::orderBy('nama', 'asc')->get();
        $tuks = \App\Models\Tuk::orderBy('nama', 'asc')->get();

        return view('components.pages.kaprodi.report-hasil-uji.list', compact('lists', 'reports', 'skemas', 'tuks'));
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

    public function listNamaKompeten(string $id)
    {
        $lists = $this->getMenuListKaprodi('report-hasil-uji');

        $reports = Jadwal::find($id)->jumlah_kompeten()->get();
        $reports = $reports->map(function ($report) {
            return [
                'skema' => $report->skema->nama,
                'nama' => $report->user->name,
                'nim' => $report->user->nim,
            ];
        });

        return view('components.pages.kaprodi.report-hasil-uji.list-nama-kompeten', compact('lists', 'reports'));
    }

    public function listNamaTidakKompeten(string $id)
    {
        $lists = $this->getMenuListKaprodi('report-hasil-uji');

        $reports = Jadwal::find($id)->jumlah_tidak_kompeten()->get();
        $reports = $reports->map(function ($report) {
            return [
                'skema' => $report->skema->nama,
                'nama' => $report->user->name,
                'nim' => $report->user->nim,
            ];
        });

        return view('components.pages.kaprodi.report-hasil-uji.list-nama-tidak-kompeten', compact('lists', 'reports'));
    }
}
