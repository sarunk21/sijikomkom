@extends('components.templates.master-layout')

@section('title', 'Dashboard Pimpinan')
@section('page-title', 'Executive Dashboard')

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
                <form method="GET" action="{{ route('dashboard.pimpinan') }}" id="filterForm">
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
                                    <a href="{{ route('dashboard.pimpinan') }}" class="btn btn-secondary">
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

    <!-- Content Row -->
    <div class="row">

        <!-- Total Pendaftaran Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pendaftaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalPendaftaran) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Skema Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Skema</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSkema }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-certificate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pass Rate Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pass Rate
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $passRate }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $passRate }}%"
                                            aria-valuenow="{{ $passRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trophy fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Secondary KPIs -->
    <div class="row">
        <!-- Total Jadwal -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Jadwal</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalJadwal) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total TUK -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total TUK</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTuk }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Total Asesor Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Asesor</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAsesor }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Tren Pendaftaran Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tren Pendaftaran (12 Bulan Terakhir)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="skemaTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribusi Skema Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 5 Skema Terpopuler</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar pt-4 pb-2">
                        <canvas id="statistikKeberhasilanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Segmentasi Demografi Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Segmentasi Jenis Kelamin</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="segmentasiDemografiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Workload Asesor Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Workload Asesor (Top 10)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="workloadAsesorChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

    <script>
    // Data dari controller
    const trendPendaftaranData = @json($trendPendaftaran);
    const distribusiSkemaData = @json($distribusiSkema);
    const segmentasiGenderData = @json($segmentasiGender);
    const topAsesorData = @json($topAsesor);
    const workloadDistributionData = @json($workloadDistribution);

    // Chart 1: Tren Pendaftaran (12 Bulan)
    const trendCtx = document.getElementById('skemaTrendChart');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendPendaftaranData.map(item => item.bulan),
            datasets: [{
                label: 'Total Pendaftaran',
                data: trendPendaftaranData.map(item => item.jumlah),
                borderColor: 'rgb(78, 115, 223)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Chart 2: Distribusi Skema (Top 5) - Bar Chart
    const distribusiCtx = document.getElementById('statistikKeberhasilanChart');
    new Chart(distribusiCtx, {
        type: 'bar',
        data: {
            labels: distribusiSkemaData.map(item => item.nama),
            datasets: [{
                label: 'Jumlah Pendaftaran',
                data: distribusiSkemaData.map(item => item.total),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                borderColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
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

    // Chart 3: Segmentasi Gender - Pie Chart (karena hanya 2 klasifikasi)
    const segmentasiCtx = document.getElementById('segmentasiDemografiChart');
    new Chart(segmentasiCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(segmentasiGenderData),
            datasets: [{
                data: Object.values(segmentasiGenderData),
                backgroundColor: ['#4e73df', '#e74a3b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Chart 4: Top 10 Asesor
    const workloadCtx = document.getElementById('workloadAsesorChart');
    new Chart(workloadCtx, {
        type: 'bar',
        data: {
            labels: topAsesorData.map(item => item.nama),
            datasets: [{
                label: 'Total Asesi',
                data: topAsesorData.map(item => item.total),
                backgroundColor: 'rgba(246, 194, 62, 0.8)',
                borderColor: 'rgb(246, 194, 62)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Chart 5: Workload Distribution
    const distribCtx = document.getElementById('trenPeminatSkemaChart');
    new Chart(distribCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(workloadDistributionData),
            datasets: [{
                data: Object.values(workloadDistributionData),
                backgroundColor: ['#1cc88a', '#36b9cc', '#f6c23e', '#fd7e14', '#e74a3b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
    </script>
@endpush
