@extends('components.templates.master-layout')

@section('title', 'Dashboard Asesi')
@section('page-title', 'Dashboard Asesi')

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
                <form method="GET" action="{{ route('dashboard.asesi') }}" id="filterForm">
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
                                    <a href="{{ route('dashboard.asesi') }}" class="btn btn-secondary">
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

    {{-- Hero Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-lg border-0 bg-gradient-primary text-white">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-2"><i class="fas fa-user-graduate mr-2"></i>Selamat Datang, {{ Auth::user()->name }}!</h3>
                            <p class="mb-0 opacity-75">Dashboard Personal - Pantau progress sertifikasi Anda</p>
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
            <div class="card border-left-primary shadow h-100 py-2">
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

        <!-- Total Sertifikat -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sertifikat Kompeten</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalSertifikat) }}</div>
                            <small class="text-muted">{{ $totalSkema }} Skema Diikuti</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-certificate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tingkat Keberhasilan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tingkat Keberhasilan</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $statusSertifikasi }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                             style="width: {{ $statusSertifikasi }}%"
                                             aria-valuenow="{{ $statusSertifikasi }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">Pass Rate Personal</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Mendatang -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Jadwal Mendatang</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($jadwalUjikom) }}</div>
                            @if($pembayaranPending > 0)
                                <small class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $pembayaranPending }} Pembayaran Pending</small>
                            @else
                                <small class="text-success"><i class="fas fa-check-circle"></i> Pembayaran Clear</small>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row">
        <!-- Performance by Skema -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy mr-2"></i>Performa per Skema
                    </h6>
                </div>
                <div class="card-body">
                    @if($performanceSkema->count() > 0)
                        @foreach($performanceSkema as $perf)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small font-weight-bold">{{ $perf['nama'] }}</span>
                                    <span class="badge badge-{{ $perf['pass_rate'] >= 80 ? 'success' : ($perf['pass_rate'] >= 60 ? 'warning' : 'danger') }}">
                                        {{ $perf['pass_rate'] }}%
                                    </span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-{{ $perf['pass_rate'] >= 80 ? 'success' : ($perf['pass_rate'] >= 60 ? 'warning' : 'danger') }}"
                                         role="progressbar"
                                         style="width: {{ $perf['pass_rate'] }}%"
                                         aria-valuenow="{{ $perf['pass_rate'] }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">{{ $perf['kompeten'] }}/{{ $perf['total_ujian'] }} Kompeten</small>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                            <p>Belum ada data performa</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status Pendaftaran Pie Chart -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie mr-2"></i>Distribusi Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="statusPendaftaranChart"></canvas>
                    </div>
                    <div class="mt-4 small">
                        @php
                            $colors = ['success', 'warning', 'danger', 'info', 'secondary', 'primary'];
                            $index = 0;
                        @endphp
                        @foreach($statusPendaftaran as $status => $jumlah)
                            <div class="mb-2">
                                <span class="mr-2">
                                    <i class="fas fa-circle text-{{ $colors[$index % count($colors)] }}"></i>
                                </span>
                                <span class="font-weight-bold">{{ $status }}:</span>
                                <span class="float-right">{{ $jumlah }}</span>
                            </div>
                            @php $index++; @endphp
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Jadwal & Sertifikat Row --}}
    <div class="row">
        <!-- Jadwal Mendatang Detail -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-alt mr-2"></i>Jadwal Ujian Mendatang
                    </h6>
                    <span class="badge badge-primary">{{ $jadwalMendatang->count() }}</span>
                </div>
                <div class="card-body">
                    @if($jadwalMendatang->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Skema</th>
                                        <th>TUK</th>
                                        <th>Countdown</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwalMendatang as $jadwal)
                                        <tr>
                                            <td>
                                                <small class="font-weight-bold">{{ $jadwal['tanggal_ujian'] }}</small>
                                            </td>
                                            <td><small>{{ Str::limit($jadwal['skema'], 20) }}</small></td>
                                            <td><small>{{ Str::limit($jadwal['tuk'], 15) }}</small></td>
                                            <td>
                                                @if($jadwal['hari_lagi'] !== null)
                                                    @if($jadwal['hari_lagi'] > 0)
                                                        <span class="badge badge-warning">{{ $jadwal['hari_lagi'] }} hari lagi</span>
                                                    @elseif($jadwal['hari_lagi'] == 0)
                                                        <span class="badge badge-danger">Hari ini!</span>
                                                    @else
                                                        <span class="badge badge-secondary">Sudah lewat</span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-calendar-times fa-3x mb-3"></i>
                            <p>Tidak ada jadwal mendatang</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Riwayat Sertifikasi -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-award mr-2"></i>Riwayat Sertifikasi
                    </h6>
                    <span class="badge badge-success">{{ $totalSertifikat }} Kompeten</span>
                </div>
                <div class="card-body">
                    @if($riwayatSertifikasi->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($riwayatSertifikasi as $riwayat)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 font-weight-bold">{{ $riwayat['skema'] }}</h6>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar mr-1"></i>{{ $riwayat['tanggal'] }}
                                            </small>
                                        </div>
                                        <span class="badge badge-{{ $riwayat['status_badge'] }} badge-pill">
                                            {{ $riwayat['status'] }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-certificate fa-3x mb-3"></i>
                            <p>Belum ada riwayat sertifikasi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Aktivitas Terbaru --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history mr-2"></i>Aktivitas Terbaru (10 Terakhir)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="aktivitasTable">
                            <thead class="thead-light">
                                <tr>
                                    <th width="15%">Tanggal</th>
                                    <th width="20%">Aktivitas</th>
                                    <th width="20%">Status</th>
                                    <th width="45%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($aktivitas as $item)
                                    <tr>
                                        <td><small>{{ $item['tanggal'] }}</small></td>
                                        <td><small class="font-weight-bold">{{ $item['aktivitas'] }}</small></td>
                                        <td>
                                            <span class="badge badge-{{ $item['status_badge'] }}">{{ $item['status'] }}</span>
                                        </td>
                                        <td><small>{{ $item['keterangan'] }}</small></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>Belum ada aktivitas</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
/* Card Animations */
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
}

/* Progress Bar Animation */
.progress-bar {
    transition: width 1s ease-in-out;
}

/* Badge Pulse for Warnings */
.badge-warning, .badge-danger {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

/* Responsive Chart Container */
.chart-area {
    position: relative;
    height: 300px;
}

.chart-pie {
    position: relative;
    height: 250px;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script>
// Data dari controller
const statusData = @json($statusPendaftaran);

// Chart untuk status pendaftaran - Bar untuk data yang banyak
const statusCtx = document.getElementById('statusPendaftaranChart').getContext('2d');
const statusKeys = Object.keys(statusData);
const statusValues = Object.values(statusData);
const useBarChart = statusKeys.length > 3 || statusValues.some(v => v > 10);

const statusChart = new Chart(statusCtx, {
    type: useBarChart ? 'bar' : 'doughnut',
    data: {
        labels: statusKeys,
        datasets: [{
            label: useBarChart ? 'Jumlah' : '',
            data: statusValues,
            backgroundColor: [
                '#1cc88a', // success
                '#f6c23e', // warning
                '#e74a3b', // danger
                '#36b9cc', // info
                '#6f42c1', // secondary
                '#4e73df'  // primary
            ],
            borderColor: useBarChart ? [
                '#17a673',
                '#f4b619',
                '#d52a1a',
                '#2c9faf',
                '#5a32a3',
                '#2e59d9'
            ] : undefined,
            borderWidth: useBarChart ? 2 : 2,
            borderColor: useBarChart ? undefined : '#fff'
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
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#fff',
                borderWidth: 1,
                displayColors: true,
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return label + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            }
        },
        ...(useBarChart ? {
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
        } : {
            cutout: '70%'
        })
    }
});

// DataTable for Aktivitas
$(document).ready(function() {
    $('#aktivitasTable').DataTable({
        "pageLength": 10,
        "ordering": true,
        "searching": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });
});
</script>
@endpush
