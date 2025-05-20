<?php

use App\Http\Controllers\Admin\APL2Controller;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\PendaftaranController;
use App\Http\Controllers\Admin\PembayaranAsesorController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SkemaController;
use App\Http\Controllers\Admin\TUKController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\DashboardController;

use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.admin');

Route::get('/login', function () {
    return view('components.pages.login');
})->name('login');

Route::group(['prefix' => 'admin'], function () {
    Route::resource('skema', SkemaController::class)->names('admin.skema');
    Route::resource('tuk', TUKController::class)->names('admin.tuk');
    Route::resource('jadwal', JadwalController::class)->names('admin.jadwal');
    Route::resource('user', UserController::class)->names('admin.user');
    Route::resource('pembayaran', PembayaranController::class)->names('admin.pembayaran');
    Route::resource('pendaftaran', PendaftaranController::class)->names('admin.pendaftaran');
    Route::resource('pembayaran-asesor', PembayaranAsesorController::class)->names('admin.pembayaran-asesor');
    Route::resource('report', ReportController::class)->names('admin.report');
    Route::resource('apl-2', APL2Controller::class)->names('admin.apl-2');
});
