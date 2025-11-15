@extends('components.templates.master-layout')

@section('title', 'Lihat Formulir')
@section('page-title', 'Lihat Formulir - ' . $bankSoal->nama)

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-check-circle mr-2"></i>{{ $bankSoal->nama }} (Sudah Disubmit)
            </h5>
        </div>
        <div class="card-body">
            <div class="alert alert-success">
                <i class="fas fa-info-circle mr-2"></i>
                Formulir ini sudah disubmit pada <strong>{{ $response->submitted_at->format('d/m/Y H:i') }}</strong> dan tidak dapat diubah lagi.
            </div>

            @if ($bankSoal->keterangan)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>{{ $bankSoal->keterangan }}
                </div>
            @endif

            <div class="row">
                @foreach ($customFields as $field)
                    <div class="col-md-{{ $field['width'] ?? 12 }} mb-4">
                        <label class="font-weight-bold text-muted">{{ $field['label'] }}</label>

                        @if ($field['type'] === 'signature_pad')
                            <div class="border rounded p-2 bg-light">
                                @if (isset($response->asesi_responses[$field['name']]) && $response->asesi_responses[$field['name']])
                                    <img src="{{ $response->asesi_responses[$field['name']] }}" alt="Signature"
                                        style="max-width: 100%; border: 1px solid #ddd;">
                                @else
                                    <p class="text-muted mb-0">Tidak ada tanda tangan</p>
                                @endif
                            </div>

                        @elseif ($field['type'] === 'file')
                            @if (isset($response->asesi_responses[$field['name']]) && $response->asesi_responses[$field['name']])
                                <div class="border rounded p-3 bg-light">
                                    <i class="fas fa-file mr-2"></i>
                                    <a href="{{ Storage::url($response->asesi_responses[$field['name']]) }}"
                                        target="_blank">
                                        {{ basename($response->asesi_responses[$field['name']]) }}
                                    </a>
                                </div>
                            @else
                                <p class="text-muted mb-0">Tidak ada file</p>
                            @endif

                        @elseif ($field['type'] === 'checkbox')
                            <div class="border rounded p-3 bg-light">
                                @if (isset($response->asesi_responses[$field['name']]) && is_array($response->asesi_responses[$field['name']]))
                                    <ul class="mb-0">
                                        @foreach ($response->asesi_responses[$field['name']] as $value)
                                            <li>{{ $value }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted mb-0">Tidak ada pilihan</p>
                                @endif
                            </div>

                        @elseif ($field['type'] === 'textarea')
                            <div class="border rounded p-3 bg-light" style="white-space: pre-wrap;">{{ $response->asesi_responses[$field['name']] ?? '-' }}</div>

                        @else
                            <div class="border rounded p-3 bg-light">
                                {{ $response->asesi_responses[$field['name']] ?? '-' }}
                            </div>
                        @endif

                        @if (isset($field['description']) && $field['description'])
                            <small class="form-text text-muted">{{ $field['description'] }}</small>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($response->catatan_asesor)
                <hr class="my-4">
                <div class="alert alert-warning">
                    <h6 class="font-weight-bold">
                        <i class="fas fa-comment mr-2"></i>Catatan Asesor:
                    </h6>
                    <p class="mb-0">{{ $response->catatan_asesor }}</p>
                </div>
            @endif

            <hr class="my-4">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('asesi.formulir.index', $jadwal->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali
                </a>

                <div class="text-muted">
                    <small>
                        <i class="fas fa-clock mr-1"></i>
                        Disubmit: {{ $response->submitted_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection
