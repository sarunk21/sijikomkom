@extends('components.templates.master-layout')

@section('title', 'Daftar Asesi - Review')
@section('page-title', 'Daftar Asesi - Review & Verifikasi')

@section('content')
    <a href="{{ route('asesor.review.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-primary mr-2"></i>
        <span class="text-primary">Kembali ke Daftar Jadwal</span>
    </a>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Jadwal Info Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-info-circle mr-2"></i> Informasi Jadwal Ujian
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="font-weight-bold" style="width: 150px;">Skema</td>
                            <td>: {{ $jadwal->jadwal->skema->nama }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Kode Skema</td>
                            <td>: {{ $jadwal->jadwal->skema->kode }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">TUK</td>
                            <td>: {{ $jadwal->jadwal->tuk->nama }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="font-weight-bold" style="width: 150px;">Tanggal Ujian</td>
                            <td>: {{ \Carbon\Carbon::parse($jadwal->jadwal->tanggal_ujian)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Waktu</td>
                            <td>: {{ \Carbon\Carbon::parse($jadwal->jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jadwal->waktu_selesai)->format('H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Jumlah Asesi</td>
                            <td>: {{ $asesiList->count() }} peserta</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Asesi List Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users mr-2"></i> Daftar Asesi
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="asesiTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>NIM/NIK</th>
                            <th>Nama Asesi</th>
                            <th>Email</th>
                            <th>Status APL1</th>
                            <th>Status APL2</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($asesiList as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->asesi->nim ?? $item->asesi->nik ?? '-' }}</td>
                                <td>
                                    <strong>{{ $item->asesi->name }}</strong>
                                </td>
                                <td>{{ $item->asesi->email }}</td>
                                <td class="text-center">
                                    @if($item->has_apl1)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle mr-1"></i> Terisi
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-clock mr-1"></i> Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->has_apl2)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle mr-1"></i> Terisi
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-clock mr-1"></i> Belum
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($item->pendaftaran_id)
                                            @if($item->has_apl1)
                                                <a href="{{ route('asesor.review.apl1', $item->pendaftaran_id) }}"
                                                    class="btn btn-sm btn-info"
                                                    title="Review APL1">
                                                    <i class="fas fa-file-alt mr-1"></i> APL1
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-secondary" disabled title="APL1 belum diisi asesi">
                                                    <i class="fas fa-file-alt mr-1"></i> APL1
                                                </button>
                                            @endif

                                            @if($item->has_apl2)
                                                <a href="{{ route('asesor.review.apl2', $item->pendaftaran_id) }}"
                                                    class="btn btn-sm btn-warning"
                                                    title="Review APL2">
                                                    <i class="fas fa-file-contract mr-1"></i> APL2
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-secondary" disabled title="APL2 belum diisi asesi">
                                                    <i class="fas fa-file-contract mr-1"></i> APL2
                                                </button>
                                            @endif
                                        @else
                                            <span class="text-muted small">Data tidak tersedia</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .badge {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .btn-group .btn {
            margin: 0 2px;
        }

        .table td {
            vertical-align: middle;
        }

        .table-borderless td {
            padding: 0.5rem 0;
        }
    </style>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#asesiTable').DataTable({
                    responsive: true,
                    ordering: false,
                    language: {
                        searchPlaceholder: "Cari asesi...",
                        search: "",
                        lengthMenu: "_MENU_ asesi per halaman",
                        zeroRecords: "Asesi tidak ditemukan",
                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ asesi",
                        infoEmpty: "Tidak ada data",
                        paginate: {
                            previous: "Sebelumnya",
                            next: "Berikutnya"
                        }
                    },
                    columnDefs: [{
                        targets: -1, // Last column (actions)
                        orderable: false
                    }]
                });
            });
        </script>
    @endpush
@endsection
