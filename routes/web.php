<?php

use App\Http\Controllers\LoginController;

use App\Http\Controllers\Admin\APL2Controller;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\PendaftaranController;
use App\Http\Controllers\Admin\PembayaranAsesorController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SkemaController;
use App\Http\Controllers\Admin\TUKController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\Asesor\VerifikasiPesertaController;
use App\Http\Controllers\Asesor\PembayaranJasaController;
use App\Http\Controllers\Asesor\HasilUjikomController;
use App\Http\Controllers\Asesor\ProfilAsesorController;

use App\Http\Controllers\Kaprodi\ReportHasilUjiController;
use App\Http\Controllers\Kaprodi\VerifikasiPendaftaranController;
use App\Http\Controllers\Kaprodi\ProfilKaprodiController;

use App\Http\Controllers\Pimpinan\ReportPimpinanController;
use App\Http\Controllers\Pimpinan\LaporanIKUController;
use App\Http\Controllers\Pimpinan\ProfilPimpinanController;

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard.admin');
    }
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::delete('/logout', [LoginController::class, 'logout'])->name('logout');

Route::group(['prefix' => 'admin', 'middleware' => 'user.type'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.admin');
    Route::resource('skema', SkemaController::class)->names('admin.skema');
    Route::resource('tuk', TUKController::class)->names('admin.tuk');
    Route::resource('jadwal', JadwalController::class)->names('admin.jadwal');
    Route::resource('user', UserController::class)->names('admin.user');
    Route::resource('pendaftaran', PendaftaranController::class)->names('admin.pendaftaran');
    Route::resource('pembayaran-asesi', PembayaranController::class)->names('admin.pembayaran-asesi');
    Route::resource('pembayaran-asesor', PembayaranAsesorController::class)->names('admin.pembayaran-asesor');
    Route::resource('report', ReportController::class)->names('admin.report');
    Route::resource('apl-2', APL2Controller::class)->names('admin.apl-2');
    Route::resource('profile', ProfileController::class)->names('admin.profile');
});

Route::group(['prefix' => 'asesor', 'middleware' => 'user.type'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.asesor');
    Route::resource('verifikasi-peserta', VerifikasiPesertaController::class)->names('asesor.verifikasi-peserta');
    Route::resource('pembayaran-jasa', PembayaranJasaController::class)->names('asesor.pembayaran-jasa');
    Route::resource('hasil-ujikom', HasilUjikomController::class)->names('asesor.hasil-ujikom');
    Route::resource('profil-asesor', ProfilAsesorController::class)->names('asesor.profil-asesor');
});

Route::group(['prefix' => 'kaprodi', 'middleware' => 'user.type'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.kaprodi');
    Route::resource('report-hasil-uji', ReportHasilUjiController::class)->names('kaprodi.report-hasil-uji');
    Route::resource('verifikasi-pendaftaran', VerifikasiPendaftaranController::class)->names('kaprodi.verifikasi-pendaftaran');
    Route::resource('profil-kaprodi', ProfilKaprodiController::class)->names('kaprodi.profil-kaprodi');
});

Route::group(['prefix' => 'pimpinan', 'middleware' => 'user.type'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.pimpinan');
    Route::resource('report-pimpinan', ReportPimpinanController::class)->names('pimpinan.report-pimpinan');
    Route::resource('laporan-iku', LaporanIKUController::class)->names('pimpinan.laporan-iku');
    Route::resource('profil-pimpinan', ProfilPimpinanController::class)->names('pimpinan.profil-pimpinan');
});
