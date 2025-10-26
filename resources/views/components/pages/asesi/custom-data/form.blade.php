@extends('components.templates.master-layout')

@section('title', 'Lengkapi Data Sertifikasi')
@section('page-title', 'Lengkapi Data Sertifikasi')

@section('content')

    <a href="{{ route('asesi.sertifikasi.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali ke Sertifikasi</span>
    </a>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Lengkapi Data Sertifikasi
                        </h6>
                        <small class="text-muted">
                            Skema: <strong>{{ $pendaftaran->skema->nama }}</strong>
                        </small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('asesi.custom-data.store', $pendaftaran->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Data yang Sudah Ada -->
                            @if(count($existingData) > 0)
                            <div class="mb-4">
                                <h6 class="text-success">Data dari Profil Anda</h6>
                                <p class="small text-muted">Data berikut sudah tersedia dari profil Anda</p>

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
                                <h6 class="text-primary">Field Dinamis</h6>
                                <p class="small text-muted">Field yang dikonfigurasi khusus untuk template ini</p>

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
                                                        id="dynamic_{{ $field['name'] }}_{{ $loop->index }}"
                                                        @if(in_array($option, old('dynamic_fields.' . $field['name'], $pendaftaran->custom_variables[$field['name']] ?? []))) checked @endif>
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
                                                        id="dynamic_{{ $field['name'] }}_{{ $loop->index }}"
                                                        @if(old('dynamic_fields.' . $field['name'], $pendaftaran->custom_variables[$field['name']] ?? '') == $option) checked @endif>
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
                                            <option value="">Pilih {{ $field['label'] }}</option>
                                            @if(isset($field['options']))
                                                @foreach($field['options'] as $option)
                                                <option value="{{ $option }}"
                                                    @if(old('dynamic_fields.' . $field['name'], $pendaftaran->custom_variables[$field['name']] ?? '') == $option) selected @endif>
                                                    {{ $option }}
                                                </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    @else
                                        <input type="{{ $field['type'] }}"
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

                            <!-- Data Tambahan (Legacy Custom Variables) -->
                            @if(count($customVariables) > 0)
                            <div class="mb-4">
                                <h6 class="text-primary">Data Tambahan</h6>
                                <p class="small text-muted">Lengkapi data berikut untuk melengkapi dokumen sertifikasi Anda</p>

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
                                    <small class="form-text text-muted">
                                        Isi data sesuai dengan informasi pribadi Anda
                                    </small>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <!-- TTD Digital Asesi -->
                            <div class="mb-4">
                                <h6 class="text-success">Tanda Tangan Digital</h6>
                                <p class="small text-muted">Buat tanda tangan digital Anda menggunakan mouse atau touchpad</p>

                                @if($pendaftaran->ttd_asesi_path)
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-image"></i>
                                    TTD saat ini:
                                    <a href="{{ asset('storage/' . $pendaftaran->ttd_asesi_path) }}" target="_blank">
                                        Lihat TTD
                                    </a>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label">Tanda Tangan Digital</label>
                                    <div class="signature-pad-container border rounded p-3" style="background-color: #f8f9fa;">
                                        <canvas id="signaturePad" width="600" height="200" style="border: 1px solid #ddd; background-color: white;"></canvas>
                                        <div class="mt-2 d-flex justify-content-between">
                                            <button type="button" id="clearSignature" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-eraser"></i> Hapus
                                            </button>
                                            <small class="text-muted align-self-center">
                                                Gunakan mouse atau touchpad untuk menandatangani
                                            </small>
                                        </div>
                                    </div>
                                    <input type="hidden" id="signatureData" name="signature_data">
                                    @error('signature_data')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Info Template -->
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Informasi</h6>
                                <ul class="mb-0">
                                    <li>Data yang Anda isi akan digunakan untuk melengkapi dokumen APL 1</li>
                                    <li>Tanda tangan digital akan ditambahkan ke dokumen</li>
                                    <li>Anda bisa mengubah data ini kapan saja sebelum generate dokumen</li>
                                    <li>Pastikan data yang diisi sesuai dengan informasi pribadi Anda</li>
                                </ul>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('asesi.sertifikasi.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Status Data</h6>
                    </div>
                    <div class="card-body">
                        <h6>Skema: {{ $pendaftaran->skema->nama }}</h6>
                        <p class="small text-muted">Status: {{ $pendaftaran->status_text }}</p>

                        @if(count($existingData) > 0)
                        <h6 class="mt-3">Data dari Profil:</h6>
                        <ul class="list-unstyled">
                            @foreach($existingData as $variable => $value)
                            <li class="small mb-2">
                                <i class="fas fa-check-circle text-success"></i>
                                {{ ucwords(str_replace(['_', '.'], ' ', $variable)) }}
                                <span class="badge badge-success ml-2">Sudah tersedia</span>
                            </li>
                            @endforeach
                        </ul>
                        @endif

                        @if(count($customVariables) > 0)
                        <h6 class="mt-3">Data yang Perlu Diisi:</h6>
                        <ul class="list-unstyled">
                            @foreach($customVariables as $variable)
                            <li class="small mb-2">
                                <i class="fas fa-{{ $pendaftaran->custom_variables && isset($pendaftaran->custom_variables[$variable]) ? 'check-circle text-success' : 'circle text-muted' }}"></i>
                                {{ ucwords(str_replace(['_', '.'], ' ', $variable)) }}
                                @if($pendaftaran->custom_variables && isset($pendaftaran->custom_variables[$variable]))
                                    <span class="badge badge-success ml-2">Sudah diisi</span>
                                @else
                                    <span class="badge badge-warning ml-2">Belum diisi</span>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                        @endif

                        @if(count($existingData) == 0 && count($customVariables) == 0)
                        <div class="alert alert-info">
                            <small>Semua data sudah tersedia dari profil Anda. Anda hanya perlu menambahkan tanda tangan digital.</small>
                        </div>
                        @endif

                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Data ini akan digunakan untuk melengkapi dokumen sertifikasi Anda.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<!-- Signature Pad Library -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('signaturePad');
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: 'rgb(0, 0, 0)',
        minWidth: 1,
        maxWidth: 3,
        throttle: 16,
        minDistance: 5
    });

    // Clear signature button
    document.getElementById('clearSignature').addEventListener('click', function() {
        signaturePad.clear();
        document.getElementById('signatureData').value = '';
    });

    // Resize canvas to maintain aspect ratio
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        signaturePad.clear();
    }

    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    // Update hidden input when signature changes
    signaturePad.addEventListener('endStroke', function() {
        if (!signaturePad.isEmpty()) {
            document.getElementById('signatureData').value = signaturePad.toDataURL();
        }
    });

    // Form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!signaturePad.isEmpty()) {
            document.getElementById('signatureData').value = signaturePad.toDataURL();
        }
    });
});
</script>

<style>
.signature-pad-container {
    max-width: 100%;
    overflow: hidden;
}

#signaturePad {
    max-width: 100%;
    height: auto;
    cursor: crosshair;
}

@media (max-width: 768px) {
    #signaturePad {
        width: 100%;
        height: 150px;
    }
}
</style>
@endpush
