@extends('components.templates.master-layout')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Analytics')

@section('content')

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filter Dashboard
            </h6>
            <button class="btn btn-sm btn-primary" type="button" data-toggle="collapse" data-target="#filterCollapse" 
                    aria-expanded="true" aria-controls="filterCollapse">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <form method="GET" action="{{ route('dashboard.admin') }}" id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="start_date">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ $startDate ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="end_date">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ $endDate ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="skema_id">Skema Sertifikasi</label>
                                <select class="form-control" id="skema_id" name="skema_id">
                                    <option value="">-- Semua Skema --</option>
                                    @foreach($skemas as $skema)
                                        <option value="{{ $skema->id }}" 
                                                {{ ($skemaId ?? '') == $skema->id ? 'selected' : '' }}>
                                            {{ $skema->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary btn-block mr-1">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('dashboard.admin') }}" class="btn btn-secondary">
                                        <i class="fas fa-redo"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Hero Statistics Row --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-lg border-0 bg-gradient-primary text-white">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-2"><i class="fas fa-chart-line mr-2"></i>Sistem Informasi Uji Kompetensi</h3>
                            <p class="mb-0 opacity-75">Real-time analytics dan insights untuk pengambilan keputusan strategis</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="display-4">{{ \Carbon\Carbon::now()->format('d M Y') }}</div>
                            <small class="opacity-75">{{ \Carbon\Carbon::now()->format('H:i') }} WIB</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Cards Row 1 --}}
    <div class="row">
        <!-- Total Pendaftaran -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 animate-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pendaftaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalPendaftaran) }}</div>
                            @if($growthRate > 0)
                                <small class="text-success"><i class="fas fa-arrow-up"></i> +{{ $growthRate }}% vs bulan lalu</small>
                            @elseif($growthRate < 0)
                                <small class="text-danger"><i class="fas fa-arrow-down"></i> {{ $growthRate }}% vs bulan lalu</small>
                            @else
                                <small class="text-muted"><i class="fas fa-minus"></i> Stabil</small>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Asesi -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 animate-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Asesi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalAsesi) }}</div>
                            <small class="text-muted">Peserta Unik</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pass Rate -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 animate-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tingkat Kelulusan</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $passRate }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                             style="width: {{ $passRate }}%"
                                             aria-valuenow="{{ $passRate }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">{{ $totalLulus }} dari {{ $totalSelesai }} peserta</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Skema -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 animate-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Skema</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSkema }}</div>
                            <small class="text-muted">Sertifikasi Tersedia</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-certificate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Cards Row 2 --}}
    <div class="row">
        <!-- Total Asesor -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Total Asesor</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAsesor }}</div>
                            <small class="text-muted">Asesor Aktif</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Jadwal -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Total Jadwal</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalJadwal }}</div>
                            <small class="text-muted">{{ $jadwalAktif }} Aktif</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversion Rate -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Conversion Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $conversionRate }}%</div>
                            <small class="text-muted">Pendaftaran → Lulus</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-funnel-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Menunggu -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu Verifikasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusStats['menunggu_verifikasi'] }}</div>
                            <small class="text-muted">Perlu Action</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="row">
        <!-- Trend Pendaftaran -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area mr-2"></i>Trend Pendaftaran (12 Bulan Terakhir)
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="fas fa-info-circle text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <div class="dropdown-header">Penjelasan:</div>
                            <a class="dropdown-item" href="#">
                                <small>Chart ini menampilkan trend jumlah pendaftaran dalam 12 bulan terakhir untuk melihat pola seasonality dan pertumbuhan</small>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 5 Skema -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-medal mr-2"></i>Top 5 Skema Populer
                    </h6>
                </div>
                <div class="card-body">
                    @if($distribusiSkema->count() > 0)
                        <div class="chart-bar pt-4 pb-2">
                            <canvas id="skemaChart"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data skema</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 - Advanced Analytics --}}
    <div class="row">
        <!-- Workload Asesor -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users-cog mr-2"></i>Workload Asesor (Top 10)
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        <small><i class="fas fa-info-circle"></i> Distribusi beban kerja untuk capacity planning</small>
                    </p>
                    @if($workloadAsesor->count() > 0)
                        <div class="chart-bar">
                            <canvas id="workloadChart"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data workload</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Top Performing Skema --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-trophy mr-2"></i>Top Performing Skema (Berdasarkan Pass Rate)
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        <small><i class="fas fa-info-circle"></i> Ranking skema dengan tingkat kelulusan tertinggi (minimal 5 ujian)</small>
                    </p>
                    @if($topSkema->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Skema</th>
                                        <th class="text-center">Total Ujian</th>
                                        <th class="text-center">Lulus</th>
                                        <th class="text-center">Pass Rate</th>
                                        <th width="30%">Performance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topSkema as $item)
                                        <tr>
                                            <td class="text-center">
                                                @if($loop->index == 0)
                                                    <i class="fas fa-trophy text-warning" style="font-size: 1.5rem;"></i>
                                                @elseif($loop->index == 1)
                                                    <i class="fas fa-medal text-secondary" style="font-size: 1.3rem;"></i>
                                                @elseif($loop->index == 2)
                                                    <i class="fas fa-medal text-bronze" style="font-size: 1.1rem;"></i>
                                                @else
                                                    {{ $loop->iteration }}
                                                @endif
                                            </td>
                                            <td><strong>{{ $item['nama'] }}</strong></td>
                                            <td class="text-center">{{ $item['total_ujian'] }}</td>
                                            <td class="text-center">{{ $item['total_lulus'] }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-{{ $item['pass_rate'] >= 80 ? 'success' : ($item['pass_rate'] >= 60 ? 'warning' : 'danger') }}" style="font-size: 0.9rem;">
                                                    {{ $item['pass_rate'] }}%
                                                </span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 25px;">
                                                    <div class="progress-bar bg-{{ $item['pass_rate'] >= 80 ? 'success' : ($item['pass_rate'] >= 60 ? 'warning' : 'danger') }}"
                                                         role="progressbar"
                                                         style="width: {{ $item['pass_rate'] }}%">
                                                        {{ $item['pass_rate'] }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum cukup data untuk ranking (minimal 5 ujian per skema)</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- AI Insights --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-lightbulb mr-2"></i>Insights & Recommendations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Trend Analysis -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-success h-100">
                                <div class="card-body">
                                    <h6 class="text-success font-weight-bold mb-3">
                                        <i class="fas fa-chart-line mr-1"></i> Trend Analysis
                                    </h6>
                                    <p class="mb-0">{{ $insights['trend'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Capacity Planning -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-info h-100">
                                <div class="card-body">
                                    <h6 class="text-info font-weight-bold mb-3">
                                        <i class="fas fa-users mr-1"></i> Capacity Planning
                                    </h6>
                                    <p class="mb-0">{{ $insights['capacity'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Items -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-warning h-100">
                                <div class="card-body">
                                    <h6 class="text-warning font-weight-bold mb-3">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Action Items
                                    </h6>
                                    <p class="mb-0">{{ $insights['action'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Distribution --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tasks mr-2"></i>Distribusi Status Pendaftaran
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md py-3 border-right">
                            <h4 class="text-info">{{ $statusStats['menunggu_verifikasi'] }}</h4>
                            <p class="text-muted mb-0 small">Menunggu Verifikasi</p>
                        </div>
                        <div class="col-md py-3 border-right">
                            <h4 class="text-danger">{{ $statusStats['ditolak'] }}</h4>
                            <p class="text-muted mb-0 small">Ditolak</p>
                        </div>
                        <div class="col-md py-3 border-right">
                            <h4 class="text-success">{{ $statusStats['diverifikasi'] }}</h4>
                            <p class="text-muted mb-0 small">Diverifikasi</p>
                        </div>
                        <div class="col-md py-3 border-right">
                            <h4 class="text-warning">{{ $statusStats['menunggu_ujian'] }}</h4>
                            <p class="text-muted mb-0 small">Menunggu Ujian</p>
                        </div>
                        <div class="col-md py-3 border-right">
                            <h4 class="text-primary">{{ $statusStats['ujian_berlangsung'] }}</h4>
                            <p class="text-muted mb-0 small">Ujian Berlangsung</p>
                        </div>
                        <div class="col-md py-3 border-right">
                            <h4 class="text-success">{{ $statusStats['selesai'] }}</h4>
                            <p class="text-muted mb-0 small">Selesai</p>
                        </div>
                        <div class="col-md py-3">
                            <h4 class="text-dark">{{ $statusStats['asesor_tidak_hadir'] }}</h4>
                            <p class="text-muted mb-0 small">Asesor Tidak Hadir</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
    }

    .animate-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .animate-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }

    .modern-funnel {
        padding: 10px 0;
    }

    .funnel-item {
        margin-bottom: 8px;
    }

    .funnel-bar {
        border-radius: 12px;
        padding: 20px 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        margin: 0 auto;
        position: relative;
        overflow: hidden;
    }

    .funnel-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.2);
        transition: left 0.5s ease;
    }

    .funnel-bar:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .funnel-bar:hover::before {
        left: 100%;
    }

    .funnel-bar-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .funnel-bar-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .funnel-bar-icon {
        font-size: 1.8rem;
        color: #fff;
        opacity: 0.95;
    }

    .funnel-bar-label {
        font-size: 1.1rem;
        font-weight: 700;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .funnel-bar-right {
        display: flex;
        align-items: baseline;
        gap: 10px;
    }

    .funnel-bar-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: #fff;
    }

    .funnel-bar-percent {
        font-size: 1rem;
        font-weight: 600;
        color: rgba(255,255,255,0.9);
        background: rgba(0,0,0,0.15);
        padding: 4px 10px;
        border-radius: 20px;
    }

    .funnel-arrow {
        text-align: center;
        padding: 8px 0;
        color: #858796;
        font-size: 1.2rem;
    }

    .funnel-arrow i {
        display: block;
        margin-bottom: 4px;
        animation: bounce 1.5s infinite;
    }

    .funnel-dropoff-text {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #e74a3b;
    }

    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(5px);
        }
    }

    .metric-card {
        border-radius: 15px;
        padding: 25px;
        color: #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 20px;
        position: relative;
        overflow: hidden;
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 3s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 0.5;
        }
        50% {
            opacity: 1;
        }
    }

    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }

    .metric-card-icon {
        font-size: 3rem;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }

    .metric-card-content {
        flex: 1;
        position: relative;
        z-index: 1;
    }

    .metric-card-label {
        font-size: 0.9rem;
        font-weight: 600;
        opacity: 0.95;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-card-value {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 6px;
    }

    .metric-card-desc {
        font-size: 0.85rem;
        opacity: 0.85;
    }

    .text-bronze {
        color: #CD7F32 !important;
    }

    .chart-area {
        position: relative;
        height: 20rem;
        width: 100%;
    }

    .chart-bar {
        position: relative;
        height: 15rem;
        width: 100%;
    }

    .chart-pie {
        position: relative;
        height: 15rem;
        width: 100%;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script>
// Data dari controller
const trenData = @json($trenPendaftaran);
const skemaData = @json($distribusiSkema);
const workloadData = @json($workloadAsesor);

// Trend Chart
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: trenData.map(d => d.bulan),
        datasets: [{
            label: 'Jumlah Pendaftaran',
            data: trenData.map(d => d.jumlah),
            borderColor: 'rgb(78, 115, 223)',
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            borderWidth: 3,
            pointRadius: 5,
            pointBackgroundColor: 'rgb(78, 115, 223)',
            pointBorderColor: '#fff',
            pointHoverRadius: 7,
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.8)',
                padding: 12,
                titleFont: { size: 14 },
                bodyFont: { size: 13 }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 5 },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});

// Skema Bar Chart
@if($distribusiSkema->count() > 0)
const skemaCtx = document.getElementById('skemaChart').getContext('2d');
new Chart(skemaCtx, {
    type: 'bar',
    data: {
        labels: Object.keys(skemaData),
        datasets: [{
            label: 'Jumlah Pendaftaran',
            data: Object.values(skemaData),
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
            borderColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
            borderWidth: 2
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + context.parsed.y;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});
@endif

// Workload Bar Chart
@if($workloadAsesor->count() > 0)
const workloadCtx = document.getElementById('workloadChart').getContext('2d');
new Chart(workloadCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($workloadAsesor->pluck('nama')) !!},
        datasets: [{
            label: 'Jumlah Asesi',
            data: {!! json_encode($workloadAsesor->pluck('total')) !!},
            backgroundColor: 'rgba(28, 200, 138, 0.8)',
            borderColor: 'rgb(28, 200, 138)',
            borderWidth: 2
        }]
    },
    options: {
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: { stepSize: 5 }
            }
        }
    }
});
@endif
</script>
@endpush
