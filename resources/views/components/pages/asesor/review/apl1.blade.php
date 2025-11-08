@extends('components.templates.master-layout')

@section('title', 'Review APL1')
@section('page-title', 'Review APL1 - Form Asesor')

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

    <!-- APL1 Review Form -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-file-alt mr-2"></i> Form Review APL1
            </h6>
            <small>Isi dan berikan penilaian untuk setiap item yang ditugaskan kepada Anda</small>
        </div>
        <div class="card-body">
            <form action="{{ route('asesor.review.store-apl1', $pendaftaran->id) }}" method="POST" id="apl1ReviewForm">
                @csrf

                {{-- Display Asesi's Data (Read-only) --}}
                @if($template->custom_variables && count($template->custom_variables) > 0)
                    @php
                        $hasAsesiData = false;
                        $hasAsesorData = false;
                    @endphp

                    {{-- First, show all asesi-filled fields --}}
                    @foreach($template->custom_variables as $index => $variable)
                        @php
                            $variableRole = $variable['role'] ?? 'asesi';
                            if ($variableRole === 'asesi' || $variableRole === 'both') {
                                $hasAsesiData = true;
                                break;
                            }
                        @endphp
                    @endforeach

                    @if($hasAsesiData)
                        <div class="mb-5">
                            <h5 class="text-primary border-bottom pb-2 mb-4">
                                <i class="fas fa-eye mr-2"></i> Data dari Asesi
                            </h5>
                            <p class="text-muted small mb-4">Berikut adalah data yang telah diisi oleh asesi</p>

                            @foreach($template->custom_variables as $index => $variable)
                                @php
                                    $variableRole = $variable['role'] ?? 'asesi';
                                    if ($variableRole !== 'asesi' && $variableRole !== 'both') {
                                        continue;
                                    }

                                    // Skip signature_pad
                                    if (isset($variable['type']) && $variable['type'] === 'signature_pad') {
                                        continue;
                                    }

                                    $asesiValue = $pendaftaran->custom_variables[$variable['name']] ?? '-';
                                @endphp

                                <div class="card mb-3 border-left-info">
                                    <div class="card-body">
                                        <label class="form-label font-weight-bold text-dark">
                                            {{ $variable['label'] }}
                                        </label>
                                        <div class="form-control-plaintext bg-light p-3 rounded border">
                                            {{ $asesiValue }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Show Asesi's Signature --}}
                            @if($pendaftaran->ttd_asesi_path)
                                <div class="card mb-3 border-left-info">
                                    <div class="card-body">
                                        <label class="form-label font-weight-bold text-dark">
                                            <i class="fas fa-signature mr-2"></i> Tanda Tangan Asesi
                                        </label>
                                        <div class="border p-3 bg-light rounded">
                                            <img src="{{ asset('storage/' . $pendaftaran->ttd_asesi_path) }}"
                                                 alt="Tanda Tangan Asesi"
                                                 class="img-fluid"
                                                 style="max-height: 150px; background: white; padding: 10px; border: 1px solid #dee2e6;">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Then, show fields to be filled by asesor --}}
                    @foreach($template->custom_variables as $index => $variable)
                        @php
                            $variableRole = $variable['role'] ?? 'asesi';
                            if ($variableRole === 'asesor' || $variableRole === 'both') {
                                $hasAsesorData = true;
                                break;
                            }
                        @endphp
                    @endforeach

                    @if($hasAsesorData)
                        <div class="mb-5">
                            <h5 class="text-success border-bottom pb-2 mb-4">
                                <i class="fas fa-edit mr-2"></i> Bagian Asesor
                            </h5>
                            <p class="text-muted small mb-4">Isi bagian berikut sesuai dengan hasil penilaian Anda</p>

                            @foreach($template->custom_variables as $index => $variable)
                                @php
                                    $variableRole = $variable['role'] ?? 'asesi';
                                    if ($variableRole !== 'asesor' && $variableRole !== 'both') {
                                        continue;
                                    }

                                    // Skip signature_pad (handled separately)
                                    if (isset($variable['type']) && $variable['type'] === 'signature_pad') {
                                        continue;
                                    }

                                    $asesorValue = $pendaftaran->asesor_data[$variable['name']] ?? '';
                                @endphp

                                <div class="card mb-3 border-left-success">
                                    <div class="card-body">
                                        <label class="form-label font-weight-bold text-dark" for="asesor_var_{{ $index }}">
                                            {{ $variable['label'] }}
                                            @if(isset($variable['required']) && $variable['required'])
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        @if(isset($variable['type']) && $variable['type'] === 'textarea')
                                            <textarea class="form-control"
                                                      name="asesor_variables[{{ $variable['name'] }}]"
                                                      id="asesor_var_{{ $index }}"
                                                      rows="4"
                                                      placeholder="Masukkan {{ strtolower($variable['label']) }}..."
                                                      {{ isset($variable['required']) && $variable['required'] ? 'required' : '' }}>{{ $asesorValue }}</textarea>
                                        @else
                                            <input type="text"
                                                   class="form-control"
                                                   name="asesor_variables[{{ $variable['name'] }}]"
                                                   id="asesor_var_{{ $index }}"
                                                   value="{{ $asesorValue }}"
                                                   placeholder="Masukkan {{ strtolower($variable['label']) }}..."
                                                   {{ isset($variable['required']) && $variable['required'] ? 'required' : '' }}>
                                        @endif

                                        @if(isset($variable['description']))
                                            <small class="form-text text-muted">{{ $variable['description'] }}</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Asesor Signature --}}
                    <div class="mb-4">
                        <h5 class="text-success border-bottom pb-2 mb-4">
                            <i class="fas fa-signature mr-2"></i> Tanda Tangan Asesor
                        </h5>

                        @if($pendaftaran->ttd_asesor_path)
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle mr-2"></i> Tanda tangan sudah tersimpan
                            </div>
                            <div class="border p-3 bg-light rounded mb-3">
                                <img src="{{ asset('storage/' . $pendaftaran->ttd_asesor_path) }}"
                                     alt="Tanda Tangan Asesor"
                                     class="img-fluid"
                                     style="max-height: 150px; background: white; padding: 10px; border: 1px solid #dee2e6;">
                            </div>
                            <button type="button" class="btn btn-warning btn-sm" id="changeSignatureBtn">
                                <i class="fas fa-edit mr-1"></i> Ubah Tanda Tangan
                            </button>
                        @endif

                        <div id="signaturePadContainer" class="{{ $pendaftaran->ttd_asesor_path ? 'd-none' : '' }}">
                            <div class="border rounded p-3 bg-white mb-2" style="max-width: 750px;">
                                <canvas id="signaturePad"></canvas>
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary" id="clearSignatureBtn">
                                <i class="fas fa-eraser mr-1"></i> Hapus Tanda Tangan
                            </button>
                            <input type="hidden" name="signature_data" id="signatureData">
                        </div>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="mt-4 pt-4 border-top">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('asesor.review.show-asesi', $pendaftaran->jadwal_id) }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                        <div>
                            <a href="{{ route('asesor.review.generate-apl1', $pendaftaran->id) }}"
                               class="btn btn-info mr-2"
                               target="_blank">
                                <i class="fas fa-download mr-2"></i> Generate APL1
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-2"></i> Simpan Review
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .border-left-info {
            border-left: 4px solid #36b9cc !important;
        }

        .border-left-success {
            border-left: 4px solid #1cc88a !important;
        }

        .signature-pad {
            border: 2px dashed #ddd;
            border-radius: 4px;
            cursor: crosshair;
        }

        .table-borderless td {
            padding: 0.5rem 0;
        }
    </style>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
        <script>
            $(document).ready(function() {
                // Initialize Signature Pad
                const canvas = document.getElementById('signaturePad');
                const signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)'
                });

                // Clear signature
                $('#clearSignatureBtn').on('click', function() {
                    signaturePad.clear();
                    $('#signatureData').val('');
                });

                // Show signature pad for changing
                $('#changeSignatureBtn').on('click', function() {
                    $('#signaturePadContainer').removeClass('d-none');
                    $(this).hide();
                });

                // Form submission
                $('#apl1ReviewForm').on('submit', function(e) {
                    // Save signature data if signed
                    if (!signaturePad.isEmpty()) {
                        const dataURL = signaturePad.toDataURL();
                        $('#signatureData').val(dataURL);
                    }

                    // Validate form
                    if (!this.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                        alert('Mohon lengkapi semua field yang wajib diisi');
                        return false;
                    }

                    // Confirm submission
                    if (!confirm('Apakah Anda yakin ingin menyimpan review APL1 ini?')) {
                        e.preventDefault();
                        return false;
                    }
                });

                // Resize canvas on window resize
                function resizeCanvas() {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    const canvas = document.getElementById('signaturePad');
                    if (canvas) {
                        canvas.width = canvas.offsetWidth * ratio;
                        canvas.height = canvas.offsetHeight * ratio;
                        canvas.getContext("2d").scale(ratio, ratio);
                    }
                }

                window.addEventListener("resize", resizeCanvas);
                resizeCanvas();
            });
        </script>
    @endpush
@endsection
