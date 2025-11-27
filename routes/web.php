<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\APL2Controller;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
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
use App\Http\Controllers\Admin\BankSoalController;

use App\Http\Controllers\Asesi\DaftarUjikomController;
use App\Http\Controllers\Asesi\CustomDataController;
use App\Http\Controllers\Asesi\DashboardController as AsesiDashboardController;
use App\Http\Controllers\Asesi\InformasiPembayaranController;
use App\Http\Controllers\Asesi\UploadSertifikatController;
use App\Http\Controllers\Asesi\ProfilAsesiController;
use App\Http\Controllers\Asesi\SertifikasiController;
use App\Http\Controllers\Asesi\UjikomController;
use App\Http\Controllers\Asesi\TemplateController;
use App\Http\Controllers\Asesi\FormulirController;
use App\Http\Controllers\Asesi\SkemaController as AsesiSkemaController;

use App\Http\Controllers\Asesor\VerifikasiPesertaController;
use App\Http\Controllers\Asesor\DashboardController as AsesorDashboardController;
use App\Http\Controllers\Asesor\PembayaranJasaController;
use App\Http\Controllers\Asesor\HasilUjikomController;
use App\Http\Controllers\Asesor\ProfilAsesorController;
use App\Http\Controllers\Asesor\ReviewController;
use App\Http\Controllers\Asesor\PemeriksaanController;

use App\Http\Controllers\Kaprodi\ReportHasilUjiController;
use App\Http\Controllers\Kaprodi\VerifikasiPendaftaranController;
use App\Http\Controllers\Kaprodi\ProfilKaprodiController;
use App\Http\Controllers\Kaprodi\DashboardController as KaprodiDashboardController;

use App\Http\Controllers\Pimpinan\ReportPimpinanController;
use App\Http\Controllers\Pimpinan\LaporanIKUController;
use App\Http\Controllers\Pimpinan\ProfilPimpinanController;
use App\Http\Controllers\Pimpinan\DashboardController as PimpinanDashboardController;

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
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard.admin');
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
    Route::post('user/toggle-asesor-status/{id}', [UserController::class, 'toggleAsesorStatus'])->name('admin.user.toggle-asesor-status');
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

    // Bank Soal routes (custom routes harus sebelum resource)
    Route::get('bank-soal/{id}/download', [BankSoalController::class, 'download'])->name('admin.bank-soal.download');
    Route::post('bank-soal/{id}/toggle-status', [BankSoalController::class, 'toggleStatus'])->name('admin.bank-soal.toggle-status');
    Route::resource('bank-soal', BankSoalController::class)->names('admin.bank-soal');

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
    Route::post('testing/fix-stuck-distributions', [TestingController::class, 'fixStuckDistributions'])->name('admin.testing.fix-stuck-distributions');
});

Route::group(['prefix' => 'asesi', 'middleware' => 'user.type'], function () {
    Route::get('/', [AsesiDashboardController::class, 'index'])->name('dashboard.asesi');
    Route::resource('informasi-pembayaran', InformasiPembayaranController::class)->names('asesi.informasi-pembayaran');
    Route::resource('upload-sertifikat', UploadSertifikatController::class)->names('asesi.upload-sertifikat');
    Route::resource('profil-asesi', ProfilAsesiController::class)->names('asesi.profil-asesi');
    Route::resource('daftar-ujikom', DaftarUjikomController::class)->names('asesi.daftar-ujikom')->middleware('check.second.registration');
    Route::resource('sertifikasi', SertifikasiController::class)->names('asesi.sertifikasi');

    // Skema routes
    Route::get('skema', [AsesiSkemaController::class, 'index'])->name('asesi.skema.index');
    Route::get('skema/{id}', [AsesiSkemaController::class, 'show'])->name('asesi.skema.show');

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
    Route::get('ujikom', [UjikomController::class, 'index'])->name('asesi.ujikom.index');

    // Registration info route
    Route::get('registration-info', [App\Http\Controllers\Asesi\RegistrationInfoController::class, 'index'])->name('asesi.registration-info');

    // Formulir routes (Bank Soal)
    Route::get('formulir/{jadwalId}', [FormulirController::class, 'index'])->name('asesi.formulir.index');
    Route::get('formulir/{jadwalId}/fill/{bankSoalId}', [FormulirController::class, 'fill'])->name('asesi.formulir.fill');
    Route::post('formulir/{jadwalId}/save-draft/{bankSoalId}', [FormulirController::class, 'saveDraft'])->name('asesi.formulir.save-draft');
    Route::post('formulir/{jadwalId}/submit/{bankSoalId}', [FormulirController::class, 'submit'])->name('asesi.formulir.submit');
    Route::get('formulir/{jadwalId}/view/{bankSoalId}', [FormulirController::class, 'view'])->name('asesi.formulir.view');
});

