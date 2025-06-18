<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\PembayaranAsesor;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;

class PembayaranJasaController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListAsesor('pembayaran-jasa');
        $pembayaranJasa = PembayaranAsesor::with(['jadwal', 'jadwal.skema', 'jadwal.tuk'])->get();
        return view('components.pages.asesor.pembayaran-jasa.list', compact('lists', 'pembayaranJasa'));
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
