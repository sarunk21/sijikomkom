@extends('components.templates.master-layout')

@section('title', 'Form APL 1')
@section('page-title', 'Form APL 1')

@section('content')

    <a href="{{ route('asesi.sertifikasi.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali ke Sertifikasi</span>
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

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-file-word mr-2"></i> Form APL 1 (Formulir Permohonan Sertifikasi Kompetensi)
                        </h6>
                        <small>
                            Skema: <strong>{{ $pendaftaran->skema->nama }}</strong>
                        </small>
                    </div>
                    <div class="card-body">
                        @if($allCustomVariablesFilled)
                            <!-- Semua data sudah lengkap, tampilkan tombol generate -->
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle mr-2"></i>
                                <strong>Data Lengkap!</strong> Semua data yang diperlukan sudah terisi. Anda dapat men-generate dokumen APL 1 sekarang.
                            </div>

                            <!-- Data yang Sudah Ada -->
                            @if(count($existingData) > 0)
                            <div class="mb-4">
                                <h6 class="text-success"><i class="fas fa-database mr-2"></i>Data yang Tersedia</h6>
                                <p class="small text-muted">Data berikut sudah tersedia dan akan digunakan dalam dokumen APL 1</p>

                                <div class="row">
                                    @foreach($existingData as $variable => $value)
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label text-muted">
                                                {{ ucwords(str_replace(['_', '.'], ' ', $variable)) }}
                                            </label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">
                                                <i class="fas fa-check-circle text-success mr-2"></i>
                                                {{ $value ?: '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Tanda Tangan Digital yang Sudah Disimpan -->
                            @if($pendaftaran->ttd_asesi_path)
                            <div class="mb-4">
                                <h6 class="text-success"><i class="fas fa-signature mr-2"></i>Tanda Tangan Digital</h6>
                                <p class="small text-muted">Tanda tangan digital Anda yang sudah tersimpan</p>
                                <div class="border p-3 bg-light rounded">
                                    <img src="{{ asset('storage/' . $pendaftaran->ttd_asesi_path) }}" 
                                         alt="Tanda Tangan Digital" 
                                         class="img-fluid"
                                         style="max-height: 150px; background: white; padding: 10px; border: 1px solid #dee2e6;">
                                </div>
                            </div>
                            @endif

                            <div class="text-center mt-4">
                                <a href="{{ route('asesi.template.apl1-download', $pendaftaran->id) }}"
                                    class="btn btn-success btn-lg">
                                    <i class="fas fa-download mr-2"></i> Generate & Download APL 1
                                </a>
                            </div>
                        @else
                            <!-- Ada data yang perlu diisi -->
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Lengkapi Data!</strong> Mohon lengkapi data berikut terlebih dahulu sebelum men-generate dokumen APL 1.
                            </div>

                            <form action="{{ route('asesi.template.apl1-store', $pendaftaran->id) }}" method="POST">
                                @csrf

                                <!-- Data yang Sudah Ada -->
                                @if(count($existingData) > 0)
                                <div class="mb-4">
                                    <h6 class="text-success"><i class="fas fa-check-circle mr-2"></i>Data yang Sudah Tersedia</h6>
                                    <p class="small text-muted">Data berikut sudah tersedia dari database</p>

                                    <div class="row">
                                        @foreach($existingData as $variable => $value)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label text-muted">
                                                    {{ ucwords(str_replace(['_', '.'], ' ', $variable)) }}
                                                </label>
                                                <div class="form-control-plaintext bg-light p-2 rounded">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    {{ $value ?: '-' }}
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- Dynamic Fields -->
                                @if(count($dynamicFields) > 0)
                                <div class="mb-4">
                                    <h6 class="text-primary"><i class="fas fa-edit mr-2"></i>Data yang Perlu Diisi</h6>
                                    <p class="small text-muted">Lengkapi field berikut untuk melengkapi dokumen APL 1</p>

                                    @foreach($dynamicFields as $field)
                                    <div class="mb-3">
                                        <label for="dynamic_{{ $field['name'] }}" class="form-label">
                                            {{ $field['label'] }}
                                            @if($field['required'])
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        @if($field['type'] === 'textarea')
                                            <textarea class="form-control @error('dynamic_fields.' . $field['name']) is-invalid @enderror"
                                                id="dynamic_{{ $field['name'] }}"
                                                name="dynamic_fields[{{ $field['name'] }}]"
                                                rows="3"
                                                placeholder="Masukkan {{ strtolower($field['label']) }}">{{ old('dynamic_fields.' . $field['name'], $pendaftaran->custom_variables[$field['name']] ?? '') }}</textarea>
                                        @elseif($field['type'] === 'checkbox')
                                            <div class="form-check-group">
                                                @if(isset($field['options']))
                                                    @foreach($field['options'] as $option)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="dynamic_fields[{{ $field['name'] }}][]"
                                                            value="{{ $option }}"
                                                            id="dynamic_{{ $field['name'] }}_{{ $loop->index }}">
                                                        <label class="form-check-label" for="dynamic_{{ $field['name'] }}_{{ $loop->index }}">
                                                            {{ $option }}
                                                        </label>
                                                    </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @elseif($field['type'] === 'radio')
                                            <div class="form-check-group">
                                                @if(isset($field['options']))
                                                    @foreach($field['options'] as $option)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="dynamic_fields[{{ $field['name'] }}]"
                                                            value="{{ $option }}"
                                                            id="dynamic_{{ $field['name'] }}_{{ $loop->index }}">
                                                        <label class="form-check-label" for="dynamic_{{ $field['name'] }}_{{ $loop->index }}">
                                                            {{ $option }}
                                                        </label>
                                                    </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @elseif($field['type'] === 'select')
                                            <select class="form-control @error('dynamic_fields.' . $field['name']) is-invalid @enderror"
                                                id="dynamic_{{ $field['name'] }}"
                                                name="dynamic_fields[{{ $field['name'] }}]">
                                                <option value="">Pilih {{ strtolower($field['label']) }}</option>
                                                @if(isset($field['options']))
                                                    @foreach($field['options'] as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @elseif($field['type'] === 'date')
                                            <input type="date"
                                                class="form-control @error('dynamic_fields.' . $field['name']) is-invalid @enderror"
                                                id="dynamic_{{ $field['name'] }}"
                                                name="dynamic_fields[{{ $field['name'] }}]"
                                                value="{{ old('dynamic_fields.' . $field['name'], $pendaftaran->custom_variables[$field['name']] ?? '') }}">
                                        @else
                                            <input type="text"
                                                class="form-control @error('dynamic_fields.' . $field['name']) is-invalid @enderror"
                                                id="dynamic_{{ $field['name'] }}"
                                                name="dynamic_fields[{{ $field['name'] }}]"
                                                value="{{ old('dynamic_fields.' . $field['name'], $pendaftaran->custom_variables[$field['name']] ?? '') }}"
                                                placeholder="Masukkan {{ strtolower($field['label']) }}">
                                        @endif

                                        @error('dynamic_fields.' . $field['name'])
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                <!-- Custom Variables (Legacy) -->
                                @if(count($customVariables) > 0)
                                <div class="mb-4">
                                    <h6 class="text-primary"><i class="fas fa-edit mr-2"></i>Data Tambahan</h6>
                                    <p class="small text-muted">Lengkapi data berikut untuk melengkapi dokumen APL 1</p>

                                    @foreach($customVariables as $variable)
                                    <div class="mb-3">
                                        <label for="custom_{{ $variable }}" class="form-label">
                                            {{ ucwords(str_replace(['_', '.'], ' ', $variable)) }}
                                        </label>
                                        <input type="text"
                                            class="form-control @error('custom_variables.' . $variable) is-invalid @enderror"
                                            id="custom_{{ $variable }}"
                                            name="custom_variables[{{ $variable }}]"
                                            value="{{ old('custom_variables.' . $variable, $pendaftaran->custom_variables[$variable] ?? '') }}"
                                            placeholder="Masukkan {{ strtolower(str_replace(['_', '.'], ' ', $variable)) }}">
                                        @error('custom_variables.' . $variable)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                {{-- Digital Signature Section --}}
                                <div class="card mb-4 border-left-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-signature mr-2"></i>
                                            Tanda Tangan Digital
                                            @if(!empty($pendaftaran->ttd_asesi_path))
                                                <span class="badge badge-success ml-2">Sudah Tersimpan</span>
                                            @else
                                                <span class="badge badge-warning ml-2">Belum Diisi</span>
                                            @endif
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if(!empty($pendaftaran->ttd_asesi_path))
                                            <!-- TTD sudah ada, tampilkan -->
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                <strong>Tanda tangan sudah tersimpan.</strong> Anda dapat menggantinya dengan tanda tangan baru jika diperlukan.
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label font-weight-bold">Tanda Tangan yang Tersimpan:</label>
                                                <div class="border p-3 bg-light rounded">
                                                    <img src="{{ asset('storage/' . $pendaftaran->ttd_asesi_path) }}" 
                                                         alt="Tanda Tangan Digital" 
                                                         class="img-fluid"
                                                         style="max-height: 150px; background: white; padding: 10px; border: 1px solid #dee2e6;">
                                                </div>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="checkbox" class="form-check-input" id="change-signature">
                                                <label class="form-check-label" for="change-signature">
                                                    Saya ingin mengganti tanda tangan
                                                </label>
                                            </div>

                                            <div id="signature-section" style="display: none;">
                                        @else
                                            <!-- TTD belum ada -->
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                <strong>Petunjuk:</strong> Silakan berikan tanda tangan digital Anda sebagai bukti keaslian data yang telah diisi.
                                            </div>
                                            <div id="signature-section">
                                        @endif

                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="signature-pad-container">
                                                    <canvas id="signature-pad" width="600" height="200" class="border"></canvas>
                                                    <div class="mt-2">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-signature">
                                                            <i class="fas fa-eraser"></i> Hapus Tanda Tangan
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    <strong>Penting:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        <li>Tanda tangan harus jelas dan terbaca</li>
                                                        <li>Gunakan mouse atau touch untuk menandatangani</li>
                                                        <li>Tanda tangan akan tersimpan secara digital</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" name="signature_data" id="signature-data">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <a href="{{ route('asesi.sertifikasi.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times mr-2"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i> Simpan Data
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-info text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-info-circle mr-2"></i> Informasi
                        </h6>
                    </div>
                    <div class="card-body">
                        <h6><i class="fas fa-graduation-cap mr-2"></i> Skema</h6>
                        <p class="mb-3">{{ $pendaftaran->skema->nama }}</p>

                        <h6><i class="fas fa-tag mr-2"></i> Kode Skema</h6>
                        <p class="mb-3">{{ $pendaftaran->skema->kode }}</p>

                        <h6><i class="fas fa-flag mr-2"></i> Status</h6>
                        <p class="mb-3">
                            <span class="badge badge-{{ $pendaftaran->status == 4 ? 'warning' : 'secondary' }}">
                                {{ $pendaftaran->status_text }}
                            </span>
                        </p>

                        <h6><i class="fas fa-calendar mr-2"></i> Tanggal Ujian</h6>
                        <p class="mb-3">{{ $pendaftaran->jadwal->tanggal_ujian }}</p>

                        <h6><i class="fas fa-map-marker-alt mr-2"></i> TUK</h6>
                        <p>{{ $pendaftaran->jadwal->tuk->nama }}</p>

                        @if($allCustomVariablesFilled)
                            <div class="alert alert-success mt-3">
                                <i class="fas fa-check-circle mr-2"></i>
                                <strong>Siap Generate!</strong>
                            </div>
                        @else
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Perlu Lengkapi Data</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-secondary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-question-circle mr-2"></i> Bantuan
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-2">
                            <strong>APL 1 (Formulir Permohonan Sertifikasi Kompetensi)</strong> adalah dokumen permohonan sertifikasi yang berisi data diri dan kompetensi yang akan diujikan.
                        </p>
                        <p class="small mb-2">
                            Lengkapi semua data yang diperlukan untuk dapat men-generate dokumen APL 1.
                        </p>
                        <p class="small mb-0">
                            Setelah data lengkap, klik tombol <strong>Generate & Download APL 1</strong> untuk mengunduh dokumen.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .signature-pad-container {
            position: relative;
        }

        #signature-pad {
            border: 2px solid #dee2e6;
            border-radius: 5px;
            background-color: white;
        }

        .border-left-primary {
            border-left: 4px solid #007bff !important;
        }
    </style>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        $(document).ready(function() {
            @if(!$allCustomVariablesFilled)
            // Initialize signature pad hanya jika form masih perlu diisi
            const canvas = document.getElementById('signature-pad');
            const hasTTD = {{ !empty($pendaftaran->ttd_asesi_path) ? 'true' : 'false' }};
            
            if (canvas) {
                const signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(255, 255, 255, 0)',
                    penColor: 'rgb(0, 0, 0)'
                });

                // Toggle signature section jika TTD sudah ada
                if (hasTTD) {
                    $('#change-signature').on('change', function() {
                        if ($(this).is(':checked')) {
                            $('#signature-section').slideDown();
                            signaturePad.clear();
                        } else {
                            $('#signature-section').slideUp();
                            signaturePad.clear();
                        }
                    });
                }

                // Clear signature
                $('#clear-signature').on('click', function() {
                    signaturePad.clear();
                });

                // Handle form submission
                $('form').on('submit', function(e) {
                    // Jika TTD sudah ada dan tidak ingin ganti, skip validasi
                    if (hasTTD && !$('#change-signature').is(':checked')) {
                        return true;
                    }

                    // Get signature data
                    if (!signaturePad.isEmpty()) {
                        $('#signature-data').val(signaturePad.toDataURL());
                    } else {
                        // Jika TTD belum ada, maka wajib diisi
                        if (!hasTTD) {
                            alert('Silakan berikan tanda tangan digital terlebih dahulu.');
                            e.preventDefault();
                            return false;
                        }
                    }
                });
            }
            @endif
        });
    </script>
    @endpush
@endsection