Route::group(['prefix' => 'asesor', 'middleware' => 'user.type'], function () {
    Route::get('/', [AsesorDashboardController::class, 'index'])->name('dashboard.asesor');
    Route::post('/confirm-jadwal', [AsesorDashboardController::class, 'confirmJadwal'])->name('asesor.dashboard.confirm-jadwal');

    // Review & Verifikasi routes (NEW - unified menu)
    Route::get('review', [ReviewController::class, 'index'])->name('asesor.review.index');
    Route::get('review/jadwal/{jadwalId}/asesi', [ReviewController::class, 'showAsesi'])->name('asesor.review.show-asesi');
    Route::get('review/apl1/{pendaftaranId}', [ReviewController::class, 'reviewApl1'])->name('asesor.review.apl1');
    Route::post('review/apl1/{pendaftaranId}', [ReviewController::class, 'storeReviewApl1'])->name('asesor.review.store-apl1');
    Route::get('review/apl1/{pendaftaranId}/generate', [ReviewController::class, 'generateApl1'])->name('asesor.review.generate-apl1');
    Route::get('review/apl2/{pendaftaranId}', [ReviewController::class, 'reviewApl2'])->name('asesor.review.apl2');
    Route::post('review/apl2/{pendaftaranId}', [ReviewController::class, 'storeReviewApl2'])->name('asesor.review.store-apl2');
    Route::get('review/apl2/{pendaftaranId}/generate', [ReviewController::class, 'generateApl2'])->name('asesor.review.generate-apl2');

    // OLD routes (can be deprecated later)
    Route::resource('verifikasi-peserta', VerifikasiPesertaController::class)->names('asesor.verifikasi-peserta');
    Route::get('verifikasi-peserta/show-asesi/{jadwalId}', [VerifikasiPesertaController::class, 'showAsesi'])->name('asesor.verifikasi-peserta.show-asesi');
    Route::match(['PUT', 'DELETE'], 'verifikasi-peserta/update-status/{jadwalId}', [VerifikasiPesertaController::class, 'updateStatus'])->name('asesor.verifikasi-peserta.update-status');
    Route::resource('pembayaran-jasa', PembayaranJasaController::class)->names('asesor.pembayaran-jasa');
    Route::resource('hasil-ujikom', HasilUjikomController::class)->names('asesor.hasil-ujikom');
    Route::get('hasil-ujikom/show-jawaban-asesi/{id}', [HasilUjikomController::class, 'showJawabanAsesi'])->name('asesor.hasil-ujikom.show-jawaban-asesi');

    // FR AK 05 routes
    Route::get('fr-ak-05/form/{jadwalId}', [App\Http\Controllers\Asesor\FrAk05Controller::class, 'showForm'])->name('asesor.fr-ak-05.form');
    Route::post('fr-ak-05/generate/{jadwalId}', [App\Http\Controllers\Asesor\FrAk05Controller::class, 'generate'])->name('asesor.fr-ak-05.generate');

    Route::resource('profil-asesor', ProfilAsesorController::class)->names('asesor.profil-asesor');

    // Pemeriksaan routes (Bank Soal Review)
    Route::get('pemeriksaan/jadwal/{jadwalId}/asesi', [PemeriksaanController::class, 'asesiList'])->name('asesor.pemeriksaan.asesi-list');
    Route::get('pemeriksaan/jadwal/{jadwalId}/asesi/{asesiId}/formulir', [PemeriksaanController::class, 'formulirList'])->name('asesor.pemeriksaan.formulir-list');
    Route::get('pemeriksaan/jadwal/{jadwalId}/asesi/{asesiId}/formulir/{bankSoalId}/review', [PemeriksaanController::class, 'review'])->name('asesor.pemeriksaan.review');
    Route::post('pemeriksaan/jadwal/{jadwalId}/asesi/{asesiId}/formulir/{bankSoalId}/review', [PemeriksaanController::class, 'saveReview'])->name('asesor.pemeriksaan.save-review');
    // FR AI 07 sekarang sudah masuk ke Bank Soal (tidak perlu route terpisah lagi)
    // Route::get('pemeriksaan/jadwal/{jadwalId}/asesi/{asesiId}/fr-ai-07', [PemeriksaanController::class, 'frAi07'])->name('asesor.pemeriksaan.fr-ai-07');
    // Route::post('pemeriksaan/jadwal/{jadwalId}/asesi/{asesiId}/fr-ai-07', [PemeriksaanController::class, 'saveFrAi07'])->name('asesor.pemeriksaan.save-fr-ai-07');
    Route::get('pemeriksaan/jadwal/{jadwalId}/asesi/{asesiId}/penilaian', [PemeriksaanController::class, 'penilaian'])->name('asesor.pemeriksaan.penilaian');
    Route::post('pemeriksaan/jadwal/{jadwalId}/asesi/{asesiId}/penilaian', [PemeriksaanController::class, 'savePenilaian'])->name('asesor.pemeriksaan.save-penilaian');
    Route::get('pemeriksaan/jadwal/{jadwalId}/asesi/{asesiId}/formulir/{bankSoalId}/generate', [PemeriksaanController::class, 'generateTemplate'])->name('asesor.pemeriksaan.generate-template');
});

Route::group(['prefix' => 'kaprodi', 'middleware' => 'user.type'], function () {
    Route::get('/', [KaprodiDashboardController::class, 'index'])->name('dashboard.kaprodi');
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
    Route::get('/', [PimpinanDashboardController::class, 'index'])->name('dashboard.pimpinan');
    Route::get('report-pimpinan/list-nama-kompeten/{id}', [ReportPimpinanController::class, 'listNamaKompeten'])->name('pimpinan.report-pimpinan.list-nama-kompeten');
    Route::get('report-pimpinan/list-nama-tidak-kompeten/{id}', [ReportPimpinanController::class, 'listNamaTidakKompeten'])->name('pimpinan.report-pimpinan.list-nama-tidak-kompeten');
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
