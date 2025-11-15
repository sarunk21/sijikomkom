@extends('components.templates.master-layout')

@section('title', 'Isi Formulir')
@section('page-title', 'Isi Formulir - ' . $bankSoal->nama)

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-edit mr-2"></i>{{ $bankSoal->nama }}
            </h5>
        </div>
        <div class="card-body">
            @if ($bankSoal->keterangan)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>{{ $bankSoal->keterangan }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="font-weight-bold mb-2">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Terdapat kesalahan:
                    </h6>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            <form id="formulirForm" method="POST">
                @csrf
                <div class="row">
                    @foreach ($customFields as $index => $field)
                        <div class="col-md-{{ $field['width'] ?? 12 }} mb-3">
                            <label class="font-weight-bold">
                                {{ $field['label'] }}
                                @if ($field['required'] ?? false)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>

                            @if ($field['type'] === 'text')
                                <input type="text" name="responses[{{ $field['name'] }}]"
                                    class="form-control" value="{{ $response->asesi_responses[$field['name']] ?? '' }}"
                                    {{ $field['required'] ?? false ? 'required' : '' }}>

                            @elseif ($field['type'] === 'textarea')
                                <textarea name="responses[{{ $field['name'] }}]" class="form-control" rows="4"
                                    {{ $field['required'] ?? false ? 'required' : '' }}>{{ $response->asesi_responses[$field['name']] ?? '' }}</textarea>

                            @elseif ($field['type'] === 'number')
                                <input type="number" name="responses[{{ $field['name'] }}]"
                                    class="form-control" value="{{ $response->asesi_responses[$field['name']] ?? '' }}"
                                    {{ $field['required'] ?? false ? 'required' : '' }}>

                            @elseif ($field['type'] === 'email')
                                <input type="email" name="responses[{{ $field['name'] }}]"
                                    class="form-control" value="{{ $response->asesi_responses[$field['name']] ?? '' }}"
                                    {{ $field['required'] ?? false ? 'required' : '' }}>

                            @elseif ($field['type'] === 'date')
                                <input type="date" name="responses[{{ $field['name'] }}]"
                                    class="form-control" value="{{ $response->asesi_responses[$field['name']] ?? '' }}"
                                    {{ $field['required'] ?? false ? 'required' : '' }}>

                            @elseif ($field['type'] === 'checkbox')
                                @foreach ($field['options'] ?? [] as $option)
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                            id="{{ $field['name'] }}_{{ $loop->index }}"
                                            name="responses[{{ $field['name'] }}][]" value="{{ $option }}"
                                            {{ in_array($option, $response->asesi_responses[$field['name']] ?? []) ? 'checked' : '' }}>
                                        <label class="custom-control-label"
                                            for="{{ $field['name'] }}_{{ $loop->index }}">
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach

                            @elseif ($field['type'] === 'radio')
                                @foreach ($field['options'] ?? [] as $option)
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input"
                                            id="{{ $field['name'] }}_{{ $loop->index }}"
                                            name="responses[{{ $field['name'] }}]" value="{{ $option }}"
                                            {{ ($response->asesi_responses[$field['name']] ?? '') === $option ? 'checked' : '' }}
                                            {{ $field['required'] ?? false ? 'required' : '' }}>
                                        <label class="custom-control-label"
                                            for="{{ $field['name'] }}_{{ $loop->index }}">
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach

                            @elseif ($field['type'] === 'select')
                                <select name="responses[{{ $field['name'] }}]" class="form-control"
                                    {{ $field['required'] ?? false ? 'required' : '' }}>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($field['options'] ?? [] as $option)
                                        <option value="{{ $option }}"
                                            {{ ($response->asesi_responses[$field['name']] ?? '') === $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>

                            @elseif ($field['type'] === 'file')
                                <input type="file" name="responses[{{ $field['name'] }}]"
                                    class="form-control-file"
                                    {{ $field['required'] ?? false ? 'required' : '' }}>
                                @if (isset($response->asesi_responses[$field['name']]) && $response->asesi_responses[$field['name']])
                                    <small class="text-muted">
                                        File saat ini: {{ basename($response->asesi_responses[$field['name']]) }}
                                    </small>
                                @endif

                            @elseif ($field['type'] === 'signature_pad')
                                <div class="border rounded p-2 bg-white">
                                    <canvas id="signaturePad_{{ $field['name'] }}" width="700" height="200"
                                        style="width: 100%; max-width: 700px; border: 1px solid #ddd;"></canvas>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-secondary"
                                            onclick="clearSignature('{{ $field['name'] }}')">
                                            <i class="fas fa-eraser mr-1"></i>Hapus Tanda Tangan
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="responses[{{ $field['name'] }}]"
                                    id="signature_{{ $field['name'] }}"
                                    value="{{ $response->asesi_responses[$field['name']] ?? '' }}">
                            @endif

                            @if (isset($field['description']) && $field['description'])
                                <small class="form-text text-muted">{{ $field['description'] }}</small>
                            @endif
                        </div>
                    @endforeach
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('asesi.formulir.index', $jadwal->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali
                    </a>
                    <div>
                        <button type="button" class="btn btn-warning mr-2" onclick="saveDraft()">
                            <i class="fas fa-save mr-1"></i>Simpan Draft
                        </button>
                        <button type="button" class="btn btn-success" onclick="submitForm()">
                            <i class="fas fa-paper-plane mr-1"></i>Submit Final
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        // Initialize signature pads
        const signaturePads = {};

        @foreach ($customFields as $field)
            @if ($field['type'] === 'signature_pad')
                (function() {
                    const canvas = document.getElementById('signaturePad_{{ $field['name'] }}');
                    const signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)'
                    });
                    signaturePads['{{ $field['name'] }}'] = signaturePad;

                    // Load existing signature
                    const existingSignature = document.getElementById('signature_{{ $field['name'] }}').value;
                    if (existingSignature) {
                        signaturePad.fromDataURL(existingSignature);
                    }

                    // Update hidden input on change
                    signaturePad.addEventListener('endStroke', function() {
                        document.getElementById('signature_{{ $field['name'] }}').value = signaturePad.toDataURL();
                    });
                })();
            @endif
        @endforeach

        function clearSignature(fieldName) {
            if (signaturePads[fieldName]) {
                signaturePads[fieldName].clear();
                document.getElementById('signature_' + fieldName).value = '';
            }
        }

        function saveDraft() {
            const form = document.getElementById('formulirForm');
            form.action = '{{ route('asesi.formulir.save-draft', [$jadwal->id, $bankSoal->id]) }}';
            form.submit();
        }

        function submitForm() {
            if (confirm('Apakah Anda yakin ingin submit formulir? Setelah disubmit, formulir tidak dapat diubah lagi.')) {
                // Save all signature pads
                for (let fieldName in signaturePads) {
                    document.getElementById('signature_' + fieldName).value = signaturePads[fieldName].toDataURL();
                }

                const form = document.getElementById('formulirForm');
                form.action = '{{ route('asesi.formulir.submit', [$jadwal->id, $bankSoal->id]) }}';
                form.submit();
            }
        }

        // Auto-save draft every 2 minutes
        setInterval(function() {
            // Save all signature pads
            for (let fieldName in signaturePads) {
                document.getElementById('signature_' + fieldName).value = signaturePads[fieldName].toDataURL();
            }

            const formData = new FormData(document.getElementById('formulirForm'));
            fetch('{{ route('asesi.formulir.save-draft', [$jadwal->id, $bankSoal->id]) }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        console.log('Auto-save successful');
                    }
                });
        }, 120000); // 2 minutes
    </script>
    @endpush
@endsection
