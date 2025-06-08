<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $lists = $this->getMenuListAdmin('profile');
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
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id . ',id,deleted_at,NULL',
            'nik' => 'required|unique:users,nik,NULL,id,deleted_at,NULL',
            'telephone' => 'required|unique:users,telephone,NULL,id,deleted_at,NULL',
            'alamat' => 'required',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->email),
            'nik' => $request->nik,
            'telephone' => $request->telephone,
            'alamat' => $request->alamat,
        ]);
        return redirect()->route('admin.profile.index')->with('success', 'Profile berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
