@extends('components.templates.master-layout')

@section('title', 'Dashboard Asesi')
@section('page-title', 'Dashboard Asesi')

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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPendaftaran }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Ujikom Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Jadwal Ujikom</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jadwalUjikom }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Sertifikasi Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Status Sertifikasi
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $statusSertifikasi }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $statusSertifikasi }}%"
                                            aria-valuenow="{{ $statusSertifikasi }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-certificate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pembayaran Pending Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pembayaran Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pembayaranPending }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Area Chart - Tren Pendaftaran -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tren Pendaftaran Ujikom</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Filter Periode:</div>
                            <a class="dropdown-item" href="#" onclick="updateChart('month')">Bulan Ini</a>
                            <a class="dropdown-item" href="#" onclick="updateChart('quarter')">Kuartal Ini</a>
                            <a class="dropdown-item" href="#" onclick="updateChart('year')">Tahun Ini</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="trenPendaftaranChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart - Status Pendaftaran -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Status Pendaftaran</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Filter:</div>
                            <a class="dropdown-item" href="#" onclick="updateStatusChart()">Refresh</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="statusPendaftaranChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($statusPendaftaran as $status => $jumlah)
                            <span class="mr-2">
                                <i class="fas fa-circle text-{{ $loop->index == 0 ? 'success' : ($loop->index == 1 ? 'warning' : 'danger') }}"></i> {{ $status }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="aktivitasTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Aktivitas</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($aktivitas as $item)
                                <tr>
                                    <td>{{ $item['tanggal'] }}</td>
                                    <td>{{ $item['aktivitas'] }}</td>
                                    <td><span class="badge badge-{{ $item['status'] == 'Selesai' || str_contains($item['status'], 'Lolos') ? 'success' : (str_contains($item['status'], 'Menunggu') ? 'warning' : 'danger') }}">{{ $item['status'] }}</span></td>
                                    <td>{{ $item['keterangan'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script>
// Data dari controller
const trenData = @json($trenPendaftaran);
const statusData = @json($statusPendaftaran);

// Chart untuk tren pendaftaran
const trenCtx = document.getElementById('trenPendaftaranChart').getContext('2d');
const trenChart = new Chart(trenCtx, {
    type: 'line',
    data: {
        labels: trenData.map(item => item.bulan),
        datasets: [{
            label: 'Jumlah Pendaftaran',
            data: trenData.map(item => item.jumlah),
            borderColor: 'rgb(78, 115, 223)',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Chart untuk status pendaftaran
const statusCtx = document.getElementById('statusPendaftaranChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: Object.keys(statusData),
        datasets: [{
            data: Object.values(statusData),
            backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b', '#36b9cc', '#6f42c1'],
            hoverBackgroundColor: ['#17a673', '#f4b619', '#d52a1a', '#2c9faf', '#5a32a3']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});



function updateChart(period) {
    // Simulasi update chart berdasarkan periode
    console.log('Updating chart for period:', period);
}

function updateStatusChart() {
    // Simulasi refresh status chart
    console.log('Refreshing status chart');
}
</script>
@endpush
