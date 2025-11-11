@extends('components.templates.master-layout')

@section('title', 'Generate FR AK 05')
@section('page-title', 'Generate FR AK 05')

@section('content')
    <a href="{{ route('asesor.hasil-ujikom.show', $jadwal->id) }}"
       class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali</span>
    </a>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-file-alt me-2"></i>Generate FR AK 05 - Form Asesmen
            </h5>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('asesor.fr-ak-05.generate', $jadwal->id) }}" method="POST" id="frAk05Form">
                @csrf

                <!-- Info Jadwal -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3 text-primary">
                        <i class="fas fa-info-circle me-2"></i>Informasi Jadwal Asesmen
                    </h5>
                    <div class="card border-primary" style="border-width: 2px;">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted" style="width: 150px;">Skema Sertifikasi</td>
                                                <td class="fw-bold">: {{ $jadwal->skema->nama }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">TUK</td>
                                                <td class="fw-bold">: {{ $jadwal->tuk->nama }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Tanggal Ujian</td>
                                                <td class="fw-bold">: {{ $jadwal->tanggal_ujian }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted" style="width: 150px;">Total Asesi</td>
                                                <td class="fw-bold">: {{ $asesiList->count() }} orang</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Kompeten</td>
                                                <td>: <span class="badge" style="background-color: #28a745; color: white; padding: 8px 16px;">{{ $kompeten }} orang</span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Tidak Kompeten</td>
                                                <td>: <span class="badge" style="background-color: #dc3545; color: white; padding: 8px 16px;">{{ $tidakKompeten }} orang</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Asesi -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3 text-primary">
                        <i class="fas fa-users me-2"></i>Daftar Asesi yang Dinilai
                    </h5>
                    <div class="alert alert-info d-flex align-items-center mb-3">
                        <i class="fas fa-info-circle me-2 fs-5"></i>
                        <span>Anda dapat menambahkan keterangan untuk setiap asesi (opsional). Keterangan ini akan muncul di dokumen FR AK 05.</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th style="width: 60px;" class="text-center">No</th>
                                    <th>Nama Asesi</th>
                                    <th style="width: 140px;">NIM</th>
                                    <th style="width: 180px;" class="text-center">Status</th>
                                    <th style="width: 350px;">Keterangan (Opsional)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @foreach($asesiList as $index => $item)
                                <tr>
                                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ $item->asesi->name }}</td>
                                    <td class="text-muted">{{ $item->asesi->nim }}</td>
                                    <td class="text-center">
                                        @if($item->status == 5)
                                            <span class="badge" style="background-color: #28a745; color: white; padding: 10px 18px; font-size: 14px; font-weight: 600;">
                                                <i class="fas fa-check-circle me-1"></i>Kompeten (K)
                                            </span>
                                        @else
                                            <span class="badge" style="background-color: #dc3545; color: white; padding: 10px 18px; font-size: 14px; font-weight: 600;">
                                                <i class="fas fa-times-circle me-1"></i>Tidak Kompeten (BK)
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="keterangan_{{ $item->id }}"
                                            placeholder="Tambahkan catatan..."
                                            value="{{ old('keterangan_' . $item->id) }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Custom Fields -->
                @if(count($customVariables) > 0)
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-edit text-primary me-2"></i>Keterangan dan Data Tambahan
                    </h6>
                    <p class="text-muted small mb-3">
                        <i class="fas fa-info-circle"></i> Isi field di bawah ini sesuai kebutuhan. Field yang ditandai dengan (*) wajib diisi.
                    </p>

                    <div class="row">
                        @foreach($customVariables as $variable)
                            <div class="col-md-6 mb-3">
                                <label for="{{ $variable['name'] }}" class="form-label fw-semibold">
                                    {{ $variable['label'] ?? $variable['name'] }}
                                    @if(isset($variable['required']) && $variable['required'])
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>

                                @php
                                    // Auto-fill untuk field TTD dengan nama asesor
                                    $autoFillValue = old($variable['name']);
                                    if (empty($autoFillValue) && (
                                        str_contains(strtolower($variable['name']), 'ttd') ||
                                        str_contains(strtolower($variable['name']), 'tanda_tangan') ||
                                        str_contains(strtolower($variable['label'] ?? ''), 'Tanda Tangan')
                                    )) {
                                        $autoFillValue = Auth::user()->name;
                                    }
                                @endphp

                                @if($variable['type'] == 'textarea')
                                    <textarea
                                        class="form-control"
                                        id="{{ $variable['name'] }}"
                                        name="{{ $variable['name'] }}"
                                        rows="3"
                                        placeholder="Masukkan {{ strtolower($variable['label'] ?? $variable['name']) }}"
                                        {{ (isset($variable['required']) && $variable['required']) ? 'required' : '' }}>{{ $autoFillValue }}</textarea>
                                @elseif($variable['type'] == 'number')
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="{{ $variable['name'] }}"
                                        name="{{ $variable['name'] }}"
                                        value="{{ $autoFillValue }}"
                                        placeholder="Masukkan {{ strtolower($variable['label'] ?? $variable['name']) }}"
                                        {{ (isset($variable['required']) && $variable['required']) ? 'required' : '' }}>
                                @elseif($variable['type'] == 'date')
                                    <input
                                        type="date"
                                        class="form-control"
                                        id="{{ $variable['name'] }}"
                                        name="{{ $variable['name'] }}"
                                        value="{{ $autoFillValue }}"
                                        {{ (isset($variable['required']) && $variable['required']) ? 'required' : '' }}>
                                @elseif($variable['type'] == 'select' && isset($variable['options']))
                                    <select
                                        class="form-control"
                                        id="{{ $variable['name'] }}"
                                        name="{{ $variable['name'] }}"
                                        {{ (isset($variable['required']) && $variable['required']) ? 'required' : '' }}>
                                        <option value="">Pilih {{ $variable['label'] ?? $variable['name'] }}</option>
                                        @foreach(explode(',', $variable['options']) as $option)
                                            <option value="{{ trim($option) }}" {{ old($variable['name']) == trim($option) ? 'selected' : '' }}>
                                                {{ trim($option) }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="{{ $variable['name'] }}"
                                        name="{{ $variable['name'] }}"
                                        value="{{ $autoFillValue }}"
                                        placeholder="Masukkan {{ strtolower($variable['label'] ?? $variable['name']) }}"
                                        {{ (isset($variable['required']) && $variable['required']) ? 'required' : '' }}>
                                @endif

                                @if(isset($variable['description']))
                                    <small class="form-text text-muted">{{ $variable['description'] }}</small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Signature Pad -->
                @if($signatureFieldName)
                <div class="mb-4">
                    <h5 class="fw-bold mb-3 text-primary">
                        <i class="fas fa-signature me-2"></i>Tanda Tangan Asesor <span class="text-danger">*</span>
                    </h5>
                    <div class="card border-warning" style="border-width: 2px;">
                        <div class="card-body p-4">
                            <div class="alert alert-warning d-flex align-items-center mb-4">
                                <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
                                <span><strong>WAJIB:</strong> Silakan tanda tangani di area di bawah ini. Tanda tangan akan disertakan dalam dokumen FR AK 05.</span>
                            </div>
                            <div class="text-center mb-3">
                                <div class="border border-dark bg-white rounded shadow-sm" style="display: inline-block; padding: 10px;">
                                    <canvas id="signaturePad" width="700" height="200"></canvas>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-danger" id="clearSignature">
                                    <i class="fas fa-eraser me-2"></i>Hapus Tanda Tangan
                                </button>
                            </div>
                            <input type="hidden" name="{{ $signatureFieldName }}" id="ttdAsesor">
                            <div id="signatureError" class="alert alert-danger mt-3" style="display: none;">
                                <i class="fas fa-exclamation-circle me-2"></i><strong>Error:</strong> Tanda tangan wajib diisi sebelum generate dokumen!
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Auto-filled Data Info -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3 text-primary">
                        <i class="fas fa-robot me-2"></i>Data yang Otomatis Terisi oleh Sistem
                    </h5>
                    <div class="card border-success" style="border-width: 2px;">
                        <div class="card-body p-4">
                            <div class="alert alert-success d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle me-2 fs-5"></i>
                                <span>Data berikut akan <strong>otomatis terisi</strong> di dokumen FR AK 05 tanpa perlu diisi manual.</span>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body">
                                            <h6 class="fw-bold text-success mb-3">
                                                <i class="fas fa-users me-2"></i>Data Asesi
                                            </h6>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2 d-flex align-items-start">
                                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                                    <span><strong>Daftar Asesi:</strong> Semua nama asesi yang dinilai</span>
                                                </li>
                                                <li class="mb-2 d-flex align-items-start">
                                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                                    <span><strong>Kompeten:</strong> {{ $kompeten }} asesi</span>
                                                </li>
                                                <li class="mb-2 d-flex align-items-start">
                                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                                    <span><strong>Tidak Kompeten:</strong> {{ $tidakKompeten }} asesi</span>
                                                </li>
                                                <li class="d-flex align-items-start">
                                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                                    <span><strong>Total Asesi:</strong> {{ $asesiList->count() }} asesi</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body">
                                            <h6 class="fw-bold text-success mb-3">
                                                <i class="fas fa-info-circle me-2"></i>Data Asesmen
                                            </h6>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2 d-flex align-items-start">
                                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                                    <span><strong>Skema:</strong> {{ $jadwal->skema->nama }}</span>
                                                </li>
                                                <li class="mb-2 d-flex align-items-start">
                                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                                    <span><strong>Tanggal Ujian:</strong> {{ $jadwal->tanggal_ujian }}</span>
                                                </li>
                                                <li class="mb-2 d-flex align-items-start">
                                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                                    <span><strong>TUK:</strong> {{ $jadwal->tuk->nama }}</span>
                                                </li>
                                                <li class="d-flex align-items-start">
                                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                                    <span><strong>Asesor:</strong> {{ Auth::user()->name }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-between align-items-center pt-4 border-top mt-4">
                    <a href="{{ route('asesor.hasil-ujikom.show', $jadwal->id) }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <button type="submit" class="btn btn-success btn-lg px-5" id="submitBtn">
                        <i class="fas fa-download me-2"></i>Generate & Download FR AK 05
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .badge {
            padding: 0.35rem 0.65rem;
        }

        #signaturePad {
            border: 3px dashed #adb5bd;
            border-radius: 4px;
            cursor: crosshair;
            touch-action: none;
            background: #fafafa;
        }

        #signaturePad:hover {
            border-color: #495057;
            background: #ffffff;
        }

        .fw-semibold {
            font-weight: 600;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .card {
            transition: all 0.3s ease;
        }

        .btn-lg {
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
        }

        h5.text-primary {
            border-left: 4px solid #0d6efd;
            padding-left: 12px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('signaturePad');

            // Set canvas size properly
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = 700 * ratio;
            canvas.height = 200 * ratio;
            canvas.style.width = '700px';
            canvas.style.height = '200px';
            const ctx = canvas.getContext('2d');
            ctx.scale(ratio, ratio);

            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)',
                minWidth: 1.5,
                maxWidth: 3
            });

            // Clear button
            document.getElementById('clearSignature').addEventListener('click', function() {
                signaturePad.clear();
                document.getElementById('ttdAsesor').value = '';
                document.getElementById('signatureError').style.display = 'none';
            });

            // Form submission
            document.getElementById('frAk05Form').addEventListener('submit', function(e) {
                // Check if signature is empty
                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    document.getElementById('signatureError').style.display = 'block';

                    // Scroll to signature pad
                    canvas.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    // Show alert
                    alert('Silakan tanda tangani terlebih dahulu sebelum generate dokumen.');
                    return false;
                }

                // Save signature as base64
                const signatureData = signaturePad.toDataURL();
                document.getElementById('ttdAsesor').value = signatureData;

                // Disable submit button to prevent double submission
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';

                // Re-enable button after timeout (in case download doesn't trigger properly)
                setTimeout(function() {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-download me-2"></i>Generate & Download FR AK 05';
                }, 10000); // 10 seconds timeout
            });
        });
    </script>
@endsection
