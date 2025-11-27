@extends('components.templates.master-layout')

@section('title', 'Dashboard Asesor')
@section('page-title', 'Dashboard Asesor')

@section('content')

    {{-- Alert Konfirmasi Kehadiran --}}
    @if(isset($pendingConfirmations) && $pendingConfirmations->count() > 0)
    <div class="alert alert-warning border-left-warning shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <div class="mr-3">
                <i class="fas fa-exclamation-triangle fa-2x"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="font-weight-bold mb-1">
                    <i class="fas fa-calendar-check mr-1"></i> Konfirmasi Kehadiran Diperlukan!
                </h6>
                <p class="mb-0">
                    Anda ditugaskan untuk <strong>{{ $pendingConfirmations->count() }} jadwal ujikom</strong> mendatang.
                    <a href="{{ route('asesor.review.index') }}" class="alert-link font-weight-bold">Buka Menu Review & Verifikasi →</a>
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Row 1: KPI Cards --}}
    <div class="row">
        <!-- Total Asesi Dinilai -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card gradient-card gradient-primary shadow h-100 py-3">
                <div class="card-body">
                    <div class="metric-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="metric-value text-white">{{ number_format($totalAsesiDinilai) }}</div>
                    <div class="metric-label text-white-50">Total Asesi Dinilai</div>
                    <div class="text-xs text-white-50 mt-2">
                        <i class="fas fa-info-circle mr-1"></i> Lifetime (sejak awal)
                    </div>
                </div>
            </div>
        </div>

        <!-- Asesi Bulan Ini -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card gradient-card gradient-success shadow h-100 py-3">
                <div class="card-body">
                    <div class="metric-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="metric-value text-white">{{ number_format($asesiBulanIni) }}</div>
                    <div class="metric-label text-white-50">Asesi Bulan Ini</div>
                    <div class="text-xs text-white-50 mt-2">
                        @if($perubahanAsesi >= 0)
                            <i class="fas fa-arrow-up mr-1"></i> +{{ $perubahanAsesi }}% dari bulan lalu
                        @else
                            <i class="fas fa-arrow-down mr-1"></i> {{ $perubahanAsesi }}% dari bulan lalu
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tingkat Kelulusan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card gradient-card gradient-info shadow h-100 py-3">
                <div class="card-body">
                    <div class="metric-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="metric-value text-white">{{ $tingkatKelulusan }}%</div>
                    <div class="metric-label text-white-50">Tingkat Kelulusan</div>
                    <div class="text-xs text-white-50 mt-2">
                        <i class="fas fa-calculator mr-1"></i> {{ number_format($performanceSummary['total_kompeten']) }} kompeten dari {{ number_format($performanceSummary['total_penilaian']) }} total
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Aktif -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card gradient-card gradient-warning shadow h-100 py-3">
                <div class="card-body">
                    <div class="metric-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="metric-value text-white">{{ number_format($jadwalAktif) }}</div>
                    <div class="metric-label text-white-50">Jadwal Aktif</div>
                    <div class="text-xs text-white-50 mt-2">
                        <i class="fas fa-hourglass-half mr-1"></i> Pending + Berlangsung
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2: Secondary KPIs --}}
    <div class="row">
        <!-- Total Skema -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Skema Dikuasai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSkema }}</div>
                            <div class="text-xs text-muted mt-1">Sertifikasi aktif</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-certificate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avg Waktu Penilaian -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Avg Waktu Penilaian</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avgWaktuPenilaian }} Jam</div>
                            <div class="text-xs text-muted mt-1">Per asesi (rata-rata)</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Jadwal Selesai -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Jadwal Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalJadwalSelesai) }}</div>
                            <div class="text-xs text-muted mt-1">Jadwal yang sudah dinilai</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rata-rata Asesi per Jadwal -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Avg Asesi per Jadwal</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avgAsesiPerJadwal }}</div>
                            <div class="text-xs text-muted mt-1">Rata-rata beban kerja</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: Charts --}}
    <div class="row">
        <!-- Trend Penilaian (6 Bulan) -->
        <div class="col-xl-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area mr-2"></i>Trend Penilaian (6 Bulan Terakhir)
                    </h6>
                    <span class="badge badge-primary">Kompeten vs Tidak Kompeten</span>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="trendPenilaianChart"></canvas>
                    </div>
                    <div class="mt-3 text-sm text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Cara Baca:</strong> Grafik menampilkan jumlah asesi yang dinilai kompeten (hijau) dan tidak kompeten (merah) per bulan dalam 6 bulan terakhir.
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie mr-2"></i>Ringkasan Performa
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:250px;">
                        <canvas id="performancePieChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-sm"><i class="fas fa-circle text-success mr-1"></i> Kompeten</span>
                            <strong>{{ number_format($performanceSummary['total_kompeten']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-sm"><i class="fas fa-circle text-danger mr-1"></i> Tidak Kompeten</span>
                            <strong>{{ number_format($performanceSummary['total_tidak_kompeten']) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 4: More Charts --}}
    <div class="row">
        <!-- Distribusi per Skema -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-layer-group mr-2"></i>Top 5 Skema yang Dinilai
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="distribusiSkemaChart"></canvas>
                    </div>
                    <div class="mt-3 text-sm text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Insight:</strong> Menampilkan 5 skema yang paling sering Anda nilai, membantu mengidentifikasi area keahlian utama.
                    </div>
                </div>
            </div>
        </div>

        <!-- Workload Analysis -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tasks mr-2"></i>Analisis Beban Kerja (6 Bulan)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="workloadChart"></canvas>
                    </div>
                    <div class="mt-3 text-sm text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Insight:</strong> Menunjukkan tren beban kerja bulanan untuk perencanaan waktu dan kapasitas yang lebih baik.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 5: Upcoming Jadwal --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-check mr-2"></i>Jadwal Ujikom Mendatang
                    </h6>
                </div>
                <div class="card-body">
                    @if($upcomingJadwal->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Skema</th>
                                        <th>TUK</th>
                                        <th>Jumlah Asesi</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingJadwal as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ \Carbon\Carbon::parse($item['jadwal']->tanggal_ujian)->format('d M Y') }}</strong>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($item['jadwal']->waktu_mulai)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($item['jadwal']->waktu_selesai)->format('H:i') }}
                                        </td>
                                        <td>{{ $item['jadwal']->skema->nama ?? 'N/A' }}</td>
                                        <td>{{ $item['jadwal']->tuk->nama ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $item['jumlah_asesi'] }} Asesi</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-success">
                                                <i class="fas fa-check mr-1"></i>Confirmed
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle mr-2"></i>
                            Tidak ada jadwal ujikom mendatang yang sudah dikonfirmasi.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Gradient Cards */
        .gradient-card {
            border: none;
            border-radius: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .gradient-info {
            background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
        }

        .gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .metric-icon {
            position: absolute;
            right: 1rem;
            top: 1rem;
            font-size: 2rem;
            opacity: 0.3;
            color: white;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .metric-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        .chart-container {
            position: relative;
            margin: auto;
        }
    </style>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Trend Penilaian Chart
        const trendCtx = document.getElementById('trendPenilaianChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($trendPenilaian, 'bulan')) !!},
                datasets: [
                    {
                        label: 'Kompeten',
                        data: {!! json_encode(array_column($trendPenilaian, 'kompeten')) !!},
                        borderColor: 'rgb(17, 153, 142)',
                        backgroundColor: 'rgba(17, 153, 142, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Tidak Kompeten',
                        data: {!! json_encode(array_column($trendPenilaian, 'tidak_kompeten')) !!},
                        borderColor: 'rgb(245, 87, 108)',
                        backgroundColor: 'rgba(245, 87, 108, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Performance Pie Chart
        const pieCtx = document.getElementById('performancePieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Kompeten', 'Tidak Kompeten'],
                datasets: [{
                    data: [{{ $performanceSummary['total_kompeten'] }}, {{ $performanceSummary['total_tidak_kompeten'] }}],
                    backgroundColor: [
                        'rgba(17, 153, 142, 0.8)',
                        'rgba(245, 87, 108, 0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Distribusi Skema Chart
        const skemaCtx = document.getElementById('distribusiSkemaChart').getContext('2d');
        new Chart(skemaCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($distribusiSkema->pluck('nama')->toArray()) !!},
                datasets: [{
                    label: 'Jumlah Asesi',
                    data: {!! json_encode($distribusiSkema->pluck('jumlah')->toArray()) !!},
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Workload Chart
        const workloadCtx = document.getElementById('workloadChart').getContext('2d');
        new Chart(workloadCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($workloadAnalysis, 'bulan')) !!},
                datasets: [{
                    label: 'Jumlah Asesi',
                    data: {!! json_encode(array_column($workloadAnalysis, 'jumlah')) !!},
                    backgroundColor: 'rgba(240, 147, 251, 0.8)',
                    borderColor: 'rgba(240, 147, 251, 1)',
                    borderWidth: 1
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
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
    @endpush

@endsection
