<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\APL2;
use App\Models\Pendaftaran;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UjikomController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $asesi = Auth::user();
        $lists = $this->getMenuListAsesi('ujikom');
        $pendaftaran = Pendaftaran::where('user_id', $asesi->id)->get();

        return view('components.pages.asesi.ujikom.list', compact('lists', 'pendaftaran'));
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
        $asesi = Auth::user();
        $pendaftaran = Pendaftaran::where('user_id', $asesi->id)->first();
        $apl2 = APL2::where('skema_id', $pendaftaran->skema_id)->get();
        $lists = $this->getMenuListAsesi('ujikom');

        return view('components.pages.asesi.ujikom.show', compact('pendaftaran', 'lists', 'apl2'));
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
