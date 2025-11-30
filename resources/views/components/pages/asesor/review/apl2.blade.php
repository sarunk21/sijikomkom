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
                {{-- Section 1: Data Asesi (Read-Only) --}}
                @php
                    $hasAsesiFields = false;
                @endphp

                @foreach($customVariables as $variable)
                    @php
                        $variableRole = $variable['role'] ?? 'asesi';
                        if (isset($variable['type']) && $variable['type'] === 'signature_pad') continue;
                        if ($variableRole === 'asesi' || $variableRole === 'both') {
                            $hasAsesiFields = true;
                            break;
                        }
                    @endphp
                @endforeach

                @if($hasAsesiFields)
                    @foreach($customVariables as $index => $variable)
                        @php
                            $variableRole = $variable['role'] ?? 'asesi';
                            if (isset($variable['type']) && $variable['type'] === 'signature_pad') continue;
                            if ($variableRole !== 'asesi' && $variableRole !== 'both') continue;
                            $value = $asesiResponses[$variable['name']] ?? '-';
                        @endphp

                        <div class="card mb-3 bg-light border-left-info">
                            <div class="card-body">
                                <label class="form-label font-weight-bold mb-2">
                                    <i class="fas fa-user text-info mr-1"></i>
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
                        <div class="card mb-3 bg-light border-left-info">
                            <div class="card-body">
                                <label class="form-label font-weight-bold mb-2">
                                    <i class="fas fa-signature text-info mr-2"></i> Tanda Tangan Asesi
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
                @endif
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Asesi belum mengisi APL2.
                </div>
            @endif
        </div>
    </div>

    {{-- Section 2: Form untuk Asesor --}}
    @php
        $hasAsesorFields = false;
        if($customVariables && count($customVariables) > 0) {
            foreach($customVariables as $variable) {
                $variableRole = $variable['role'] ?? 'asesi';
                if ($variableRole === 'asesor' || $variableRole === 'both') {
                    $hasAsesorFields = true;
                    break;
                }
            }
        }
    @endphp

    @if($hasAsesorFields)
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-success text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-user-tie mr-2"></i> Input Data Asesor
            </h6>
            <small>Isi data berikut sebagai asesor</small>
        </div>
        <div class="card-body">
            <form action="{{ route('asesor.review.store-apl2', $pendaftaran->id) }}" method="POST">
                @csrf

                @foreach($customVariables as $index => $variable)
                    @php
                        $variableRole = $variable['role'] ?? 'asesi';

                        // Skip jika bukan untuk asesor
                        if ($variableRole !== 'asesor' && $variableRole !== 'both') {
                            continue;
                        }

                        // Skip signature_pad (akan ditangani terpisah)
                        if (isset($variable['type']) && $variable['type'] === 'signature_pad') {
                            continue;
                        }

                        $variableName = $variable['name'];
                        $variableLabel = $variable['label'];
                        $variableType = $variable['type'] ?? 'text';
                        $variableOptions = $variable['options'] ?? null;
                        $variableRequired = $variable['required'] ?? 'Ya';
                        $isRequired = ($variableRequired === 'Ya');

                        // Get current value from pendaftaran custom_variables
                        $currentValue = $asesiResponses[$variableName] ?? '';
                    @endphp

                    <div class="form-group">
                        <label class="font-weight-500">
                            <i class="fas fa-pen text-success mr-1"></i>
                            {{ $variableLabel }}
                            @if($isRequired)
                                <span class="text-danger">*</span>
                            @endif
                        </label>

                        @if($variableType === 'text' || $variableType === 'Text')
                            <input type="text"
                                   name="asesor_variables[{{ $variableName }}]"
                                   class="form-control"
                                   value="{{ old('asesor_variables.' . $variableName, $currentValue) }}"
                                   placeholder="Masukkan {{ $variableLabel }}"
                                   {{ $isRequired ? 'required' : '' }}>

                        @elseif($variableType === 'textarea' || $variableType === 'Textarea')
                            <textarea name="asesor_variables[{{ $variableName }}]"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Masukkan {{ $variableLabel }}"
                                      {{ $isRequired ? 'required' : '' }}>{{ old('asesor_variables.' . $variableName, $currentValue) }}</textarea>

                        @elseif($variableType === 'select' || $variableType === 'Select')
                            <select name="asesor_variables[{{ $variableName }}]"
                                    class="form-control"
                                    {{ $isRequired ? 'required' : '' }}>
                                <option value="">Pilih {{ $variableLabel }}</option>
                                @if($variableOptions)
                                    @foreach(explode(',', $variableOptions) as $option)
                                        <option value="{{ trim($option) }}"
                                                {{ old('asesor_variables.' . $variableName, $currentValue) == trim($option) ? 'selected' : '' }}>
                                            {{ trim($option) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>

                        @elseif($variableType === 'checkbox' || $variableType === 'Checkbox')
                            @if($variableOptions)
                                @foreach(explode(',', $variableOptions) as $option)
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="asesor_variables[{{ $variableName }}][]"
                                               value="{{ trim($option) }}"
                                               id="{{ $variableName }}_{{ $loop->index }}"
                                               {{ is_array(old('asesor_variables.' . $variableName, $currentValue)) && in_array(trim($option), old('asesor_variables.' . $variableName, $currentValue)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $variableName }}_{{ $loop->index }}">
                                            {{ trim($option) }}
                                        </label>
                                    </div>
                                @endforeach
                            @endif

                        @elseif($variableType === 'radio' || $variableType === 'Radio')
                            @if($variableOptions)
                                @foreach(explode(',', $variableOptions) as $option)
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="asesor_variables[{{ $variableName }}]"
                                               value="{{ trim($option) }}"
                                               id="{{ $variableName }}_{{ $loop->index }}"
                                               {{ old('asesor_variables.' . $variableName, $currentValue) == trim($option) ? 'checked' : '' }}
                                               {{ $isRequired ? 'required' : '' }}>
                                        <label class="form-check-label" for="{{ $variableName }}_{{ $loop->index }}">
                                            {{ trim($option) }}
                                        </label>
                                    </div>
                                @endforeach
                            @endif
                        @endif

                        @if(isset($variable['description']))
                            <small class="form-text text-muted">{{ $variable['description'] }}</small>
                        @endif
                    </div>
                @endforeach

                {{-- Tanda Tangan Asesor --}}
                @php
                    $hasAsesorSignature = false;
                    foreach($customVariables as $variable) {
                        if (isset($variable['type']) && $variable['type'] === 'signature_pad') {
                            $variableRole = $variable['role'] ?? 'asesi';
                            if ($variableRole === 'asesor' || $variableRole === 'both') {
                                $hasAsesorSignature = true;
                                break;
                            }
                        }
                    }
                @endphp

                @if($hasAsesorSignature)
                <div class="form-group">
                    <label class="font-weight-500">
                        <i class="fas fa-signature text-success mr-1"></i>
                        Tanda Tangan Asesor <span class="text-danger">*</span>
                    </label>

                    @if($pendaftaran->ttd_asesor_path)
                        <div class="mb-3 p-3 bg-light rounded border">
                            <small class="text-muted d-block mb-2">Tanda tangan saat ini:</small>
                            <img src="{{ asset('storage/' . $pendaftaran->ttd_asesor_path) }}"
                                 alt="Tanda Tangan Asesor"
                                 class="img-fluid border"
                                 style="max-height: 150px;">
                        </div>
                    @endif

                    <div class="border rounded bg-white" style="width: 100%; max-width: 600px;">
                        <canvas id="signaturePadAsesor" class="signature-pad"></canvas>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-secondary" id="clearSignatureAsesor">
                            <i class="fas fa-eraser mr-1"></i> Hapus Tanda Tangan
                        </button>
                    </div>
                    <input type="hidden" name="signature_data" id="signature_data" required>
                </div>
                @endif

                {{-- Action Buttons --}}
                <hr class="my-4">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <span class="text-danger">*</span> Wajib diisi
                    </small>
                    <div>
                        <a href="{{ route('asesor.review.show-asesi', $pendaftaran->jadwal_id) }}"
                           class="btn btn-outline-secondary mr-2">
                            <i class="fas fa-times mr-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fas fa-save mr-2"></i> Simpan Data Asesor
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Generate Button --}}
    <div class="mt-4 text-right">
        <a href="{{ route('asesor.review.generate-apl2', $pendaftaran->id) }}"
           class="btn btn-primary"
           target="_blank">
            <i class="fas fa-download mr-2"></i> Generate APL2
        </a>
    </div>

    <style>
        .table-borderless td {
            padding: 0.5rem 0;
        }
        .signature-pad {
            display: block;
            cursor: crosshair;
            touch-action: none;
        }
    </style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Signature Pad for Asesor
        const canvasAsesor = document.getElementById('signaturePadAsesor');
        if (canvasAsesor) {
            const signaturePadAsesor = new SignaturePad(canvasAsesor, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)'
            });

            // Clear button functionality
            document.getElementById('clearSignatureAsesor')?.addEventListener('click', function() {
                signaturePadAsesor.clear();
                document.getElementById('signature_data').value = '';
            });

            // Form submission - capture signature data
            const form = canvasAsesor.closest('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (signaturePadAsesor.isEmpty()) {
                        e.preventDefault();
                        alert('Mohon berikan tanda tangan asesor terlebih dahulu.');
                        return false;
                    }
                    // Save signature data as base64
                    const signatureData = signaturePadAsesor.toDataURL('image/png');
                    document.getElementById('signature_data').value = signatureData;
                });
            }

            // Set canvas size properly
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const canvas = canvasAsesor;

                // Set display size (css pixels)
                canvas.style.width = '100%';
                canvas.style.height = '200px';

                // Set actual size in memory (scaled to account for extra pixel density)
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * ratio;
                canvas.height = rect.height * ratio;

                // Scale all drawing operations by the dpr
                const ctx = canvas.getContext('2d');
                ctx.scale(ratio, ratio);

                // Reset signature pad
                signaturePadAsesor.clear();
            }

            // Initial resize
            resizeCanvas();

            // Resize on window resize
            window.addEventListener('resize', resizeCanvas);
        }
    });
</script>
@endpush
