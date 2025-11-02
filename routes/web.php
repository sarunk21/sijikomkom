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
use App\Http\Controllers\Admin\TestingController;
use App\Http\Controllers\Admin\AdminTemplateController;

use App\Http\Controllers\Asesi\DaftarUjikomController;
use App\Http\Controllers\Asesi\CustomDataController;
use App\Http\Controllers\Asesi\DashboardController as AsesiDashboardController;
use App\Http\Controllers\Asesi\InformasiPembayaranController;
use App\Http\Controllers\Asesi\UploadSertifikatController;
use App\Http\Controllers\Asesi\ProfilAsesiController;
use App\Http\Controllers\Asesi\SertifikasiController;
use App\Http\Controllers\Asesi\UjikomController;
use App\Http\Controllers\Asesi\TemplateController;
use App\Http\Controllers\Asesi\Apl2Controller as AsesiApl2Controller;

use App\Http\Controllers\Asesor\VerifikasiPesertaController;
use App\Http\Controllers\Asesor\DashboardController as AsesorDashboardController;
use App\Http\Controllers\Asesor\PembayaranJasaController;
use App\Http\Controllers\Asesor\HasilUjikomController;
use App\Http\Controllers\Asesor\ProfilAsesorController;
use App\Http\Controllers\Asesor\Apl2Controller as AsesorApl2Controller;

use App\Http\Controllers\Kaprodi\ReportHasilUjiController;
use App\Http\Controllers\Kaprodi\VerifikasiPendaftaranController;
use App\Http\Controllers\Kaprodi\ProfilKaprodiController;

use App\Http\Controllers\Pimpinan\ReportPimpinanController;
use App\Http\Controllers\Pimpinan\LaporanIKUController;
use App\Http\Controllers\Pimpinan\ProfilPimpinanController;

