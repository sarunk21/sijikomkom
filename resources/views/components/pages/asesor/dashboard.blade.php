@extends('components.templates.master-layout')

@section('title', 'Dashboard Asesor')
@section('page-title', 'Dashboard Asesor')

@section('content')

    <!-- Content Row -->
    <div class="row">

        <!-- Total Ujikom Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Ujikom</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUjikom }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Hari Ini Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Jadwal Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jadwalHariIni }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rata-rata Nilai Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rata-rata Nilai
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $rataNilai }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $rataNilai }}%"
                                            aria-valuenow="{{ $rataNilai }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pembayaran Jasa Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pembayaran Jasa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($pembayaranJasa, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Area Chart - Performa Ujikom -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Performa Ujikom Bulanan</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Filter Periode:</div>
                            <a class="dropdown-item" href="#" onclick="updatePerformaChart('month')">Bulan Ini</a>
                            <a class="dropdown-item" href="#" onclick="updatePerformaChart('quarter')">Kuartal Ini</a>
                            <a class="dropdown-item" href="#" onclick="updatePerformaChart('year')">Tahun Ini</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="performaUjikomChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart - Status Penilaian -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Status Penilaian</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Filter:</div>
                            <a class="dropdown-item" href="#" onclick="updateStatusPenilaianChart()">Refresh</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="statusPenilaianChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($statusPenilaian as $status => $jumlah)
                            <span class="mr-2">
                                <i class="fas fa-circle text-{{ $status == 'Lulus' ? 'success' : ($status == 'Sedang Dinilai' ? 'warning' : 'danger') }}"></i> {{ $status }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Ujikom Terdekat Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Jadwal Ujikom Terdekat</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="jadwalTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Tanggal & Waktu</th>
                                    <th>Nama Asesi</th>
                                    <th>Skema</th>
                                    <th>TUK</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalTerdekat as $item)
                                <tr>
                                    <td>{{ $item['tanggal'] }}</td>
                                    <td>{{ $item['nama'] }}</td>
                                    <td>{{ $item['skema'] }}</td>
                                    <td>{{ $item['tuk'] }}</td>
                                    <td><span class="badge badge-{{ $item['status'] == 'Selesai' || str_contains($item['status'], 'Lolos') ? 'success' : (str_contains($item['status'], 'Menunggu') ? 'warning' : 'info') }}">{{ $item['status'] }}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">Detail</button>
                                        <button class="btn btn-sm btn-success">Mulai Ujikom</button>
                                    </td>
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
const performaData = @json($performaUjikom);
const statusPenilaianData = @json($statusPenilaian);

// Chart untuk performa ujikom
const performaCtx = document.getElementById('performaUjikomChart').getContext('2d');
const performaChart = new Chart(performaCtx, {
    type: 'bar',
    data: {
        labels: performaData.map(item => item.bulan),
        datasets: [{
            label: 'Jumlah Ujikom',
            data: performaData.map(item => item.jumlah),
            backgroundColor: 'rgba(78, 115, 223, 0.8)',
            borderColor: 'rgb(78, 115, 223)',
            borderWidth: 1
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

// Chart untuk status penilaian
const statusPenilaianCtx = document.getElementById('statusPenilaianChart').getContext('2d');
const statusPenilaianChart = new Chart(statusPenilaianCtx, {
    type: 'doughnut',
    data: {
        labels: Object.keys(statusPenilaianData),
        datasets: [{
            data: Object.values(statusPenilaianData),
            backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
            hoverBackgroundColor: ['#17a673', '#f4b619', '#d52a1a']
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



function updatePerformaChart(period) {
    // Simulasi update chart berdasarkan periode
    console.log('Updating performa chart for period:', period);
}

function updateStatusPenilaianChart() {
    // Simulasi refresh status penilaian chart
    console.log('Refreshing status penilaian chart');
}
</script>
@endpush
