@extends('components.templates.master-layout')

@section('title', 'Form APL2')
@section('page-title', 'Form APL2 (Portofolio)')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="card-title mb-0">
                <i class="fas fa-file-alt me-2"></i>
                Form APL2 (Portofolio)
            </h5>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Petunjuk:</strong> Silakan isi form APL2 dengan lengkap dan jelas. Data ini akan digunakan untuk proses sertifikasi Anda.
            </div>

            <form action="{{ route('asesi.sertifikasi.store-apl2', $pendaftaran->id) }}" method="post">
                @csrf

                {{-- Section APL2 Form --}}
                @if($template)
                    <div class="card mb-4 border-left-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-alt me-2"></i>
                                Form APL2 (Portofolio)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Petunjuk:</strong> Silakan isi form APL2 terlebih dahulu sebelum mengerjakan soal ujian.
                            </div>

                            {{-- Custom Variables dari Template APL2 --}}
                            @if($template->custom_variables && count($template->custom_variables) > 0)
                                @foreach($template->custom_variables as $index => $variable)
                                    <div class="form-group mb-4">
                                        <label for="custom_variable_{{ $variable['name'] }}" class="form-label fw-bold">
                                            <i class="fas fa-question-circle me-1"></i>
                                            {{ $variable['label'] }}
                                            @if($variable['required'] ?? false)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        @if($variable['type'] === 'textarea')
                                            <textarea name="custom_variables[{{ $variable['name'] }}]"
                                                      id="custom_variable_{{ $variable['name'] }}"
                                                      class="form-control @error('custom_variables.' . $variable['name']) is-invalid @enderror"
                                                      rows="3"
                                                      placeholder="Masukkan {{ $variable['label'] }}..."
                                                      {{ ($variable['required'] ?? false) ? 'required' : '' }}>{{ old('custom_variables.' . $variable['name']) ?? (($pendaftaran->custom_variables ?? [])[$variable['name']] ?? '') }}</textarea>
                                        @elseif($variable['type'] === 'select' && !empty($variable['options']))
                                            <select name="custom_variables[{{ $variable['name'] }}]"
                                                    id="custom_variable_{{ $variable['name'] }}"
                                                    class="form-control @error('custom_variables.' . $variable['name']) is-invalid @enderror"
                                                    {{ ($variable['required'] ?? false) ? 'required' : '' }}>
                                                <option value="">Pilih {{ $variable['label'] }}...</option>
                                                @foreach(explode(',', trim($variable['options'])) as $option)
                                                    @php $option = trim($option); @endphp
                                                    @if(!empty($option))
                                                        <option value="{{ $option }}"
                                                                {{ old('custom_variables.' . $variable['name']) == $option || (($pendaftaran->custom_variables ?? [])[$variable['name']] ?? '') == $option ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        @elseif($variable['type'] === 'checkbox')
                                            <div class="form-check">
                                                <input type="checkbox"
                                                       name="custom_variables[{{ $variable['name'] }}]"
                                                       id="custom_variable_{{ $variable['name'] }}"
                                                       class="form-check-input @error('custom_variables.' . $variable['name']) is-invalid @enderror"
                                                       value="1"
                                                       {{ old('custom_variables.' . $variable['name']) == '1' || (($pendaftaran->custom_variables ?? [])[$variable['name']] ?? '') == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="custom_variable_{{ $variable['name'] }}">
                                                    {{ $variable['label'] }}
                                                </label>
                                            </div>
                                        @elseif($variable['type'] === 'radio' && !empty($variable['options']))
                                            <div class="form-group">
                                                @foreach(explode(',', trim($variable['options'])) as $option)
                                                    @php $option = trim($option); @endphp
                                                    @if(!empty($option))
                                                        <div class="form-check">
                                                            <input type="radio"
                                                                   name="custom_variables[{{ $variable['name'] }}]"
                                                                   id="custom_variable_{{ $variable['name'] }}_{{ $loop->index }}"
                                                                   class="form-check-input @error('custom_variables.' . $variable['name']) is-invalid @enderror"
                                                                   value="{{ $option }}"
                                                                   {{ old('custom_variables.' . $variable['name']) == $option || (($pendaftaran->custom_variables ?? [])[$variable['name']] ?? '') == $option ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="custom_variable_{{ $variable['name'] }}_{{ $loop->index }}">
                                                                {{ $option }}
                                                            </label>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @elseif($variable['type'] === 'file')
                                            <input type="file"
                                                   name="custom_variables[{{ $variable['name'] }}]"
                                                   id="custom_variable_{{ $variable['name'] }}"
                                                   class="form-control @error('custom_variables.' . $variable['name']) is-invalid @enderror"
                                                   {{ ($variable['required'] ?? false) ? 'required' : '' }}>
                                            <small class="form-text text-muted">Upload bukti untuk {{ $variable['label'] }}</small>
                                        @else
                                            <input type="{{ $variable['type'] }}"
                                                   name="custom_variables[{{ $variable['name'] }}]"
                                                   id="custom_variable_{{ $variable['name'] }}"
                                                   class="form-control @error('custom_variables.' . $variable['name']) is-invalid @enderror"
                                                   placeholder="Masukkan {{ $variable['label'] }}..."
                                                   value="{{ old('custom_variables.' . $variable['name']) ?? (($pendaftaran->custom_variables ?? [])[$variable['name']] ?? '') }}"
                                                   {{ ($variable['required'] ?? false) ? 'required' : '' }}>
                                        @endif

                                        @error('custom_variables.' . $variable['name'])
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Tidak ada pertanyaan yang dikonfigurasi untuk template APL2 ini.
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Template APL2 tidak ditemukan untuk skema ini.
                    </div>
                @endif

                {{-- Digital Signature Section --}}
                <div class="card mb-4 border-left-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-signature me-2"></i>
                            Tanda Tangan Digital
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Petunjuk:</strong> Silakan berikan tanda tangan digital Anda sebagai bukti keaslian data yang telah diisi.
                        </div>

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

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        <i class="fas fa-list me-1"></i>
                        Total pertanyaan: <strong>{{ $template ? ($template->custom_variables ? count($template->custom_variables) : 0) : 0 }}</strong>
                    </div>
                    <div>
                        <a href="{{ route('asesi.sertifikasi.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Simpan Data APL2
                        </button>
                    </div>
                </div>
            </form>
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
            // Initialize signature pad
            const canvas = document.getElementById('signature-pad');
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgb(0, 0, 0)'
            });

            // Clear signature
            $('#clear-signature').on('click', function() {
                signaturePad.clear();
            });

            // Handle form submission
            $('form').on('submit', function(e) {
                // Get signature data
                if (!signaturePad.isEmpty()) {
                    $('#signature-data').val(signaturePad.toDataURL());
                } else {
                    alert('Silakan berikan tanda tangan digital terlebih dahulu.');
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
    @endpush
@endsection
