@extends('components.templates.master-layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    <!-- Content Row -->
    <div class="row">

        <!-- Total Pendaftaran Card -->
        <div class="col-xl-3 col-md-6 mb-4">
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
        <div class="col-xl-3 col-md-6 mb-4">
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

        <!-- Tingkat Keberhasilan Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tingkat Keberhasilan
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $tingkatKeberhasilan }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $tingkatKeberhasilan }}%"
                                            aria-valuenow="{{ $tingkatKeberhasilan }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Asesor Card -->
        <div class="col-xl-3 col-md-6 mb-4">
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

        <!-- Tren Pendaftaran Skema Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tren Pendaftaran Skema (6 Bulan Terakhir)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="skemaTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Keberhasilan Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Keberhasilan</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
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

    <!-- Content Row -->
    <div class="row">

        <!-- Tren Peminat Skema Chart -->
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Skema Terpopuler (6 Bulan Terakhir)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="trenPeminatSkemaChart"></canvas>
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
    const skemaTrendData = @json($skemaTrend);
    const statistikKeberhasilanData = @json($statistikKeberhasilan);
    const segmentasiJenisKelaminData = @json($segmentasiJenisKelamin);
    const workloadAsesorData = @json($workloadAsesor);
    const trenPeminatSkemaData = @json($trenPeminatSkema);

    // Chart 1: Tren Pendaftaran Skema
    const skemaTrendCtx = document.getElementById('skemaTrendChart');
    new Chart(skemaTrendCtx, {
        type: 'line',
        data: {
            labels: skemaTrendData.map(item => item.month),
            datasets: [{
                label: 'Total Pendaftaran',
                data: skemaTrendData.map(item => item.total_pendaftaran),
                borderColor: 'rgb(78, 115, 223)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.3
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

    // Chart 2: Statistik Keberhasilan
    const statistikCtx = document.getElementById('statistikKeberhasilanChart');
    new Chart(statistikCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(statistikKeberhasilanData),
            datasets: [{
                data: Object.values(statistikKeberhasilanData),
                backgroundColor: ['#1cc88a', '#e74a3b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Chart 3: Segmentasi Jenis Kelamin
    const segmentasiCtx = document.getElementById('segmentasiDemografiChart');
    new Chart(segmentasiCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(segmentasiJenisKelaminData),
            datasets: [{
                data: Object.values(segmentasiJenisKelaminData),
                backgroundColor: ['#4e73df', '#e74a3b', '#f6c23e', '#36b9cc']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Chart 4: Workload Asesor
    const workloadCtx = document.getElementById('workloadAsesorChart');
    new Chart(workloadCtx, {
        type: 'bar',
        data: {
            labels: workloadAsesorData.map(item => item.asesor_name),
            datasets: [{
                label: 'Jumlah Ujikom',
                data: workloadAsesorData.map(item => item.jumlah_ujikom),
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

    // Chart 5: Tren Peminat Skema
    const trenPeminatCtx = document.getElementById('trenPeminatSkemaChart');
    new Chart(trenPeminatCtx, {
        type: 'bar',
        data: {
            labels: trenPeminatSkemaData.map(item => item.skema_nama),
            datasets: [{
                label: 'Total Pendaftaran',
                data: trenPeminatSkemaData.map(item => item.total_pendaftaran),
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: 'rgb(78, 115, 223)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
    </script>
@endpush
