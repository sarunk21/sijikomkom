<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TUKController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = getMenuList('tuk');
        $activeMenu = 'tuk';
        return view('components.pages.admin.tuk.list', compact('lists', 'activeMenu'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lists = getMenuList('tuk');
        $activeMenu = 'tuk';
        return view('components.pages.admin.tuk.create', compact('lists', 'activeMenu'));
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
        $lists = getMenuList('tuk');
        $activeMenu = 'tuk';
        return view('components.pages.admin.tuk.edit', compact('lists', 'activeMenu'));
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
