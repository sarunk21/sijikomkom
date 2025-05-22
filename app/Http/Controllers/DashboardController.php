<?php

namespace App\Http\Controllers;

use App\Traits\MenuTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use MenuTrait;

    public function index()
    {
        $lists = $this->getMenuListAdmin('dashboard');
        return view('components.pages.admin.dashboard', compact('lists'));
    }
}
