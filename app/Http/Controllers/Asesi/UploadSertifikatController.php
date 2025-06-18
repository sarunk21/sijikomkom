<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;

class UploadSertifikatController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListAsesi('upload-sertifikat');
        // Assuming you have a model for HasilUjikom, you can fetch the data here
        // $hasilUjikom = HasilUjikom::all();
        // For now, we'll just pass an empty array
        $uploadSertifikat = [];

        return view('components.pages.asesi.upload-sertifikat.list', compact('lists', 'uploadSertifikat'));
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
