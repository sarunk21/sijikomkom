<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('components.templates.master-layout');
});
