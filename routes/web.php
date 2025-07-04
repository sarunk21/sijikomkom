<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\APL2Controller;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\PendaftaranController;
use App\Http\Controllers\Admin\PembayaranAsesorController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SkemaController;
use App\Http\Controllers\Admin\TUKController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UploadSertifikatAdminController;

use App\Http\Controllers\Asesi\DaftarUjikomController;
use App\Http\Controllers\Asesi\InformasiPembayaranController;
use App\Http\Controllers\Asesi\UploadSertifikatController;
use App\Http\Controllers\Asesi\ProfilAsesiController;
use App\Http\Controllers\Asesi\SertifikasiController;
use App\Http\Controllers\Asesi\UjikomController;

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

Route::group(['middleware' => 'user.type'], function () {
    Route::resource('profile', ProfileController::class)->names('profile');
});

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
    Route::resource('upload-sertifikat', UploadSertifikatAdminController::class)->names('admin.upload-sertifikat-admin');
    Route::get('apl-2/create/question/{skema_id}', [APL2Controller::class, 'create'])->name('admin.apl-2.create.question');
    Route::resource('apl-2', APL2Controller::class)->names('admin.apl-2');
    Route::resource('admin-profile', AdminProfileController::class)->names('admin.profile');
});

Route::group(['prefix' => 'asesi', 'middleware' => 'user.type'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.asesi');
    Route::resource('informasi-pembayaran', InformasiPembayaranController::class)->names('asesi.informasi-pembayaran');
    Route::resource('upload-sertifikat', UploadSertifikatController::class)->names('asesi.upload-sertifikat');
    Route::resource('profil-asesi', ProfilAsesiController::class)->names('asesi.profil-asesi');
    Route::resource('daftar-ujikom', DaftarUjikomController::class)->names('asesi.daftar-ujikom');
    Route::resource('sertifikasi', SertifikasiController::class)->names('asesi.sertifikasi');
    Route::post('ujikom/jawaban/{id}', [UjikomController::class, 'store'])->name('asesi.ujikom.store.jawaban');
    Route::resource('ujikom', UjikomController::class)->names('asesi.ujikom');
});

Route::group(['prefix' => 'asesor', 'middleware' => 'user.type'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.asesor');
    Route::resource('verifikasi-peserta', VerifikasiPesertaController::class)->names('asesor.verifikasi-peserta');
    Route::get('verifikasi-peserta/show-asesi/{jadwalId}', [VerifikasiPesertaController::class, 'showAsesi'])->name('asesor.verifikasi-peserta.show-asesi');
    Route::match(['PUT', 'DELETE'], 'verifikasi-peserta/update-status/{jadwalId}', [VerifikasiPesertaController::class, 'updateStatus'])->name('asesor.verifikasi-peserta.update-status');
    Route::resource('pembayaran-jasa', PembayaranJasaController::class)->names('asesor.pembayaran-jasa');
    Route::resource('hasil-ujikom', HasilUjikomController::class)->names('asesor.hasil-ujikom');
    Route::get('hasil-ujikom/show-jawaban-asesi/{id}', [HasilUjikomController::class, 'showJawabanAsesi'])->name('asesor.hasil-ujikom.show-jawaban-asesi');
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
