@extends('components.templates.master-layout')

@section('title', 'Detail Skema - ' . $skema->nama)
@section('page-title', 'Detail Skema Sertifikasi')

@section('content')
    <a href="{{ route('asesi.skema.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-primary mr-2"></i>
        <span class="text-primary">Kembali ke Daftar Skema</span>
    </a>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-certificate mr-2"></i> {{ $skema->nama }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-primary mb-3">Informasi Skema</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold" style="width: 150px;">
                                        <i class="fas fa-code text-primary mr-2"></i> Kode Skema
                                    </td>
                                    <td>: {{ $skema->kode }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">
                                        <i class="fas fa-tag text-primary mr-2"></i> Kategori
                                    </td>
                                    <td>: {{ $skema->kategori ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">
                                        <i class="fas fa-book text-primary mr-2"></i> Bidang
                                    </td>
                                    <td>: {{ $skema->bidang ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-primary mb-3">Status</h6>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle mr-2"></i>
                                Skema ini aktif dan tersedia untuk pendaftaran
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-info-circle mr-2"></i> Deskripsi Skema
                            </h6>

                            @if($skema->deskripsi)
                                <div class="bg-light p-4 rounded border">
                                    <p class="text-justify mb-0" style="white-space: pre-line;">{{ $skema->deskripsi }}</p>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Deskripsi belum tersedia untuk skema ini.
                                </div>
                            @endif
                        </div>
                    </div>

                    @php
                        // Cek apakah ada jadwal aktif untuk skema ini
                        $jadwalAktif = \App\Models\Jadwal::where('skema_id', $skema->id)
                            ->where('status', 1)
                            ->where('tanggal_ujian', '>', now())
                            ->exists();
                    @endphp

                    @if($jadwalAktif)
                        <div class="mt-4 pt-4 border-top">
                            <div class="alert alert-info">
                                <i class="fas fa-calendar-check mr-2"></i>
                                <strong>Jadwal Tersedia!</strong> Ada jadwal ujian yang tersedia untuk skema ini.
                            </div>
                            <a href="{{ route('asesi.daftar-ujikom.create') }}" class="btn btn-primary">
                                <i class="fas fa-file-signature mr-2"></i> Daftar Ujikom Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .table-borderless td {
            padding: 0.75rem 0;
        }

        .bg-light {
            background-color: #f8f9fc !important;
        }
    </style>
@endsection
