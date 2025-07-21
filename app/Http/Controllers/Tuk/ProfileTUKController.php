<?php

namespace App\Http\Controllers\Tuk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MenuTrait;
use Illuminate\Support\Facades\Auth;

class ProfileTUKController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $lists = $this->getMenuListKepalaTuk('profil-tuk');
        return view('components.pages.profile', compact('lists', 'user'));
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
