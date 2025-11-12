@extends('components.templates.master-layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    <!-- Date Range & Skema Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter"></i> Filter Data Dashboard
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="startDate" class="form-label">Tanggal Mulai:</label>
                            <input type="date" class="form-control" id="startDate" name="start_date">
                        </div>
                        <div class="col-md-3">
                            <label for="endDate" class="form-label">Tanggal Akhir:</label>
                            <input type="date" class="form-control" id="endDate" name="end_date">
                        </div>
                        <div class="col-md-3">
                            <label for="skemaFilter" class="form-label">Filter Skema:</label>
                            <select class="form-control" id="skemaFilter" name="skema_id">
                                <option value="">Semua Skema</option>
                                <!-- Options will be populated by JavaScript -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-primary btn-block mb-2" id="applyFilter">
                                    <i class="fas fa-filter"></i> Terapkan Filter
                                </button>
                                <button type="button" class="btn btn-secondary btn-block" id="clearFilter">
                                    <i class="fas fa-times"></i> Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPendaftaran">-</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSkema">-</div>
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
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800" id="tingkatKeberhasilan">-</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" id="progressKeberhasilan" style="width: 0%"
                                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalAsesor">-</div>
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
                <!-- Card Header -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tren Pendaftaran Skema</h6>
                </div>
                <!-- Card Body -->
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
                <!-- Card Header -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Keberhasilan</h6>
                </div>
                <!-- Card Body -->
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
                <!-- Card Header -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Segmentasi Demografi</h6>
                </div>
                <!-- Card Body -->
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
                <!-- Card Header -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Workload Asesor</h6>
                </div>
                <!-- Card Body -->
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
                <!-- Card Header -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tren Peminat Skema dari Waktu ke Waktu</h6>
                </div>
                <!-- Card Body -->
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
<!-- Analytics Dashboard -->
<script src="{{ asset('js/analytics-dashboard-laravel.js') }}"></script>

<!-- Debug Script -->
<script>
// Debug and ensure data loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== Dashboard Debug Info ===');
    console.log('Page loaded');
    console.log('Chart.js loaded:', typeof Chart !== 'undefined');
    console.log('jQuery loaded:', typeof $ !== 'undefined');

    // Wait a bit for the analytics class to initialize
    setTimeout(function() {
        console.log('Attempting manual data load test...');

        // Test fetch to analytics endpoint
        const userType = window.location.pathname.includes('/pimpinan/') ? 'pimpinan' : 'kaprodi';
        const url = window.location.origin + '/' + userType + '/analytics/dashboard-data';

        console.log('Testing URL:', url);

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            return response.json();
        })
        .then(data => {
            console.log('=== Analytics Data Response ===');
            console.log(data);

            if (data.success && data.data) {
                console.log('Data loaded successfully!');
                console.log('Dashboard Summary:', data.data.dashboard_summary);
                console.log('Skema Trend items:', data.data.skema_trend?.length || 0);
                console.log('Kompetensi Skema keys:', Object.keys(data.data.kompetensi_skema || {}).length);
            } else {
                console.error('Data load failed:', data.message);
            }
        })
        .catch(error => {
            console.error('=== Fetch Error ===');
            console.error(error);
        });
    }, 2000);
});
</script>
@endpush
