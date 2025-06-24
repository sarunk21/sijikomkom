<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('name', 'asc')->get();
        $lists = $this->getMenuListAdmin('user');
        return view('components.pages.admin.user.list', compact('lists', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lists = $this->getMenuListAdmin('user');
        $activeMenu = 'user';
        return view('components.pages.admin.user.create', compact('lists', 'activeMenu'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'nik' => 'required|unique:users,nik,NULL,id,deleted_at,NULL',
            'telephone' => 'required|unique:users,telephone,NULL,id,deleted_at,NULL',
            'user_type' => 'required|in:asesi,asesor,kaprodi,pimpinan,admin',
            'alamat' => 'required',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->email),
                'nik' => $request->nik,
                'telephone' => $request->telephone,
                'alamat' => $request->alamat,
                'user_type' => $request->user_type,
            ]);
            return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('admin.user.index')->with('error', 'User gagal ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        $lists = $this->getMenuListAdmin('user');
        $activeMenu = 'user';
        return view('components.pages.admin.user.edit', compact('lists', 'activeMenu', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lists = $this->getMenuListAdmin('user');
        $activeMenu = 'user';
        $user = User::find($id);
        return view('components.pages.admin.user.edit', compact('lists', 'activeMenu', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'nik' => 'required|unique:users,nik,NULL,id,deleted_at,NULL',
            'telephone' => 'required|unique:users,telephone,NULL,id,deleted_at,NULL',
            'alamat' => 'required',
        ]);

        try {
            $user = User::find($id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->email),
                'nik' => $request->nik,
                'telephone' => $request->telephone,
                'alamat' => $request->alamat,
                'user_type' => $request->user_type,
            ]);
            return redirect()->route('admin.user.index')->with('success', 'User berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('admin.user.index')->with('error', 'User gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        try {
            $user->delete();
            return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.user.index')->with('error', 'User gagal dihapus');
        }
    }
}
