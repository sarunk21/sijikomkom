@extends('components.templates.master-layout')

@section('title', 'Informasi Jadwal Ujian')
@section('page-title', 'Informasi Jadwal Ujian')

@section('content')
    @php
        $lists = $lists ?? [];
        $activeMenu = $activeMenu ?? 'jadwal';
    @endphp

    <div class="mb-3">
        <a href="{{ route('admin.jadwal.create') }}" class="btn btn-dark"><i class="fas fa-plus mr-2"></i> Tambah Jadwal</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        @foreach ($pendaftaranUjikom as $item)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card shadow-sm h-100">
                    <!-- Card Header -->
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 font-weight-bold">{{ $item->asesor->name }}</h6>
                            <span class="badge badge-light text-dark">
                                {{ $item->pendaftaran->count() }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- Detail Jadwal -->
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">TUK</small>
                                    <p class="mb-1 font-weight-bold">{{ $item->jadwal->tuk->nama }}</p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Status</small>
                                    <p class="mb-1">
                                        @if ($item->status == 1)
                                            <span class="badge badge-warning">{{ $item->status_text }}</span>
                                        @elseif ($item->status == 2)
                                            <span class="badge badge-info">{{ $item->status_text }}</span>
                                        @elseif ($item->status == 3)
                                            <span class="badge badge-success">{{ $item->status_text }}</span>
                                        @elseif ($item->status == 4)
                                            <span class="badge badge-secondary">{{ $item->status_text }}</span>
                                        @elseif ($item->status == 5)
                                            <span class="badge badge-success">{{ $item->status_text }}</span>
                                        @elseif ($item->status == 6)
                                            <span class="badge badge-info">{{ $item->status_text }}</span>
                                        @elseif ($item->status == 7)
                                            <span class="badge badge-danger">{{ $item->status_text }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $item->status_text }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <small class="text-muted">Tanggal Ujian</small>
                                    <p class="mb-1 font-weight-bold">{{ \Carbon\Carbon::parse($item->tanggal_ujian)->format('d M Y H:i') }}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Selesai</small>
                                    <p class="mb-1">{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y H:i') }}</p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Deadline Daftar</small>
                                    <p class="mb-1">{{ \Carbon\Carbon::parse($item->tanggal_maksimal_pendaftaran)->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- List Asesi -->
                        <div class="mb-3">
                            <h6 class="font-weight-bold text-primary mb-2">
                                <i class="fas fa-users mr-1"></i> Daftar Peserta
                            </h6>

                            @if ($item->pendaftaran->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach ($item->pendaftaran as $pendaftaran)
                                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="mb-0 font-weight-bold">{{ $pendaftaran->user->name }}</p>
                                                <small class="text-muted">{{ $pendaftaran->user->nim }}</small>
                                            </div>
                                            <span class="badge badge-success">Terdaftar</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-users text-muted mb-2" style="font-size: 2rem;"></i>
                                    <p class="text-muted mb-0">Belum ada peserta terdaftar</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <style>
        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-info {
            background-color: #17a2b8;
            color: #fff;
        }

        .badge-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .badge-success {
            background-color: #28a745;
            color: #fff;
        }

        .badge-secondary {
            background-color: #6c757d;
            color: #fff;
        }

        .badge-primary {
            background-color: #007bff;
            color: #fff;
        }

        .card-header {
            border-bottom: none;
        }

        .list-group-item {
            border: none;
            border-bottom: 1px solid #e9ecef;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .btn-group-sm > .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    @endpush
@endsection
