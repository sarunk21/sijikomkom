@extends('components.templates.master-layout')

@section('title', 'Dashboard Kepala TUK')
@section('page-title', 'Dashboard Kepala TUK')

@section('content')

    <!-- Content Row -->
    <div class="row">

        <!-- Total Asesi Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Asesi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAsesi }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kapasitas TUK Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Kapasitas TUK</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kapasitasTuk }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Hari Ini Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jadwal Hari Ini
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $jadwalHariIni }}</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $jadwalHariIni > 0 ? ($jadwalHariIni / 8 * 100) : 0 }}%"
                                            aria-valuenow="{{ $jadwalHariIni }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendapatan Bulanan Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pendapatan Bulanan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($pendapatanBulanan, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Area Chart - Tren Kunjungan TUK -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tren Kunjungan TUK</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Filter Periode:</div>
                            <a class="dropdown-item" href="#" onclick="updateTrenChart('month')">Bulan Ini</a>
                            <a class="dropdown-item" href="#" onclick="updateTrenChart('quarter')">Kuartal Ini</a>
                            <a class="dropdown-item" href="#" onclick="updateTrenChart('year')">Tahun Ini</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="trenKunjunganChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart - Distribusi Skema -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Distribusi Skema</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Filter:</div>
                            <a class="dropdown-item" href="#" onclick="updateDistribusiChart()">Refresh</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="distribusiSkemaChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($distribusiSkema as $skema => $jumlah)
                            <span class="mr-2">
                                <i class="fas fa-circle text-{{ $loop->index == 0 ? 'primary' : ($loop->index == 1 ? 'success' : 'info') }}"></i> {{ $skema }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fasilitas & Kapasitas Row -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Fasilitas TUK</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Komputer & Perangkat</span>
                            <span class="text-success">95%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: 95%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Jaringan Internet</span>
                            <span class="text-success">100%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Software Lisensi</span>
                            <span class="text-warning">85%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: 85%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Ruang Ujikom</span>
                            <span class="text-success">90%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: 90%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Jadwal Ujikom Minggu Ini</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Jumlah Ujikom</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalMingguan as $item)
                                <tr>
                                    <td>{{ $item['hari'] }}</td>
                                    <td>{{ $item['jumlah'] }}</td>
                                    <td><span class="badge badge-{{ $item['status'] == 'Selesai' ? 'success' : ($item['status'] == 'Sedang Berlangsung' ? 'warning' : 'info') }}">{{ $item['status'] }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Laporan Ringkas Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Laporan Ringkas Bulanan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="border-right">
                                <h4 class="text-primary">{{ $laporanBulanan['totalUjikom'] }}</h4>
                                <p class="text-muted">Total Ujikom</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border-right">
                                <h4 class="text-success">{{ $laporanBulanan['lulus'] }}</h4>
                                <p class="text-muted">Lulus</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border-right">
                                <h4 class="text-warning">{{ $laporanBulanan['tidakLulus'] }}</h4>
                                <p class="text-muted">Tidak Lulus</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div>
                                <h4 class="text-info">{{ $laporanBulanan['persentaseLulus'] }}%</h4>
                                <p class="text-muted">Persentase Kelulusan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Data dari controller
const trenData = @json($trenKunjungan);
const distribusiData = @json($distribusiSkema);

// Chart untuk tren kunjungan
const trenCtx = document.getElementById('trenKunjunganChart').getContext('2d');
const trenChart = new Chart(trenCtx, {
    type: 'line',
    data: {
        labels: trenData.map(item => item.bulan),
        datasets: [{
            label: 'Jumlah Kunjungan',
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
                    stepSize: 5
                }
            }
        }
    }
});

// Chart untuk distribusi skema
const distribusiCtx = document.getElementById('distribusiSkemaChart').getContext('2d');
const distribusiChart = new Chart(distribusiCtx, {
    type: 'doughnut',
    data: {
        labels: Object.keys(distribusiData),
        datasets: [{
            data: Object.values(distribusiData),
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#6f42c1', '#e83e8c'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#5a32a3', '#d63384']
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



function updateTrenChart(period) {
    // Simulasi update chart berdasarkan periode
    console.log('Updating tren chart for period:', period);
}

function updateDistribusiChart() {
    // Simulasi refresh distribusi chart
    console.log('Refreshing distribusi chart');
}
</script>
@endpush
