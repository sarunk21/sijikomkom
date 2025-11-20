<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Skema;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;

class SkemaController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of all skema with their descriptions
     */
    public function index()
    {
        $lists = $this->getMenuListAsesi('skema');

        $skemas = Skema::orderBy('created_at', 'desc')->get();

        return view('components.pages.asesi.skema.index', compact('lists', 'skemas'));
    }

    /**
     * Display the specified skema detail
     */
    public function show($id)
    {
        $lists = $this->getMenuListAsesi('skema');

        $skema = Skema::findOrFail($id);

        return view('components.pages.asesi.skema.show', compact('lists', 'skema'));
    }
}