use App\Http\Controllers\Tuk\KonfirmasiJadwalController;
use App\Http\Controllers\Tuk\DashboardController as TukDashboardController;
use App\Http\Controllers\Tuk\ProfileTUKController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;

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
    // Import User (CSV)
    Route::post('user/import', [UserController::class, 'import'])->name('admin.user.import');
    Route::get('user/import/template', [UserController::class, 'downloadTemplate'])->name('admin.user.import.template');
    Route::get('user/import/template-excel', [UserController::class, 'downloadTemplateExcel'])->name('admin.user.import.template.excel');
    Route::post('user/nonaktifkan/{id}', [UserController::class, 'nonaktifkan'])->name('admin.user.nonaktifkan');
    Route::post('user/aktifkan/{id}', [UserController::class, 'aktifkan'])->name('admin.user.aktifkan');
    Route::resource('pendaftaran', PendaftaranController::class)->names('admin.pendaftaran');
    Route::resource('pembayaran-asesi', PembayaranController::class)->names('admin.pembayaran-asesi');
    Route::resource('pembayaran-asesor', PembayaranAsesorController::class)->names('admin.pembayaran-asesor');
    Route::get('pembayaran-asesor/{id}/download', [PembayaranAsesorController::class, 'download'])->name('admin.pembayaran-asesor.download');
    Route::resource('verifikasi-pembayaran', App\Http\Controllers\Admin\VerifikasiPembayaranController::class)->names('admin.verifikasi-pembayaran');
    Route::post('verifikasi-pembayaran/{id}/approve', [App\Http\Controllers\Admin\VerifikasiPembayaranController::class, 'approve'])->name('admin.verifikasi-pembayaran.approve');
    Route::post('verifikasi-pembayaran/{id}/reject', [App\Http\Controllers\Admin\VerifikasiPembayaranController::class, 'reject'])->name('admin.verifikasi-pembayaran.reject');
    Route::resource('template-master', AdminTemplateController::class)->names('admin.template-master');
    Route::get('template-master/{id}/download', [AdminTemplateController::class, 'download'])->name('admin.template-master.download');
    Route::post('template-master/{id}/toggle-status', [AdminTemplateController::class, 'toggleStatus'])->name('admin.template-master.toggle-status');
    Route::resource('report', ReportController::class)->names('admin.report');
    Route::resource('upload-sertifikat', UploadSertifikatAdminController::class)->names('admin.upload-sertifikat-admin');
    // APL2 Template routes (harus sebelum resource)
    Route::get('apl-2/template', [APL2Controller::class, 'templateIndex'])->name('admin.apl-2.template.index');
    Route::get('apl-2/template/create', [APL2Controller::class, 'templateCreate'])->name('admin.apl-2.template.create');
    Route::get('apl-2/template/{id}/edit', [APL2Controller::class, 'templateEdit'])->name('admin.apl-2.template.edit');
    Route::post('apl-2/template', [APL2Controller::class, 'templateStore'])->name('admin.apl-2.template.store');
    Route::put('apl-2/template/{id}', [APL2Controller::class, 'templateUpdate'])->name('admin.apl-2.template.update');

    // APL2 Resource routes
    Route::get('apl-2/create/question/{skema_id}', [APL2Controller::class, 'create'])->name('admin.apl-2.create.question');
    Route::get('apl-2/skema/{skema_id}', [APL2Controller::class, 'showBySkema'])->name('admin.apl-2.show-by-skema');
    Route::resource('apl-2', APL2Controller::class)->names('admin.apl-2');
    Route::resource('admin-profile', AdminProfileController::class)->names('admin.profile');

    // Analytics routes
    Route::get('analytics/dashboard-data', [AnalyticsController::class, 'getDashboardData'])->name('admin.analytics.dashboard-data');
    Route::post('analytics/clear-cache', [AnalyticsController::class, 'clearCache'])->name('admin.analytics.clear-cache');

    // Analytics API routes (migrated from Python)
    Route::prefix('analytics')->group(function () {
        Route::get('/', [AnalyticsController::class, 'root'])->name('admin.analytics.root');
        Route::get('/health', [AnalyticsController::class, 'healthCheck'])->name('admin.analytics.health');
        Route::get('/skema-trend', [AnalyticsController::class, 'skemaTrend'])->name('admin.analytics.skema-trend');
        Route::get('/kompetensi-skema', [AnalyticsController::class, 'kompetensiSkema'])->name('admin.analytics.kompetensi-skema');
        Route::get('/segmentasi-demografi', [AnalyticsController::class, 'segmentasiDemografi'])->name('admin.analytics.segmentasi-demografi');
        Route::get('/workload-asesor', [AnalyticsController::class, 'workloadAsesor'])->name('admin.analytics.workload-asesor');
        Route::get('/tren-peminat-skema', [AnalyticsController::class, 'trenPeminatSkema'])->name('admin.analytics.tren-peminat-skema');
        Route::get('/dashboard-summary', [AnalyticsController::class, 'dashboardSummary'])->name('admin.analytics.dashboard-summary');
        Route::get('/debug-tables', [AnalyticsController::class, 'debugTables'])->name('admin.analytics.debug-tables');
    });

    // Testing routes
    Route::get('testing', [TestingController::class, 'index'])->name('admin.testing');
    Route::post('testing/update-status-pendaftaran', [TestingController::class, 'updateStatusPendaftaran'])->name('admin.testing.update-status-pendaftaran');
    Route::post('testing/trigger-distribusi', [TestingController::class, 'triggerDistribusi'])->name('admin.testing.trigger-distribusi');
    Route::post('testing/start-jadwal', [TestingController::class, 'startJadwal'])->name('admin.testing.start-jadwal');
    Route::post('testing/simulasi-ujikom', [TestingController::class, 'simulasiUjikom'])->name('admin.testing.simulasi-ujikom');
    Route::post('testing/selesaikan-ujikom', [TestingController::class, 'selesaikanUjikom'])->name('admin.testing.selesaikan-ujikom');
    Route::post('testing/selesaikan-jadwal', [TestingController::class, 'selesaikanJadwal'])->name('admin.testing.selesaikan-jadwal');
    Route::post('testing/trigger-pembayaran-asesor', [TestingController::class, 'triggerPembayaranAsesor'])->name('admin.testing.trigger-pembayaran-asesor');
    Route::post('testing/upload-sertifikat', [TestingController::class, 'uploadSertifikat'])->name('admin.testing.upload-sertifikat');
    Route::post('testing/fix-stuck-payments', [TestingController::class, 'fixStuckPayments'])->name('admin.testing.fix-stuck-payments');
});

