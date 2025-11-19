<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
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

        // Get jadwal yang sudah dimulai untuk asesi ini
        // Status 3 = Ujian Berlangsung
        $jadwals = Pendaftaran::where('user_id', $asesi->id)
            ->whereHas('jadwal', function($query) {
                $query->where('status', 3); // 3 = Ujian Berlangsung
            })
            ->with(['jadwal', 'jadwal.skema', 'jadwal.tuk'])
            ->get()
            ->pluck('jadwal')
            ->unique('id');

        return view('components.pages.asesi.ujikom.list', compact('lists', 'jadwals'));
    }
}
