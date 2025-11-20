@extends('components.templates.master-layout')

@section('title', 'Daftar Skema Sertifikasi')
@section('page-title', 'Daftar Skema Sertifikasi')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-certificate mr-2"></i> Daftar Skema Sertifikasi
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Berikut adalah daftar skema sertifikasi yang tersedia. Klik pada skema untuk melihat detail lengkapnya.
                    </p>

                    @if($skemas->count() > 0)
                        <div class="row">
                            @foreach($skemas as $skema)
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 shadow-sm border-left-primary">
                                        <div class="card-body">
                                            <h5 class="card-title text-primary font-weight-bold">
                                                {{ $skema->nama }}
                                            </h5>

                                            <div class="mb-3">
                                                <span class="badge badge-info mr-2">
                                                    <i class="fas fa-code mr-1"></i> {{ $skema->kode }}
                                                </span>
                                                @if($skema->kategori)
                                                    <span class="badge badge-secondary mr-2">
                                                        <i class="fas fa-tag mr-1"></i> {{ $skema->kategori }}
                                                    </span>
                                                @endif
                                                @if($skema->bidang)
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-book mr-1"></i> {{ $skema->bidang }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if($skema->deskripsi)
                                                <p class="card-text text-muted">
                                                    {{ Str::limit($skema->deskripsi, 150) }}
                                                </p>
                                            @else
                                                <p class="card-text text-muted">
                                                    <em>Deskripsi belum tersedia untuk skema ini.</em>
                                                </p>
                                            @endif

                                            <a href="{{ route('asesi.skema.show', $skema->id) }}"
                                               class="btn btn-primary btn-sm mt-2">
                                                <i class="fas fa-eye mr-1"></i> Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Belum ada skema sertifikasi yang tersedia saat ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .border-left-primary {
            border-left: 4px solid #4e73df !important;
        }

        .card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }

        .badge {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }
    </style>
@endsection
