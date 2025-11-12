<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;

class LaporanIKUController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $lists = $this->getMenuListPimpinan('laporan-iku');

        $query = Report::where('status', 1)
            ->with(['user', 'skema']);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('skema_id')) {
            $query->where('skema_id', $request->skema_id);
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        // Get all skema for filter dropdown
        $skemas = \App\Models\Skema::orderBy('nama', 'asc')->get();

        return view('components.pages.pimpinan.laporan-iku.list', compact('lists', 'reports', 'skemas'));
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
