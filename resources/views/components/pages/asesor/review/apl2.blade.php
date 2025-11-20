@extends('components.templates.master-layout')

@section('title', 'Review APL2')
@section('page-title', 'Review APL2 - Form Asesor')

@section('content')
    <a href="{{ route('asesor.review.show-asesi', $pendaftaran->jadwal_id) }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-primary mr-2"></i>
        <span class="text-primary">Kembali ke Daftar Asesi</span>
    </a>

    @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center shadow-sm mb-4" style="border-left: 4px solid #dc3545;">
            <i class="fas fa-exclamation-circle me-3" style="font-size: 1.5rem;"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center shadow-sm mb-4" style="border-left: 4px solid #28a745;">
            <i class="fas fa-check-circle me-3" style="font-size: 1.5rem;"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- Asesi Info Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-user mr-2"></i> Informasi Asesi
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="font-weight-bold" style="width: 150px;">Nama</td>
                            <td>: {{ $pendaftaran->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">NIM/NIK</td>
                            <td>: {{ $pendaftaran->user->nim ?? $pendaftaran->user->nik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Email</td>
                            <td>: {{ $pendaftaran->user->email }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="font-weight-bold" style="width: 150px;">Skema</td>
                            <td>: {{ $pendaftaran->skema->nama }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Tanggal Ujian</td>
                            <td>: {{ \Carbon\Carbon::parse($pendaftaran->jadwal->tanggal_ujian)->format('d F Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- APL2 Review Form -->
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-file-contract mr-2"></i> Data APL2 dari Asesi
            </h6>
            <small>Data yang telah diisi oleh asesi (Read-Only)</small>
        </div>
        <div class="card-body">
            @php
                // Use template custom_variables for APL2
                $customVariables = $template->custom_variables ?? [];

                // Get asesi responses from custom_variables
                $asesiResponses = $pendaftaran->custom_variables ?? [];
            @endphp

            @if($customVariables && count($customVariables) > 0)
                @foreach($customVariables as $index => $variable)
                    @php
                        $variableRole = $variable['role'] ?? 'asesi';

                        // Skip signature_pad
                        if (isset($variable['type']) && $variable['type'] === 'signature_pad') {
                            continue;
                        }

                        // Only show asesi fields (read-only)
                        if ($variableRole !== 'asesi' && $variableRole !== 'both') {
                            continue;
                        }

                        $value = $asesiResponses[$variable['name']] ?? '-';
                    @endphp

                    {{-- Display asesi's data (read-only) in card --}}
                    <div class="card mb-3 bg-light">
                        <div class="card-body">
                            <label class="form-label font-weight-bold mb-2">
                                {{ $variable['label'] }}
                            </label>

                            <div class="alert alert-secondary mb-0">
                                {{ $value }}
                            </div>

                            @if(isset($variable['description']))
                                <small class="form-text text-muted mt-2">{{ $variable['description'] }}</small>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Show asesi signature if exists --}}
                @if($pendaftaran->ttd_asesi_path)
                    <div class="card mb-3 bg-light">
                        <div class="card-body">
                            <label class="form-label font-weight-bold mb-2">
                                <i class="fas fa-signature mr-2"></i> Tanda Tangan Asesi
                            </label>
                            <div class="border p-3 bg-white rounded">
                                <img src="{{ asset('storage/' . $pendaftaran->ttd_asesi_path) }}"
                                     alt="Tanda Tangan Asesi"
                                     class="img-fluid"
                                     style="max-height: 150px;">
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Asesi belum mengisi APL2.
                </div>
            @endif

            {{-- Action Button --}}
            <div class="mt-4 pt-4 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('asesor.review.show-asesi', $pendaftaran->jadwal_id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    <a href="{{ route('asesor.review.generate-apl2', $pendaftaran->id) }}"
                       class="btn btn-primary"
                       target="_blank">
                        <i class="fas fa-download mr-2"></i> Generate APL2
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table-borderless td {
            padding: 0.5rem 0;
        }
    </style>
@endsection
