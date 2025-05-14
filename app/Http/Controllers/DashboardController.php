<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $lists = getMenuList('dashboard');
        return view('components.pages.admin.dashboard', compact('lists'));
    }
}