Route::group(['prefix' => 'asesi', 'middleware' => 'user.type'], function () {
    Route::get('/', [AsesiDashboardController::class, 'index'])->name('dashboard.asesi');
    Route::resource('informasi-pembayaran', InformasiPembayaranController::class)->names('asesi.informasi-pembayaran');
    Route::resource('upload-sertifikat', UploadSertifikatController::class)->names('asesi.upload-sertifikat');
    Route::resource('profil-asesi', ProfilAsesiController::class)->names('asesi.profil-asesi');
    Route::resource('daftar-ujikom', DaftarUjikomController::class)->names('asesi.daftar-ujikom')->middleware('check.second.registration');
    Route::resource('sertifikasi', SertifikasiController::class)->names('asesi.sertifikasi');

    // APL 1 routes
    Route::get('template/apl1/{pendaftaranId}', [TemplateController::class, 'showApl1Form'])->name('asesi.template.apl1-form');
    Route::post('template/apl1/{pendaftaranId}', [TemplateController::class, 'storeApl1CustomData'])->name('asesi.template.apl1-store');
    Route::get('template/apl1/{pendaftaranId}/download', [TemplateController::class, 'downloadApl1'])->name('asesi.template.apl1-download');
    Route::get('template/preview-apl1-data/{pendaftaranId}', [TemplateController::class, 'previewApl1Data'])->name('asesi.template.preview-apl1-data');

    // APL2 routes
    Route::get('sertifikasi/{id}/apl2', [SertifikasiController::class, 'show'])->name('asesi.sertifikasi.apl2');
    Route::post('sertifikasi/{id}/apl2', [SertifikasiController::class, 'storeApl2'])->name('asesi.sertifikasi.store-apl2');
    Route::get('sertifikasi/{id}/apl2/generate', [SertifikasiController::class, 'generateApl2'])->name('asesi.sertifikasi.generate-apl2');

    // Custom data routes
    Route::get('custom-data/{pendaftaranId}', [CustomDataController::class, 'showForm'])->name('asesi.custom-data.show');
    Route::post('custom-data/{pendaftaranId}', [CustomDataController::class, 'store'])->name('asesi.custom-data.store');
    Route::post('ujikom/jawaban/{id}', [UjikomController::class, 'store'])->name('asesi.ujikom.store.jawaban');
    Route::resource('ujikom', UjikomController::class)->names('asesi.ujikom');

    // Registration info route
    Route::get('registration-info', [App\Http\Controllers\Asesi\RegistrationInfoController::class, 'index'])->name('asesi.registration-info');
});

Route::group(['prefix' => 'asesor', 'middleware' => 'user.type'], function () {
    Route::get('/', [AsesorDashboardController::class, 'index'])->name('dashboard.asesor');
    Route::resource('verifikasi-peserta', VerifikasiPesertaController::class)->names('asesor.verifikasi-peserta');
    Route::get('verifikasi-peserta/show-asesi/{jadwalId}', [VerifikasiPesertaController::class, 'showAsesi'])->name('asesor.verifikasi-peserta.show-asesi');
    Route::match(['PUT', 'DELETE'], 'verifikasi-peserta/update-status/{jadwalId}', [VerifikasiPesertaController::class, 'updateStatus'])->name('asesor.verifikasi-peserta.update-status');
    Route::resource('pembayaran-jasa', PembayaranJasaController::class)->names('asesor.pembayaran-jasa');
    Route::resource('hasil-ujikom', HasilUjikomController::class)->names('asesor.hasil-ujikom');
    Route::get('hasil-ujikom/show-jawaban-asesi/{id}', [HasilUjikomController::class, 'showJawabanAsesi'])->name('asesor.hasil-ujikom.show-jawaban-asesi');
    Route::resource('profil-asesor', ProfilAsesorController::class)->names('asesor.profil-asesor');

    // APL2 routes
    Route::resource('apl2', AsesorApl2Controller::class)->names('asesor.apl2');
    Route::post('apl2/add-signature', [AsesorApl2Controller::class, 'addSignature'])->name('asesor.apl2.add-signature');
    Route::get('apl2/preview-data/{pendaftaranId}', [AsesorApl2Controller::class, 'previewApl2Data'])->name('asesor.apl2.preview-data');
    Route::get('apl2/export-docx/{pendaftaranId}', [AsesorApl2Controller::class, 'exportDocx'])->name('asesor.apl2.export-docx');

    // APL2 Template routes for asesor
    Route::get('apl2/template', [AsesorApl2Controller::class, 'templateIndex'])->name('asesor.apl2.template.index');
});

