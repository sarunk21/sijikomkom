@extends('components.templates.master-layout')

@section('title', 'Review Formulir - ' . $bankSoal->nama)
@section('page-title', 'Review Formulir - ' . $bankSoal->nama)

@section('content')
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-check mr-2"></i>Review Formulir: {{ $bankSoal->nama }}
                </h5>
            </div>
            <div class="card-body">
                <!-- Warning if finalized -->
                @if ($isFinalized ?? false)
                    <div class="alert alert-warning">
                        <i class="fas fa-lock mr-2"></i>
                        <strong>Mode Read-Only:</strong> Penilaian sudah finalisasi (sudah dinilai BK/K). Anda hanya dapat melihat review, tidak dapat mengubahnya.
                    </div>
                @else
                    <!-- Info -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Petunjuk:</strong> Periksa jawaban asesi, isi field asesor jika ada, dan beri validasi (Sesuai/Tidak Sesuai). Default validasi adalah "Sesuai".
                    </div>
                @endif

                <form method="POST" action="{{ route('asesor.pemeriksaan.save-review', [$jadwal->id, $asesi->id, $bankSoal->id]) }}" enctype="multipart/form-data">
                    @csrf
                    
                    <fieldset {{ ($isFinalized ?? false) ? 'disabled' : '' }}>

                    <!-- Jawaban Asesi -->
                    @if ($asesiFields->isNotEmpty())
                        <h5 class="mb-3 border-bottom pb-2">
                            <i class="fas fa-user-edit mr-2"></i>Jawaban Asesi
                        </h5>

                        <div class="row">
                            @foreach ($asesiFields as $field)
                                <div class="col-md-{{ $field['width'] ?? 12 }} mb-4">
                                    <div class="card border-left-primary shadow-sm">
                                        <div class="card-body">
                                            <label class="font-weight-bold">{{ $field['label'] }}</label>

                                            <!-- Display Asesi Answer -->
                                            @if ($field['type'] === 'signature_pad')
                                                <div class="border rounded p-2 bg-light mb-2">
                                                    @if (isset($response->asesi_responses[$field['name']]) && $response->asesi_responses[$field['name']])
                                                        <img src="{{ $response->asesi_responses[$field['name']] }}"
                                                            alt="Signature" style="max-width: 100%; border: 1px solid #ddd;">
                                                    @else
                                                        <p class="text-muted mb-0">Belum ada tanda tangan</p>
                                                    @endif
                                                </div>
                                            @elseif ($field['type'] === 'file')
                                                <div class="border rounded p-3 bg-light mb-2">
                                                    @if (isset($response->asesi_responses[$field['name']]) && $response->asesi_responses[$field['name']])
                                                        <i class="fas fa-file mr-2"></i>
                                                        <a href="{{ Storage::url($response->asesi_responses[$field['name']]) }}"
                                                            target="_blank">
                                                            {{ basename($response->asesi_responses[$field['name']]) }}
                                                        </a>
                                                    @else
                                                        <p class="text-muted mb-0">Belum ada file</p>
                                                    @endif
                                                </div>
                                            @elseif ($field['type'] === 'checkbox')
                                                <div class="border rounded p-3 bg-light mb-2">
                                                    @if (isset($response->asesi_responses[$field['name']]) && is_array($response->asesi_responses[$field['name']]))
                                                        <ul class="mb-0">
                                                            @foreach ($response->asesi_responses[$field['name']] as $value)
                                                                <li>{{ $value }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p class="text-muted mb-0">Belum ada pilihan</p>
                                                    @endif
                                                </div>
                                            @elseif ($field['type'] === 'textarea')
                                                <div class="border rounded p-3 bg-light mb-2" style="white-space: pre-wrap;">{{ $response->asesi_responses[$field['name']] ?? '-' }}</div>
                                            @else
                                                <div class="border rounded p-3 bg-light mb-2">
                                                    {{ $response->asesi_responses[$field['name']] ?? '-' }}
                                                </div>
                                            @endif

                                            <!-- Validasi -->
                                            @if (in_array($field['role'] ?? 'asesi', ['asesi', 'both']) && $field['type'] !== 'signature_pad')
                                                <div class="mt-2">
                                                    <label class="font-weight-bold text-primary">Validasi:</label>
                                                    <div class="btn-group btn-group-toggle d-block" data-toggle="buttons">
                                                        <label class="btn btn-outline-success {{ !isset($response->asesor_validations[$field['name']]['is_valid']) || $response->asesor_validations[$field['name']]['is_valid'] ? 'active' : '' }}">
                                                            <input type="radio" name="asesor_validations[{{ $field['name'] }}][is_valid]"
                                                                value="1" {{ !isset($response->asesor_validations[$field['name']]['is_valid']) || $response->asesor_validations[$field['name']]['is_valid'] ? 'checked' : '' }}>
                                                            <i class="fas fa-check mr-1"></i>Sesuai
                                                        </label>
                                                        <label class="btn btn-outline-danger {{ isset($response->asesor_validations[$field['name']]['is_valid']) && !$response->asesor_validations[$field['name']]['is_valid'] ? 'active' : '' }}">
                                                            <input type="radio" name="asesor_validations[{{ $field['name'] }}][is_valid]"
                                                                value="0" {{ isset($response->asesor_validations[$field['name']]['is_valid']) && !$response->asesor_validations[$field['name']]['is_valid'] ? 'checked' : '' }}>
                                                            <i class="fas fa-times mr-1"></i>Tidak Sesuai
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif

                                            @if (isset($field['description']) && $field['description'])
                                                <small class="form-text text-muted">{{ $field['description'] }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Field Asesor -->
                    @if ($asesorFields->isNotEmpty())
                        <h5 class="mb-3 mt-4 border-bottom pb-2">
                            <i class="fas fa-user-tie mr-2"></i>Field untuk Asesor
                        </h5>

                        <div class="row">
                            @foreach ($asesorFields as $field)
                                <div class="col-md-{{ $field['width'] ?? 12 }} mb-4">
                                    <div class="card border-left-info shadow-sm">
                                        <div class="card-body">
                                            <label class="font-weight-bold">
                                                {{ $field['label'] }}
                                                @if ($field['required'] ?? false)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>

                                            @if ($field['type'] === 'text')
                                                <input type="text" name="asesor_responses[{{ $field['name'] }}]"
                                                    class="form-control" value="{{ $response->asesor_responses[$field['name']] ?? '' }}"
                                                    {{ $field['required'] ?? false ? 'required' : '' }}>

                                            @elseif ($field['type'] === 'textarea')
                                                <textarea name="asesor_responses[{{ $field['name'] }}]" class="form-control"
                                                    rows="4" {{ $field['required'] ?? false ? 'required' : '' }}>{{ $response->asesor_responses[$field['name']] ?? '' }}</textarea>

                                            @elseif ($field['type'] === 'number')
                                                <input type="number" name="asesor_responses[{{ $field['name'] }}]"
                                                    class="form-control" value="{{ $response->asesor_responses[$field['name']] ?? '' }}"
                                                    {{ $field['required'] ?? false ? 'required' : '' }}>

                                            @elseif ($field['type'] === 'email')
                                                <input type="email" name="asesor_responses[{{ $field['name'] }}]"
                                                    class="form-control" value="{{ $response->asesor_responses[$field['name']] ?? '' }}"
                                                    {{ $field['required'] ?? false ? 'required' : '' }}>

                                            @elseif ($field['type'] === 'date')
                                                <input type="date" name="asesor_responses[{{ $field['name'] }}]"
                                                    class="form-control" value="{{ $response->asesor_responses[$field['name']] ?? '' }}"
                                                    {{ $field['required'] ?? false ? 'required' : '' }}>

                                            @elseif ($field['type'] === 'checkbox')
                                                @foreach ($field['options'] ?? [] as $option)
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="asesor_{{ $field['name'] }}_{{ $loop->index }}"
                                                            name="asesor_responses[{{ $field['name'] }}][]" value="{{ $option }}"
                                                            {{ in_array($option, $response->asesor_responses[$field['name']] ?? []) ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="asesor_{{ $field['name'] }}_{{ $loop->index }}">
                                                            {{ $option }}
                                                        </label>
                                                    </div>
                                                @endforeach

                                            @elseif ($field['type'] === 'radio')
                                                @foreach ($field['options'] ?? [] as $option)
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input"
                                                            id="asesor_{{ $field['name'] }}_{{ $loop->index }}"
                                                            name="asesor_responses[{{ $field['name'] }}]" value="{{ $option }}"
                                                            {{ ($response->asesor_responses[$field['name']] ?? '') === $option ? 'checked' : '' }}
                                                            {{ $field['required'] ?? false ? 'required' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="asesor_{{ $field['name'] }}_{{ $loop->index }}">
                                                            {{ $option }}
                                                        </label>
                                                    </div>
                                                @endforeach

                                            @elseif ($field['type'] === 'select')
                                                <select name="asesor_responses[{{ $field['name'] }}]" class="form-control"
                                                    {{ $field['required'] ?? false ? 'required' : '' }}>
                                                    <option value="">-- Pilih --</option>
                                                    @foreach ($field['options'] ?? [] as $option)
                                                        <option value="{{ $option }}"
                                                            {{ ($response->asesor_responses[$field['name']] ?? '') === $option ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                            @elseif ($field['type'] === 'signature_pad')
                                                <div class="border rounded p-2 bg-white">
                                                    <canvas id="asesorSignaturePad_{{ $field['name'] }}" width="700"
                                                        height="200"
                                                        style="width: 100%; max-width: 700px; border: 1px solid #ddd;"></canvas>
                                                    <div class="mt-2">
                                                        <button type="button" class="btn btn-sm btn-secondary"
                                                            onclick="clearAsesorSignature('{{ $field['name'] }}')">
                                                            <i class="fas fa-eraser mr-1"></i>Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="asesor_responses[{{ $field['name'] }}]"
                                                    id="asesor_signature_{{ $field['name'] }}"
                                                    value="{{ $response->asesor_responses[$field['name']] ?? '' }}">
                                            @endif

                                            @if (isset($field['description']) && $field['description'])
                                                <small class="form-text text-muted">{{ $field['description'] }}</small>
                                            @endif

                                            <!-- Validasi untuk asesor fields -->
                                            @if ($bankSoal->target === 'asesor')
                                                <div class="mt-3 pt-3 border-top">
                                                    <label class="font-weight-bold text-primary">Pencapaian:</label>
                                                    <div class="btn-group btn-group-toggle d-block" data-toggle="buttons">
                                                        <label class="btn btn-outline-success {{ !isset($response->asesor_validations[$field['name']]['is_valid']) || $response->asesor_validations[$field['name']]['is_valid'] ? 'active' : '' }}">
                                                            <input type="radio" name="asesor_validations[{{ $field['name'] }}][is_valid]"
                                                                value="1" {{ !isset($response->asesor_validations[$field['name']]['is_valid']) || $response->asesor_validations[$field['name']]['is_valid'] ? 'checked' : '' }}>
                                                            <i class="fas fa-check mr-1"></i>Ya
                                                        </label>
                                                        <label class="btn btn-outline-danger {{ isset($response->asesor_validations[$field['name']]['is_valid']) && !$response->asesor_validations[$field['name']]['is_valid'] ? 'active' : '' }}">
                                                            <input type="radio" name="asesor_validations[{{ $field['name'] }}][is_valid]"
                                                                value="0" {{ isset($response->asesor_validations[$field['name']]['is_valid']) && !$response->asesor_validations[$field['name']]['is_valid'] ? 'checked' : '' }}>
                                                            <i class="fas fa-times mr-1"></i>Tidak
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Catatan Asesor -->
                    <h5 class="mb-3 mt-4 border-bottom pb-2">
                        <i class="fas fa-comment mr-2"></i>Catatan Asesor
                    </h5>
                    <div class="form-group">
                        <textarea name="catatan_asesor" class="form-control" rows="4"
                            placeholder="Tambahkan catatan jika diperlukan...">{{ $response->catatan_asesor ?? '' }}</textarea>
                    </div>

                    <!-- Status Pemeriksaan -->
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_asesor_completed"
                                name="is_asesor_completed" value="1"
                                {{ $response->is_asesor_completed ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold text-success"
                                for="is_asesor_completed">
                                <i class="fas fa-check-circle mr-1"></i>Tandai sebagai sudah selesai diperiksa
                            </label>
                        </div>
                        <small class="text-muted">Centang jika pemeriksaan formulir ini sudah selesai</small>
                    </div>

                    </fieldset>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('asesor.pemeriksaan.formulir-list', [$jadwal->id, $asesi->id]) }}"
                            class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Kembali
                        </a>
                        @if (!($isFinalized ?? false))
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save mr-1"></i>Simpan Review
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        // Initialize signature pads for asesor
        const asesorSignaturePads = {};

        @foreach ($asesorFields as $field)
            @if ($field['type'] === 'signature_pad')
                (function() {
                    const canvas = document.getElementById('asesorSignaturePad_{{ $field['name'] }}');
                    const signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)'
                    });
                    asesorSignaturePads['{{ $field['name'] }}'] = signaturePad;

                    const existingSignature = document.getElementById('asesor_signature_{{ $field['name'] }}')
                        .value;
                    if (existingSignature) {
                        signaturePad.fromDataURL(existingSignature);
                    }

                    @if ($isFinalized ?? false)
                        // Disable signature pad if finalized
                        signaturePad.off();
                    @else
                        signaturePad.addEventListener('endStroke', function() {
                            document.getElementById('asesor_signature_{{ $field['name'] }}').value = signaturePad
                                .toDataURL();
                        });
                    @endif
                })();
            @endif
        @endforeach

        function clearAsesorSignature(fieldName) {
            if (asesorSignaturePads[fieldName]) {
                asesorSignaturePads[fieldName].clear();
                document.getElementById('asesor_signature_' + fieldName).value = '';
            }
        }
    </script>
@endsection
