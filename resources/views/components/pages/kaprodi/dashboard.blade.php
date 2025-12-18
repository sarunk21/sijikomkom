@extends('components.templates.master-layout')

@section('title', 'Dashboard Kaprodi')
@section('page-title', 'Dashboard Kaprodi')

@section('content')

<style>
/* Modern Gradient Cards */
.gradient-card {
    background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
    border: none;
    color: white;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.gradient-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.gradient-primary {
    --gradient-start: #4e73df;
    --gradient-end: #224abe;
}

.gradient-success {
    --gradient-start: #1cc88a;
    --gradient-end: #13855c;
}

.gradient-info {
    --gradient-start: #36b9cc;
    --gradient-end: #258391;
}

.gradient-warning {
    --gradient-start: #f6c23e;
    --gradient-end: #dda20a;
}

.gradient-danger {
    --gradient-start: #e74a3b;
    --gradient-end: #be2617;
}

.gradient-purple {
    --gradient-start: #6f42c1;
    --gradient-end: #4e2d89;
}

.gradient-teal {
    --gradient-start: #20c9a6;
    --gradient-end: #158f75;
}

/* Metric Value Animation */
.metric-value {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}

/* Chart Container Styling */
.chart-container {
    position: relative;
    height: 300px;
}

.chart-container-large {
    position: relative;
    height: 400px;
}

/* Insight Cards */
.insight-card {
    border-left: 4px solid;
    background: #f8f9fc;
    transition: all 0.3s ease;
}

.insight-card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.insight-success {
    border-left-color: #1cc88a;
}

.insight-warning {
    border-left-color: #f6c23e;
}

.insight-danger {
    border-left-color: #e74a3b;
}

/* Status Badge Enhanced */
.status-metric {
    padding: 1rem;
    text-align: center;
    border-radius: 8px;
    background: #ffffff;
    border: 2px solid #e3e6f0;
    transition: all 0.3s ease;
}

.status-metric:hover {
    border-color: #4e73df;
    transform: scale(1.05);
}

.status-metric h3 {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.status-metric p {
    font-size: 0.85rem;
    color: #858796;
    margin: 0;
}

/* Table Styling */
.performance-table {
    font-size: 0.9rem;
}

.performance-table th {
    background: #f8f9fc;
    font-weight: 600;
    color: #5a5c69;
    border: none;
}

.performance-table td {
    vertical-align: middle;
    border-color: #e3e6f0;
}

.badge-custom {
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
    font-weight: 600;
}
</style>

<!-- KPI Cards Row 1 -->
<div class="row mb-4">
    <!-- Total Pendaftaran -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card gradient-card gradient-primary shadow h-100 py-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-2">
                            Total Pendaftaran
                        </div>
                        <div class="metric-value text-white">{{ number_format($totalPendaftaran) }}</div>
                        <div class="text-xs text-white-50 mt-2">
                            <i class="fas fa-chart-line mr-1"></i>
                            {{ $growthRate >= 0 ? '+' : '' }}{{ $growthRate }}% dari bulan lalu
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Asesi -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card gradient-card gradient-success shadow h-100 py-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-2">
                            Total Asesi
                        </div>
                        <div class="metric-value text-white">{{ number_format($totalAsesi) }}</div>
                        <div class="text-xs text-white-50 mt-2">
                            <i class="fas fa-users mr-1"></i>
                            Peserta unik terdaftar
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menunggu Verifikasi -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card gradient-card gradient-warning shadow h-100 py-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-2">
                            Menunggu Verifikasi
                        </div>
                        <div class="metric-value text-white">{{ $menungguVerifikasi }}</div>
                        <div class="text-xs text-white-50 mt-2">
                            <i class="fas fa-clock mr-1"></i>
                            Perlu ditindaklanjuti
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hourglass-half fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Rate -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card gradient-card gradient-info shadow h-100 py-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-2">
                            Tingkat Persetujuan
                        </div>
                        <div class="metric-value text-white">{{ $approvalRate }}%</div>
                        <div class="text-xs text-white-50 mt-2">
                            <i class="fas fa-check-circle mr-1"></i>
                            Dari total pendaftaran
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-double fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards Row 2 -->
<div class="row mb-4">
    <!-- Pass Rate -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card gradient-card gradient-purple shadow h-100 py-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-white-50 text-uppercase mb-2">
                            Tingkat Kelulusan
                        </div>
                        <div class="metric-value text-white">{{ $passRate }}%</div>
                        <div class="text-xs text-white-50 mt-2">
                            <i class="fas fa-graduation-cap mr-1"></i>
                            Dari ujian selesai
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-award fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Skema -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-2">
                            Total Skema
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalSkema }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-certificate fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Asesor -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-2">
                            Total Asesor
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalAsesor }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Status Pendaftaran -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tasks mr-2"></i>Statistik Status Pendaftaran
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2 mb-3">
                        <div class="status-metric">
                            <h3 class="text-warning">{{ $statusStats['menunggu_verifikasi'] }}</h3>
                            <p>Menunggu Verifikasi</p>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="status-metric">
                            <h3 class="text-danger">{{ $statusStats['ditolak'] }}</h3>
                            <p>Ditolak</p>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="status-metric">
                            <h3 class="text-success">{{ $statusStats['diverifikasi'] }}</h3>
                            <p>Diverifikasi</p>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="status-metric">
                            <h3 class="text-info">{{ $statusStats['menunggu_ujian'] }}</h3>
                            <p>Menunggu Ujian</p>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="status-metric">
                            <h3 class="text-primary">{{ $statusStats['ujian_berlangsung'] }}</h3>
                            <p>Ujian Berlangsung</p>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="status-metric">
                            <h3 class="text-secondary">{{ $statusStats['selesai'] }}</h3>
                            <p>Selesai</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="row mb-4">
    <!-- Tren Pendaftaran -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line mr-2"></i>Tren Pendaftaran (12 Bulan Terakhir)
                </h6>
                <span class="badge badge-{{ $growthRate >= 0 ? 'success' : 'danger' }} badge-custom">
                    {{ $growthRate >= 0 ? '+' : '' }}{{ $growthRate }}% Growth
                </span>
            </div>
            <div class="card-body">
                <div class="chart-container-large">
                    <canvas id="trenPendaftaranChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribusi Skema -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar mr-2"></i>Distribusi Skema (Top 5)
                </h6>
            </div>
            <div class="card-body">
                @if($distribusiSkema->count() > 0)
                    <div class="chart-container">
                        <canvas id="distribusiSkemaChart"></canvas>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data distribusi skema</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="row mb-4">
    <!-- Verifikasi Trend -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-area mr-2"></i>Tren Verifikasi & Penolakan
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container-large">
                    <canvas id="verifikasiTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gender Segmentation -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-venus-mars mr-2"></i>Segmentasi Jenis Kelamin
                </h6>
            </div>
            <div class="card-body">
                @if($segmentasiJenisKelamin->count() > 0)
                    <div class="chart-container">
                        <canvas id="segmentasiChart"></canvas>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data segmentasi</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row mb-4">
    <!-- Top Performing Skema -->
    <div class="col-xl-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-trophy mr-2"></i>Top 5 Skema by Pass Rate
                </h6>
            </div>
            <div class="card-body">
                @if($topSkema->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover performance-table">
                            <thead>
                                <tr>
                                    <th>Skema</th>
                                    <th class="text-center">Total Ujian</th>
                                    <th class="text-center">Lulus</th>
                                    <th class="text-center">Pass Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topSkema as $skema)
                                <tr>
                                    <td class="font-weight-bold">{{ $skema['nama'] }}</td>
                                    <td class="text-center">{{ $skema['total_ujian'] }}</td>
                                    <td class="text-center">{{ $skema['total_lulus'] }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $skema['pass_rate'] >= 80 ? 'success' : ($skema['pass_rate'] >= 60 ? 'warning' : 'danger') }} badge-custom">
                                            {{ $skema['pass_rate'] }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data skema (minimal 3 ujian)</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Workload Asesor -->
    <div class="col-xl-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user-tie mr-2"></i>Workload Asesor (Top 10)
                </h6>
            </div>
            <div class="card-body">
                @if($workloadAsesor->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover performance-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Asesor</th>
                                    <th class="text-center">Jumlah Ujikom</th>
                                    <th class="text-center">Banyak Asesi yang Diaseses</th>
                                    <th>Workload</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workloadAsesor as $index => $asesor)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="font-weight-bold">{{ $asesor['nama'] }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-primary badge-custom">{{ $asesor['total'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success badge-custom">{{ $asesor['total_asesi'] ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $index < 3 ? 'danger' : ($index < 6 ? 'warning' : 'success') }}"
                                                 role="progressbar"
                                                 style="width: {{ $workloadAsesor->first()['total'] > 0 ? ($asesor['total'] / $workloadAsesor->first()['total'] * 100) : 0 }}%">
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
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data workload asesor</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script>
// Data dari controller
const trenPendaftaran = @json($trenPendaftaran);
const distribusiSkema = @json($distribusiSkema);
const verifikasiTrend = @json($verifikasiTrend);
const segmentasiJenisKelamin = @json($segmentasiJenisKelamin);

// Chart 1: Tren Pendaftaran
const trenCtx = document.getElementById('trenPendaftaranChart');
if (trenCtx) {
    new Chart(trenCtx, {
        type: 'line',
        data: {
            labels: trenPendaftaran.map(item => item.bulan),
            datasets: [{
                label: 'Jumlah Pendaftaran',
                data: trenPendaftaran.map(item => item.jumlah),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#4e73df',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
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
                    ticks: {
                        stepSize: 1,
                        font: { size: 12 }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    ticks: { font: { size: 12 } },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Chart 2: Distribusi Skema
const distribusiCtx = document.getElementById('distribusiSkemaChart');
if (distribusiCtx && Object.keys(distribusiSkema).length > 0) {
    new Chart(distribusiCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(distribusiSkema),
            datasets: [{
                label: 'Jumlah Pendaftaran',
                data: Object.values(distribusiSkema),
                backgroundColor: [
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e',
                    '#e74a3b'
                ],
                borderColor: [
                    '#2e59d9',
                    '#17a673',
                    '#2c9faf',
                    '#dda20a',
                    '#be2617'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12
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
}

// Chart 3: Verifikasi Trend
const verifikasiCtx = document.getElementById('verifikasiTrendChart');
if (verifikasiCtx) {
    new Chart(verifikasiCtx, {
        type: 'line',
        data: {
            labels: verifikasiTrend.map(item => item.bulan),
            datasets: [
                {
                    label: 'Diverifikasi',
                    data: verifikasiTrend.map(item => item.diverifikasi),
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Ditolak',
                    data: verifikasiTrend.map(item => item.ditolak),
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15
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
}

// Chart 4: Segmentasi Jenis Kelamin - Pie Chart (karena hanya 2 klasifikasi)
const segmentasiCtx = document.getElementById('segmentasiChart');
if (segmentasiCtx && Object.keys(segmentasiJenisKelamin).length > 0) {
    new Chart(segmentasiCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(segmentasiJenisKelamin),
            datasets: [{
                data: Object.values(segmentasiJenisKelamin),
                backgroundColor: [
                    '#4e73df',
                    '#e74a3b',
                    '#f6c23e',
                    '#36b9cc'
                ],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 12 },
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12
                }
            }
        }
    });
}
</script>
@endpush
