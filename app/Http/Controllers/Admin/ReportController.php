<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Skema;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $lists = $this->getMenuListAdmin('report-ujikom');

        $query = Jadwal::where('status', 4)
            ->with(['skema', 'tuk']);

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_ujian', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_ujian', '<=', $request->tanggal_sampai);
        }

        // Filter by skema
        if ($request->filled('skema_id')) {
            $query->where('skema_id', $request->skema_id);
        }

        $reports = $query->orderBy('tanggal_ujian', 'asc')->get();
        $skemas = Skema::orderBy('nama', 'asc')->get();

        return view('components.pages.admin.report.list', compact('lists', 'reports', 'skemas'));
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
