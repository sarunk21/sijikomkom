@extends('components.templates.master-layout')

@section('title', 'Testing Tools')
@section('page-title', 'Testing Tools')

@section('content')
    <div class="alert alert-warning border-left-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Development Mode Only!</strong> Fitur ini hanya untuk testing. Klik tombol sesuai urutan untuk mensimulasikan full flow sistem.
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-times-circle"></i> {{ session('error') }}
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-info-circle"></i> {{ session('info') }}
        </div>
    @endif

    <!-- Statistik Quick View -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-left-primary shadow-sm">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Perlu Verifikasi</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendaftaranVerifikasi }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-success shadow-sm">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Siap Distribusi</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendaftaranMenungguDistribusi }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-warning shadow-sm">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ujikom Menunggu</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendaftaranUjikomMenunggu }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-danger shadow-sm">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pembayaran Asesor</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pembayaranAsesorMenunggu }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: NEW FLOW Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-left-purple shadow-sm" style="border-left-color: #6f42c1 !important;">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #6f42c1;">Verif Asesor ⭐</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendaftaranMenungguVerifAsesor ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-purple shadow-sm" style="border-left-color: #6f42c1 !important;">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #6f42c1;">Approval Kelayakan ⭐</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendaftaranMenungguApprovalKelayakan ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-purple shadow-sm" style="border-left-color: #6f42c1 !important;">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #6f42c1;">Menunggu Bayar ⭐</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendaftaranMenungguPembayaran ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-danger shadow-sm">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Tidak Lulus</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendaftaranTidakLulus ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Sertifikat Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-left-purple shadow-sm">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-purple text-uppercase mb-1">Sertifikat Aktif</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $sertifikatAktif }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-info shadow-sm">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sudah Sertifikat</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendaftaranSudahSertifikat }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-secondary shadow-sm">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Ujikom Berlangsung</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendaftaranUjikomBerlangsung }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-dark shadow-sm">
                <div class="card-body py-2">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Jadwal Selesai</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jadwalSelesai }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Issues & Alerts -->
    @if($pendaftaranStuckDistribution > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning border-left-warning shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="font-weight-bold mb-1">
                            <i class="fas fa-users-slash"></i> Stuck Distributions Terdeteksi
                        </h6>
                        <p class="mb-0">
                            Ada <strong>{{ $pendaftaranStuckDistribution }}</strong> pendaftaran yang stuck di status 7 (Asesor Tidak Dapat Hadir).
                            Gunakan tombol "Fix Stuck Distributions" di bawah untuk redistribusi ke asesor lain.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Testing Buttons -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-flask"></i> Quick Actions - Klik Sesuai Urutan
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Step 1 -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-primary h-100">
                        <div class="card-body">
                            <h6 class="text-primary"><span class="badge badge-primary">1</span> Loloskan Verifikasi</h6>
                            <p class="small text-muted mb-2">Update pendaftaran status 1 & 3 → 4 (Menunggu Distribusi)</p>
                            <form action="{{ route('admin.testing.update-status-pendaftaran') }}" method="POST" onsubmit="return confirm('Loloskan verifikasi?')">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm btn-block">
                                    <i class="fas fa-check-double"></i> Loloskan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-success h-100">
                        <div class="card-body">
                            <h6 class="text-success"><span class="badge badge-success">2</span> Distribusi Asesor</h6>
                            <p class="small text-muted mb-2">Distribusi ke asesor berdasarkan skema + kirim email</p>
                            <form action="{{ route('admin.testing.trigger-distribusi') }}" method="POST" onsubmit="return confirm('Distribusi ke asesor?')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm btn-block">
                                    <i class="fas fa-random"></i> Distribusi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Step 3: NEW - Auto Approve Kelayakan -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-purple h-100" style="border-color: #6f42c1 !important;">
                        <div class="card-body">
                            <h6 style="color: #6f42c1;"><span class="badge" style="background-color: #6f42c1;">3</span> Auto Approve Kelayakan ⭐</h6>
                            <p class="small text-muted mb-2">Status 5→6→8 + Buat Pembayaran (NEW FLOW)</p>
                            <form action="{{ route('admin.testing.auto-approve-kelayakan') }}" method="POST" onsubmit="return confirm('Auto approve kelayakan?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-block" style="background-color: #6f42c1; color: white;">
                                    <i class="fas fa-clipboard-check"></i> Approve
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Step 4: NEW - Auto Verify Pembayaran -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-purple h-100" style="border-color: #6f42c1 !important;">
                        <div class="card-body">
                            <h6 style="color: #6f42c1;"><span class="badge" style="background-color: #6f42c1;">4</span> Auto Verify Pembayaran ⭐</h6>
                            <p class="small text-muted mb-2">Pembayaran status 1→4, Pendaftaran 8→9 (NEW)</p>
                            <form action="{{ route('admin.testing.auto-verify-pembayaran') }}" method="POST" onsubmit="return confirm('Auto verify pembayaran?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-block" style="background-color: #6f42c1; color: white;">
                                    <i class="fas fa-money-check-alt"></i> Verify
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-warning h-100">
                        <div class="card-body">
                            <h6 class="text-warning"><span class="badge badge-warning">5</span> Mulai Jadwal</h6>
                            <p class="small text-muted mb-2">Jadwal aktif → Status 3 (Ujian Berlangsung)</p>
                            <form action="{{ route('admin.testing.start-jadwal') }}" method="POST" onsubmit="return confirm('Mulai jadwal?')">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm btn-block">
                                    <i class="fas fa-calendar-check"></i> Start
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Step 6 -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-info h-100">
                        <div class="card-body">
                            <h6 class="text-info"><span class="badge badge-info">6</span> Mulai Ujikom</h6>
                            <p class="small text-muted mb-2">PendaftaranUjikom → Status 2 (Ujikom Berlangsung)</p>
                            <form action="{{ route('admin.testing.simulasi-ujikom') }}" method="POST" onsubmit="return confirm('Mulai ujikom?')">
                                @csrf
                                <button type="submit" class="btn btn-info btn-sm btn-block">
                                    <i class="fas fa-play-circle"></i> Mulai
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Step 7 -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-secondary h-100">
                        <div class="card-body">
                            <h6 class="text-secondary"><span class="badge badge-secondary">7</span> Selesai Ujikom</h6>
                            <p class="small text-muted mb-2">PendaftaranUjikom → Status 3 (Ujikom Selesai)</p>
                            <form action="{{ route('admin.testing.selesaikan-ujikom') }}" method="POST" onsubmit="return confirm('Selesaikan ujikom?')">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm btn-block">
                                    <i class="fas fa-check"></i> Selesai
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Step 8 -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-dark h-100">
                        <div class="card-body">
                            <h6 class="text-dark"><span class="badge badge-dark">8</span> Selesai Jadwal</h6>
                            <p class="small text-muted mb-2">Jadwal → Status 4 (Selesai)</p>
                            <form action="{{ route('admin.testing.selesaikan-jadwal') }}" method="POST" onsubmit="return confirm('Selesaikan jadwal?')">
                                @csrf
                                <button type="submit" class="btn btn-dark btn-sm btn-block">
                                    <i class="fas fa-flag-checkered"></i> Finish
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Step 9 -->
                <div class="col-md-6 mb-3">
                    <div class="card border-danger h-100">
                        <div class="card-body">
                            <h6 class="text-danger"><span class="badge badge-danger">9</span> Trigger Pembayaran Asesor</h6>
                            <p class="small text-muted mb-2">Buat record PembayaranAsesor untuk jadwal yang sudah selesai</p>
                            <form action="{{ route('admin.testing.trigger-pembayaran-asesor') }}" method="POST" onsubmit="return confirm('Trigger pembayaran asesor?')">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm btn-block">
                                    <i class="fas fa-money-bill-wave"></i> Trigger Pembayaran
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Step 8 -->
                <div class="col-md-6 mb-3">
                    <div class="card border-purple h-100">
                        <div class="card-body">
                            <h6 class="text-purple"><span class="badge badge-purple">8</span> Upload Sertifikat</h6>
                            <p class="small text-muted mb-2">Simulasi upload sertifikat bertanda tangan untuk asesi yang lulus</p>
                            <form action="{{ route('admin.testing.upload-sertifikat') }}" method="POST" onsubmit="return confirm('Upload sertifikat untuk asesi yang lulus?')">
                                @csrf
                                <button type="submit" class="btn btn-purple btn-sm btn-block">
                                    <i class="fas fa-certificate"></i> Upload Sertifikat
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fix Section -->
            <hr class="my-4">
            <h6 class="font-weight-bold text-danger mb-3">
                <i class="fas fa-tools"></i> Fix & Utilities
            </h6>
            <div class="row">
                <!-- Fix Stuck Payments -->
                <div class="col-md-6 mb-3">
                    <div class="card border-danger h-100">
                        <div class="card-body">
                            <h6 class="text-danger"><i class="fas fa-wrench"></i> Fix Stuck Payments</h6>
                            <p class="small text-muted mb-2">
                                Auto-fix pembayaran "Pendaftaran Pertama" yang stuck di status "Menunggu Verifikasi" → langsung approve & create pendaftaran
                            </p>
                            <form action="{{ route('admin.testing.fix-stuck-payments') }}" method="POST" onsubmit="return confirm('Fix stuck payments? Ini akan auto-approve semua Pendaftaran Pertama yang stuck.')">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm btn-block">
                                    <i class="fas fa-band-aid"></i> Fix Stuck Payments
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Fix Stuck Distributions -->
                <div class="col-md-6 mb-3">
                    <div class="card border-warning h-100">
                        <div class="card-body">
                            <h6 class="text-warning"><i class="fas fa-redo"></i> Fix Stuck Distributions</h6>
                            <p class="small text-muted mb-2">
                                Redistribusi pendaftaran dengan status 7 (Asesor Tidak Dapat Hadir) ke asesor lain yang tersedia dengan workload terendah
                            </p>
                            <form action="{{ route('admin.testing.fix-stuck-distributions') }}" method="POST" onsubmit="return confirm('Redistribusi pendaftaran yang stuck? Ini akan mencari asesor pengganti untuk pendaftaran di status 7.')">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm btn-block">
                                    <i class="fas fa-sync-alt"></i> Fix Stuck Distributions
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card shadow mt-4">
        <div class="card-body">
            <h6 class="font-weight-bold mb-3"><i class="fas fa-info-circle text-primary"></i> Alur Testing</h6>
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <span class="badge badge-primary p-2 m-1">1. Verifikasi</span>
                <i class="fas fa-arrow-right text-muted"></i>
                <span class="badge badge-success p-2 m-1">2. Distribusi</span>
                <i class="fas fa-arrow-right text-muted"></i>
                <span class="badge badge-warning p-2 m-1">3. Start Jadwal</span>
                <i class="fas fa-arrow-right text-muted"></i>
                <span class="badge badge-info p-2 m-1">4. Start Ujikom</span>
                <i class="fas fa-arrow-right text-muted"></i>
                <span class="badge badge-secondary p-2 m-1">5. Finish Ujikom</span>
                <i class="fas fa-arrow-right text-muted"></i>
                <span class="badge badge-dark p-2 m-1">6. Finish Jadwal</span>
                <i class="fas fa-arrow-right text-muted"></i>
                <span class="badge badge-danger p-2 m-1">7. Pembayaran</span>
                <i class="fas fa-arrow-right text-muted"></i>
                <span class="badge badge-purple p-2 m-1">8. Sertifikat</span>
            </div>
            <hr>
            <p class="small text-muted mb-0">
                <strong>Catatan:</strong> Fitur ini untuk development/testing only.
                Untuk production, hapus menu "Testing Tools" di <code>app/Traits/MenuTrait.php</code> pada method <code>getMenuListAdmin()</code>.
            </p>
        </div>
    </div>

    <style>
        .border-purple {
            border-color: #6f42c1 !important;
        }

        .text-purple {
            color: #6f42c1 !important;
        }

        .badge-purple {
            background-color: #6f42c1 !important;
        }

        .btn-purple {
            background-color: #6f42c1;
            color: white;
            border-color: #6f42c1;
        }

        .btn-purple:hover {
            background-color: #5a2d91;
            border-color: #5a2d91;
            color: white;
        }

        .border-left-purple {
            border-left: 0.25rem solid #6f42c1 !important;
        }
    </style>
@endsection