Route::group(['prefix' => 'kaprodi', 'middleware' => 'user.type'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.kaprodi');
    Route::resource('report-hasil-uji', ReportHasilUjiController::class)->names('kaprodi.report-hasil-uji');
    Route::get('report-hasil-uji/list-nama-kompeten/{id}', [ReportHasilUjiController::class, 'listNamaKompeten'])->name('kaprodi.report-hasil-uji.list-nama-kompeten');
    Route::get('report-hasil-uji/list-nama-tidak-kompeten/{id}', [ReportHasilUjiController::class, 'listNamaTidakKompeten'])->name('kaprodi.report-hasil-uji.list-nama-tidak-kompeten');
    Route::resource('verifikasi-pendaftaran', VerifikasiPendaftaranController::class)->names('kaprodi.verifikasi-pendaftaran');
    Route::resource('profil-kaprodi', ProfilKaprodiController::class)->names('kaprodi.profil-kaprodi');

    // Analytics routes
    Route::get('analytics/dashboard-data', [AnalyticsController::class, 'getDashboardData'])->name('kaprodi.analytics.dashboard-data');
    Route::post('analytics/clear-cache', [AnalyticsController::class, 'clearCache'])->name('kaprodi.analytics.clear-cache');

    // Analytics API routes (migrated from Python)
    Route::prefix('analytics')->group(function () {
        Route::get('/', [AnalyticsController::class, 'root'])->name('kaprodi.analytics.root');
        Route::get('/health', [AnalyticsController::class, 'healthCheck'])->name('kaprodi.analytics.health');
        Route::get('/skema-trend', [AnalyticsController::class, 'skemaTrend'])->name('kaprodi.analytics.skema-trend');
        Route::get('/kompetensi-skema', [AnalyticsController::class, 'kompetensiSkema'])->name('kaprodi.analytics.kompetensi-skema');
        Route::get('/segmentasi-demografi', [AnalyticsController::class, 'segmentasiDemografi'])->name('kaprodi.analytics.segmentasi-demografi');
        Route::get('/workload-asesor', [AnalyticsController::class, 'workloadAsesor'])->name('kaprodi.analytics.workload-asesor');
        Route::get('/tren-peminat-skema', [AnalyticsController::class, 'trenPeminatSkema'])->name('kaprodi.analytics.tren-peminat-skema');
        Route::get('/dashboard-summary', [AnalyticsController::class, 'dashboardSummary'])->name('kaprodi.analytics.dashboard-summary');
        Route::get('/debug-tables', [AnalyticsController::class, 'debugTables'])->name('kaprodi.analytics.debug-tables');
    });
});

Route::group(['prefix' => 'pimpinan', 'middleware' => 'user.type'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.pimpinan');
    Route::resource('report-pimpinan', ReportPimpinanController::class)->names('pimpinan.report-pimpinan');
    Route::resource('laporan-iku', LaporanIKUController::class)->names('pimpinan.laporan-iku');
    Route::resource('profil-pimpinan', ProfilPimpinanController::class)->names('pimpinan.profil-pimpinan');

    // Analytics routes
    Route::get('analytics/dashboard-data', [AnalyticsController::class, 'getDashboardData'])->name('pimpinan.analytics.dashboard-data');
    Route::post('analytics/clear-cache', [AnalyticsController::class, 'clearCache'])->name('pimpinan.analytics.clear-cache');

    // Analytics API routes (migrated from Python)
    Route::prefix('analytics')->group(function () {
        Route::get('/', [AnalyticsController::class, 'root'])->name('pimpinan.analytics.root');
        Route::get('/health', [AnalyticsController::class, 'healthCheck'])->name('pimpinan.analytics.health');
        Route::get('/skema-trend', [AnalyticsController::class, 'skemaTrend'])->name('pimpinan.analytics.skema-trend');
        Route::get('/kompetensi-skema', [AnalyticsController::class, 'kompetensiSkema'])->name('pimpinan.analytics.kompetensi-skema');
        Route::get('/segmentasi-demografi', [AnalyticsController::class, 'segmentasiDemografi'])->name('pimpinan.analytics.segmentasi-demografi');
        Route::get('/workload-asesor', [AnalyticsController::class, 'workloadAsesor'])->name('pimpinan.analytics.workload-asesor');
        Route::get('/tren-peminat-skema', [AnalyticsController::class, 'trenPeminatSkema'])->name('pimpinan.analytics.tren-peminat-skema');
        Route::get('/dashboard-summary', [AnalyticsController::class, 'dashboardSummary'])->name('pimpinan.analytics.dashboard-summary');
        Route::get('/debug-tables', [AnalyticsController::class, 'debugTables'])->name('pimpinan.analytics.debug-tables');
    });
});

Route::group(['prefix' => 'tuk', 'middleware' => 'user.type'], function () {
    Route::get('/', [TukDashboardController::class, 'index'])->name('dashboard.tuk');
    Route::resource('konfirmasi-jadwal', KonfirmasiJadwalController::class)->names('tuk.konfirmasi-jadwal');
    Route::resource('profil-tuk', ProfileTUKController::class)->names('tuk.profil-tuk');
});

// API Routes (untuk AJAX requests)
Route::prefix('api')->group(function () {
    Route::get('/skema', [SkemaController::class, 'apiList'])->name('api.skema.list');
});
