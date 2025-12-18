@extends('components.templates.master-layout')

@section('title', 'Dashboard Kepala TUK')
@section('page-title', 'Dashboard Kepala TUK')

@section('content')

    <!-- Content Row - Cards -->
    <div class="row">

        <!-- Total Jadwal Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Jadwal</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalJadwal }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Aktif Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Jadwal Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jadwalAktif }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jadwalHariIni }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Asesi Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
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
    </div>

    <!-- Content Row - Charts -->
    <div class="row">

        <!-- Area Chart - Tren Jadwal -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tren Jadwal Ujikom (12 Bulan Terakhir)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="trenJadwalChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart - Distribusi Skema -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribusi Skema</h6>
                </div>
                <div class="card-body">
                    @if($distribusiSkema->count() > 0)
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

    <!-- Content Row - Tables -->
    <div class="row">

        <!-- Jadwal Minggu Ini -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Jadwal Ujikom Minggu Ini</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalMingguan as $item)
                                <tr>
                                    <td>{{ $item['hari'] }}</td>
                                    <td>{{ $item['tanggal'] }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $item['jumlah'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $item['status'] == 'Selesai' ? 'secondary' : ($item['status'] == 'Hari Ini' ? 'success' : 'info') }}">
                                            {{ $item['status'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Mendatang -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Jadwal Mendatang</h6>
                </div>
                <div class="card-body">
                    @if($jadwalMendatang->count() > 0)
                        <div class="list-group">
                            @foreach($jadwalMendatang as $jadwal)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $jadwal->skema->nama }}</h6>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('d M Y') }}</small>
                                </div>
                                <p class="mb-1">
                                    <i class="fas fa-map-marker-alt text-info mr-1"></i>
                                    <small>{{ $jadwal->tuk->nama }}</small>
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-clock mr-1"></i>{{ $jadwal->waktu_mulai ?? 'Belum ditentukan' }}
                                </small>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada jadwal mendatang</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Status Jadwal Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Status Jadwal</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="border-right py-3">
                                <h4 class="text-secondary">{{ $statusJadwal['pending'] }}</h4>
                                <p class="text-muted mb-0">Pending</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border-right py-3">
                                <h4 class="text-success">{{ $statusJadwal['aktif'] }}</h4>
                                <p class="text-muted mb-0">Aktif</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border-right py-3">
                                <h4 class="text-warning">{{ $statusJadwal['ditunda'] }}</h4>
                                <p class="text-muted mb-0">Ditunda</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-right py-3">
                                <h4 class="text-info">{{ $statusJadwal['sedang_berlangsung'] }}</h4>
                                <p class="text-muted mb-0">Sedang Berlangsung</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="py-3">
                                <h4 class="text-primary">{{ $statusJadwal['selesai'] }}</h4>
                                <p class="text-muted mb-0">Selesai</p>
                            </div>
                        </div>
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
const trenData = @json($trenJadwal);
const distribusiData = @json($distribusiSkema);

// Chart untuk tren jadwal
const trenCtx = document.getElementById('trenJadwalChart').getContext('2d');
const trenChart = new Chart(trenCtx, {
    type: 'line',
    data: {
        labels: trenData.map(item => item.bulan),
        datasets: [{
            label: 'Jumlah Jadwal',
            data: trenData.map(item => item.jumlah),
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
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            }
        }
    }
});

// Chart untuk distribusi skema (hanya jika ada data) - Bar untuk data yang banyak
@if($distribusiSkema->count() > 0)
const distribusiCtx = document.getElementById('distribusiSkemaChart').getContext('2d');
const distribusiKeys = Object.keys(distribusiData);
const distribusiValues = Object.values(distribusiData);
const useBarChart = distribusiKeys.length > 3 || distribusiValues.some(v => v > 10);

const distribusiChart = new Chart(distribusiCtx, {
    type: useBarChart ? 'bar' : 'doughnut',
    data: {
        labels: distribusiKeys,
        datasets: [{
            label: useBarChart ? 'Jumlah' : '',
            data: distribusiValues,
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#6f42c1', '#e83e8c', '#f6c23e'],
            borderColor: useBarChart ? ['#2e59d9', '#17a673', '#2c9faf', '#5a32a3', '#d63384', '#dda20a'] : undefined,
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
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
        } : {})
    }
});
@endif
</script>
@endpush
