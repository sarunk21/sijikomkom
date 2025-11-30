@extends('components.templates.master-layout')

@section('title', 'Bank Soal - Edit')
@section('page-title', 'Edit Bank Soal')

@section('content')

    <a href="{{ route('admin.bank-soal.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali</span>
    </a>

    <div class="container-fluid">

        <!-- Info Alert -->
        <div class="alert alert-info d-flex align-items-start mb-4" style="background-color: #e7f3ff; border-color: #b3d9ff;">
            <i class="fas fa-info-circle me-2 mt-1" style="color: #0066cc; font-size: 1.2rem;"></i>
            <div class="flex-grow-1">
                <strong>Petunjuk:</strong> Gunakan format <code style="background-color: #fff; padding: 2px 6px; border-radius: 3px; color: #d63384;">${variable}</code> di file DOCX untuk variable yang dipilih.
                <div class="mt-3">
                    <p class="mb-2 small"><strong>Contoh penggunaan variable:</strong></p>
                    <ul class="small mb-2" style="line-height: 1.8;">
                        <li><code>${user.name}</code> → Nama user/asesi</li>
                        <li><code>${skema.nama}</code> → Nama skema sertifikasi</li>
                        <li><code>${jadwal.tanggal_ujian}</code> → Tanggal ujian</li>
                    </ul>
                    <p class="mb-2 small"><strong>Informasi Tipe:</strong></p>
                    <ul class="small mb-2" style="line-height: 1.8;">
                        <li><strong>FR IA 03</strong> - Formulir Asesmen Mandiri (untuk asesi)</li>
                        <li><strong>FR IA 06</strong> - Formulir Asesmen Praktik (untuk asesi)</li>
                        <li><strong>FR IA 07</strong> - Ceklis Observasi Asesor (untuk asesor)</li>
                    </ul>
                    <p class="mb-0 small">Upload file dalam format <strong>PDF, DOC, atau DOCX</strong> maksimal 10MB</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Form Edit Bank Soal</h6>
                    </div>
                    <div class="card-body">
                        <!-- Alert Messages -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <strong>Terjadi kesalahan:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form action="{{ route('admin.bank-soal.update', $bankSoal->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Skema -->
                            <div class="mb-3">
                                <label for="skema_id" class="form-label">Skema Sertifikasi <span class="text-danger">*</span></label>
                                <select class="form-control @error('skema_id') is-invalid @enderror"
                                    id="skema_id" name="skema_id" required>
                                    <option value="">Pilih Skema</option>
                                    @foreach($skemas as $skema)
                                        <option value="{{ $skema->id }}" {{ (old('skema_id', $bankSoal->skema_id) == $skema->id) ? 'selected' : '' }}>
                                            {{ $skema->nama }} ({{ $skema->kode }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('skema_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Pilih skema yang sesuai dengan bank soal ini</small>
                            </div>

                            <!-- Nama Bank Soal -->
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Bank Soal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                    id="nama" name="nama" value="{{ old('nama', $bankSoal->nama) }}"
                                    placeholder="Contoh: Soal Praktik Instalasi Jaringan" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tipe Formulir -->
                            <div class="mb-3">
                                <label for="tipe" class="form-label">Tipe Formulir <span class="text-danger">*</span></label>
                                <select class="form-control @error('tipe') is-invalid @enderror"
                                    id="tipe" name="tipe" required>
                                    <option value="">Pilih Tipe Formulir</option>
                                    @foreach($tipeOptions as $key => $label)
                                        <option value="{{ $key }}" {{ (old('tipe', $bankSoal->tipe) == $key) ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Target Pengguna -->
                            <div class="mb-3">
                                <label for="target" class="form-label">Target Pengguna <span class="text-danger">*</span></label>
                                <select class="form-control @error('target') is-invalid @enderror"
                                    id="target" name="target" required>
                                    <option value="">Pilih Target</option>
                                    @foreach($targetOptions as $key => $label)
                                        <option value="{{ $key }}" {{ (old('target', $bankSoal->target) == $key) ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('target')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tentukan apakah formulir ini untuk asesi atau asesor</small>
                            </div>

                            <!-- Current File Info -->
                            <div class="mb-3">
                                <label class="form-label">File Saat Ini</label>
                                <div class="alert alert-secondary d-flex align-items-center justify-content-between mb-0">
                                    <div class="d-flex align-items-center flex-grow-1 overflow-hidden mr-3">
                                        <i class="fas fa-file-alt mr-2 text-primary" style="font-size: 1.5rem; flex-shrink: 0;"></i>
                                        <div class="overflow-hidden">
                                            <strong class="d-block text-truncate" style="max-width: 100%;" title="{{ $bankSoal->original_filename }}">
                                                {{ $bankSoal->original_filename }}
                                            </strong>
                                            <small class="text-muted">
                                                Diupload: {{ $bankSoal->updated_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.bank-soal.download', $bankSoal->id) }}"
                                       class="btn btn-sm btn-info flex-shrink-0">
                                        <i class="fas fa-download mr-1"></i> Download
                                    </a>
                                </div>
                            </div>

                            <!-- Upload New File (Optional) -->
                            <div class="mb-3">
                                <label for="file" class="form-label">Upload File Baru (Opsional)</label>
                                <input type="file" class="form-control @error('file') is-invalid @enderror"
                                    id="file" name="file" accept=".pdf,.doc,.docx" onchange="displayFileSize(this)">
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block">Kosongkan jika tidak ingin mengubah file. Format: PDF, DOC, DOCX. Maksimal 10MB</small>
                                <small class="text-info" id="fileSizeInfo"></small>
                            </div>

                            <!-- Keterangan -->
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror"
                                    id="keterangan" name="keterangan" rows="3"
                                    placeholder="Tambahkan keterangan atau catatan jika diperlukan">{{ old('keterangan', $bankSoal->keterangan) }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Variables Section with Tabs -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Setup Template Variables (Opsional)</label>
                                <p class="small text-muted mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Jika menggunakan file DOCX, Anda dapat setup variables untuk auto-fill data. Gunakan format <code>${variable}</code> dalam file .docx
                                </p>

                                <!-- Nav Tabs (Bootstrap 4 compatible) -->
                                <ul class="nav nav-tabs mb-3" id="variablesTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="database-fields-tab" data-toggle="tab" href="#database-fields" role="tab">
                                            <i class="fas fa-database me-2"></i>Database Fields
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-fields-tab" data-toggle="tab" href="#custom-fields" role="tab">
                                            <i class="fas fa-plus-circle me-2"></i>Custom Fields
                                        </a>
                                    </li>
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content" id="variablesTabContent">

                                    <!-- Database Fields Tab -->
                                    <div class="tab-pane fade show active" id="database-fields" role="tabpanel">
                                        <div class="alert alert-light border mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-lightbulb me-1"></i>
                                                <strong>Tips:</strong> Field ini otomatis diambil dari database. Centang field yang ingin digunakan di template.
                                                <br>
                                                <strong>Format di template:</strong> Gunakan <code>${variable}</code> di file DOCX.
                                                Contoh: <code>${user.name}</code>, <code>${skema.nama}</code>
                                            </small>
                                        </div>

                                        <div class="row">
                                            @php
                                                $userFields = array_filter($availableFields, function($key) {
                                                    return strpos($key, 'user.') === 0;
                                                }, ARRAY_FILTER_USE_KEY);

                                                $asesorFields = array_filter($availableFields, function($key) {
                                                    return strpos($key, 'asesor.') === 0;
                                                }, ARRAY_FILTER_USE_KEY);

                                                $skemaFields = array_filter($availableFields, function($key) {
                                                    return strpos($key, 'skema.') === 0;
                                                }, ARRAY_FILTER_USE_KEY);

                                                $jadwalFields = array_filter($availableFields, function($key) {
                                                    return strpos($key, 'jadwal.') === 0;
                                                }, ARRAY_FILTER_USE_KEY);

                                                $systemFields = array_filter($availableFields, function($key) {
                                                    return strpos($key, 'system.') === 0;
                                                }, ARRAY_FILTER_USE_KEY);

                                                // Get existing selected variables
                                                $existingVariables = old('variables', $bankSoal->variables ?? []);
                                                // Ensure $existingVariables is always an array
                                                if (!is_array($existingVariables)) {
                                                    $existingVariables = [];
                                                }
                                            @endphp

                                            <div class="col-md-6">
                                                <div class="card mb-3" style="border-left: 3px solid #007bff;">
                                                    <div class="card-body">
                                                        <h6 class="text-primary mb-3">
                                                            <i class="fas fa-user me-2"></i>Data User/Asesi
                                                        </h6>
                                            @foreach($userFields as $field => $label)
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input database-field" type="checkbox"
                                                                value="{{ $field }}" id="field_{{ str_replace('.', '_', $field) }}"
                                                                {{ in_array($field, $existingVariables) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="field_{{ str_replace('.', '_', $field) }}">
                                                                <strong>{{ $label }}</strong>
                                                                <small class="text-muted d-block"><code>${{ $field }}</code></small>
                                                            </label>
                                                        </div>
                                            @endforeach
                                                    </div>
                                                </div>

                                                <div class="card mb-3" style="border-left: 3px solid #17a2b8;">
                                                    <div class="card-body">
                                                        <h6 class="text-info mb-3">
                                                            <i class="fas fa-user-tie me-2"></i>Data Asesor
                                                        </h6>
                                            @foreach($asesorFields as $field => $label)
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input database-field" type="checkbox"
                                                                value="{{ $field }}" id="field_{{ str_replace('.', '_', $field) }}"
                                                                {{ in_array($field, $existingVariables) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="field_{{ str_replace('.', '_', $field) }}">
                                                                <strong>{{ $label }}</strong>
                                                                <small class="text-muted d-block"><code>${{ $field }}</code></small>
                                                            </label>
                                                        </div>
                                            @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card mb-3" style="border-left: 3px solid #28a745;">
                                                    <div class="card-body">
                                                        <h6 class="text-success mb-3">
                                                            <i class="fas fa-certificate me-2"></i>Data Skema
                                                        </h6>
                                            @foreach($skemaFields as $field => $label)
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input database-field" type="checkbox"
                                                                value="{{ $field }}" id="field_{{ str_replace('.', '_', $field) }}"
                                                                {{ in_array($field, $existingVariables) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="field_{{ str_replace('.', '_', $field) }}">
                                                                <strong>{{ $label }}</strong>
                                                                <small class="text-muted d-block"><code>${{ $field }}</code></small>
                                                            </label>
                                                        </div>
                                            @endforeach
                                                    </div>
                                                </div>

                                                <div class="card mb-3" style="border-left: 3px solid #ffc107;">
                                                    <div class="card-body">
                                                        <h6 class="text-warning mb-3">
                                                            <i class="fas fa-calendar me-2"></i>Data Jadwal
                                                        </h6>
                                            @foreach($jadwalFields as $field => $label)
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input database-field" type="checkbox"
                                                                value="{{ $field }}" id="field_{{ str_replace('.', '_', $field) }}"
                                                                {{ in_array($field, $existingVariables) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="field_{{ str_replace('.', '_', $field) }}">
                                                                <strong>{{ $label }}</strong>
                                                                <small class="text-muted d-block"><code>${{ $field }}</code></small>
                                                            </label>
                                                        </div>
                                            @endforeach
                                                    </div>
                                                </div>

                                                <div class="card mb-3" style="border-left: 3px solid #6c757d;">
                                                    <div class="card-body">
                                                        <h6 class="text-secondary mb-3">
                                                            <i class="fas fa-cog me-2"></i>Data System
                                                        </h6>
                                            @foreach($systemFields as $field => $label)
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input database-field" type="checkbox"
                                                                value="{{ $field }}" id="field_{{ str_replace('.', '_', $field) }}"
                                                                {{ in_array($field, $existingVariables) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="field_{{ str_replace('.', '_', $field) }}">
                                                                <strong>{{ $label }}</strong>
                                                                <small class="text-muted d-block"><code>${{ $field }}</code></small>
                                                            </label>
                                                        </div>
                                            @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Selected Variables Display -->
                                        <div class="mt-3">
                                            <h6 class="mb-2">Variables yang Dipilih:</h6>
                                            <div id="selected-variables-container" class="p-3 bg-light rounded border">
                                                <span class="text-muted"><em>Belum ada variable yang dipilih</em></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Custom Fields Tab -->
                                    <div class="tab-pane fade" id="custom-fields" role="tabpanel">
                                        <div class="alert alert-warning border mb-3">
                                            <small>
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                <strong>Custom Fields:</strong> Field ini akan menjadi form input untuk user dan variable di template DOCX.
                                                <br>
                                                <strong>Format di template:</strong> Gunakan <code>${variable}</code> di file DOCX.
                                                Contoh: <code>${pertanyaan_1}</code>, <code>${nama_perusahaan}</code>
                                            </small>
                                        </div>

                                        <div id="custom-variables-container">
                                            <!-- Will be populated by JavaScript -->
                                        </div>

                                        <button type="button" class="btn btn-primary mt-3" id="add-custom-variable">
                                            <i class="fas fa-plus me-1"></i> Tambah Custom Field
                                        </button>
                                    </div>
                                </div>

                                <!-- Hidden inputs -->
                                <input type="hidden" name="variables" id="variables-input">
                                <input type="hidden" name="field_configurations" id="field_configurations">
                                <input type="hidden" name="field_mappings" id="field_mappings">
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.bank-soal.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times mr-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary" id="submit-btn">
                                    <i class="fas fa-save mr-1"></i> Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Info Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="fas fa-info-circle mr-1"></i> Informasi
                        </h6>
                    </div>
                    <div class="card-body">
                        <h6 class="font-weight-bold">Detail Bank Soal:</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 40%;">Dibuat:</td>
                                <td>{{ $bankSoal->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Diupdate:</td>
                                <td>{{ $bankSoal->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status:</td>
                                <td>
                                    @if($bankSoal->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <hr>

                        <h6 class="font-weight-bold">Panduan Upload:</h6>
                        <ol class="small pl-3">
                            <li class="mb-2">Pastikan file sesuai dengan tipe formulir yang dipilih</li>
                            <li class="mb-2">Gunakan nama yang deskriptif untuk memudahkan pencarian</li>
                            <li class="mb-2">Periksa kembali target pengguna (asesi atau asesor)</li>
                            <li class="mb-2">Jika menggunakan DOCX, setup variables untuk auto-fill data</li>
                            <li class="mb-2">Tambahkan keterangan jika formulir memiliki versi atau catatan khusus</li>
                        </ol>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-warning">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Perhatian
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-0">Jika Anda mengupload file baru, file lama akan dihapus dan diganti dengan file yang baru.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        .form-check {
            margin-bottom: 0.5rem;
        }

        .selected-variable-item {
            display: inline-block;
            margin: 0.25rem;
            padding: 0.25rem 0.5rem;
            background-color: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .selected-variable-item .remove-selected {
            margin-left: 0.5rem;
            color: #f44336;
            cursor: pointer;
        }
    </style>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let selectedVariables = [];
        let customVariables = [];
        let customVariableIndex = 0;

        // Load existing data
        @if($bankSoal->variables)
            selectedVariables = @json($bankSoal->variables);
        @endif

        @if($bankSoal->custom_variables)
            const existingCustomVars = @json($bankSoal->custom_variables);
            existingCustomVars.forEach(function(customVar, index) {
                addCustomVariableRow(index, customVar);
                customVariableIndex++;
            });
            updateCustomVariables();
        @endif

        // Check the checkboxes that are in selectedVariables
        selectedVariables.forEach(variable => {
            $(`input[value="${variable}"]`).prop('checked', true);
        });

        // Update display on load
        updateSelectedVariablesDisplay();
        updateVariablesInput();

        // Debug: Log loaded data
        console.log('Loaded selectedVariables:', selectedVariables);
        console.log('Loaded customVariables:', customVariables);

        // Auto-update target based on tipe selection
        $('#tipe').on('change', function() {
            const tipe = $(this).val();
            const targetSelect = $('#target');

            if (tipe === 'FR IA 07') {
                targetSelect.val('asesor');
            } else if (tipe === 'FR IA 03' || tipe === 'FR IA 06') {
                targetSelect.val('asesi');
            }
        });

        // File validation
        $('#file').on('change', function() {
            const file = this.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB

            if (file && file.size > maxSize) {
                alert('Ukuran file melebihi 10MB. Silakan pilih file yang lebih kecil.');
                $(this).val('');
            }
        });

        // Handle database field selection
        $('.database-field').on('change', function() {
            const fieldValue = $(this).val();
            const fieldLabel = $(this).next('label').text();

            console.log('Checkbox changed:', fieldValue, 'Checked:', $(this).is(':checked'));

            if ($(this).is(':checked')) {
                if (!selectedVariables.includes(fieldValue)) {
                    selectedVariables.push(fieldValue);
                    console.log('Added to selectedVariables:', selectedVariables);
                    updateSelectedVariablesDisplay();
                    updateVariablesInput();
                }
            } else {
                selectedVariables = selectedVariables.filter(v => v !== fieldValue);
                console.log('Removed from selectedVariables:', selectedVariables);
                updateSelectedVariablesDisplay();
                updateVariablesInput();
            }
        });

        // Handle custom variable addition
        $('#add-custom-variable').on('click', function(e) {
            e.preventDefault();
            addCustomVariableRow(customVariableIndex);
            customVariableIndex++;
        });

        // Handle custom variable removal
        $(document).on('click', '.remove-custom-variable', function(e) {
            e.preventDefault();
            $(this).closest('.custom-variable-row').remove();
            updateCustomVariables();
            updateVariablesInput();
        });

        // Handle custom variable input change
        $(document).on('input change', '.custom-variable-row input, .custom-variable-row select, .custom-variable-row textarea', function() {
            updateCustomVariables();
            updateVariablesInput();
        });

        // Character counter for label inputs
        $(document).on('input', '.custom-label-input', function() {
            const currentLength = $(this).val().length;
            $(this).closest('.col-md-4').find('.char-count').text(`${currentLength} karakter`);
        });

        function addCustomVariableRow(index, data = null) {
            const html = `
                <div class="custom-variable-row bg-white border rounded p-3 mb-3" style="border-left: 4px solid #ffc107 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0" style="color: #f59e0b;">
                            <i class="fas fa-edit me-2"></i>Custom Field #${index + 1}
                        </h6>
                        <button type="button" class="btn btn-sm btn-danger remove-custom-variable">
                            <i class="fas fa-trash me-1"></i>Hapus
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-semibold">
                                Nama Variable <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="custom_variables[${index}][name]" class="form-control"
                                placeholder="contoh: pertanyaan_1" value="${data ? data.name : ''}">
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle me-1"></i>
                                Akan menjadi <code>$\${nama_variable}</code> di template DOCX
                            </small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-semibold">
                                Label/Pertanyaan <span class="text-danger">*</span>
                            </label>
                            <textarea name="custom_variables[${index}][label]" class="form-control custom-label-input" rows="3"
                                placeholder="Apakah Anda mampu...">${data ? data.label : ''}</textarea>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle me-1"></i>
                                Label/soal yang ditampilkan di form
                                <span class="char-count float-right text-muted">0 karakter</span>
                            </small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-semibold">
                                Untuk Siapa? <span class="text-danger">*</span>
                            </label>
                            <select name="custom_variables[${index}][role]" class="form-control">
                                <option value="asesi" ${data && data.role === 'asesi' ? 'selected' : ''}>Asesi (Peserta)</option>
                                <option value="asesor" ${data && data.role === 'asesor' ? 'selected' : ''}>Asesor (Penguji)</option>
                                <option value="both" ${data && data.role === 'both' ? 'selected' : ''}>Keduanya</option>
                            </select>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle me-1"></i>
                                Field ini akan ditampilkan untuk siapa?
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-semibold">Tipe Input</label>
                            <select name="custom_variables[${index}][type]" class="form-control">
                                <option value="text" ${data && data.type === 'text' ? 'selected' : ''}>Text</option>
                                <option value="textarea" ${data && data.type === 'textarea' ? 'selected' : ''}>Textarea</option>
                                <option value="checkbox" ${data && data.type === 'checkbox' ? 'selected' : ''}>Checkbox</option>
                                <option value="radio" ${data && data.type === 'radio' ? 'selected' : ''}>Radio</option>
                                <option value="select" ${data && data.type === 'select' ? 'selected' : ''}>Select</option>
                                <option value="number" ${data && data.type === 'number' ? 'selected' : ''}>Number</option>
                                <option value="email" ${data && data.type === 'email' ? 'selected' : ''}>Email</option>
                                <option value="date" ${data && data.type === 'date' ? 'selected' : ''}>Date</option>
                                <option value="file" ${data && data.type === 'file' ? 'selected' : ''}>File Upload</option>
                                <option value="signature_pad" ${data && data.type === 'signature_pad' ? 'selected' : ''}>Signature Pad</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-semibold">
                                Options <span class="text-muted small">(untuk checkbox/radio/select)</span>
                            </label>
                            <textarea name="custom_variables[${index}][options]" class="form-control" rows="2"
                                placeholder="Contoh: BK,K atau Belum Kompeten,Kompeten">${data && data.options ? (Array.isArray(data.options) ? data.options.join(',') : data.options) : ''}</textarea>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle me-1"></i>
                                Pisahkan dengan koma. Bisa tulis opsi panjang.
                            </small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-semibold">Required?</label>
                            <select name="custom_variables[${index}][required]" class="form-control">
                                <option value="0" ${data && !data.required ? 'selected' : ''}>Tidak</option>
                                <option value="1" ${data && data.required ? 'selected' : ''}>Ya</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">
                                Database Mapping <span class="text-muted small">(Opsional)</span>
                            </label>
                            <select name="custom_variables[${index}][mapping]" class="form-control">
                                <option value="">-- Custom Field --</option>
                                <optgroup label="Data User">
                                    <option value="user.name">Nama User</option>
                                    <option value="user.email">Email</option>
                                    <option value="user.telephone">Telepon</option>
                                    <option value="user.alamat">Alamat</option>
                                    <option value="user.nik">NIK</option>
                                    <option value="user.nim">NIM</option>
                                    <option value="user.tempat_lahir">Tempat Lahir</option>
                                    <option value="user.tanggal_lahir">Tanggal Lahir</option>
                                    <option value="user.jenis_kelamin">Jenis Kelamin</option>
                                    <option value="user.pekerjaan">Pekerjaan</option>
                                    <option value="user.pendidikan">Pendidikan</option>
                                    <option value="user.jurusan">Jurusan</option>
                                </optgroup>
                            </select>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle me-1"></i>
                                Jika dipilih, field ini akan terisi otomatis dari database
                            </small>
                        </div>
                    </div>
                </div>
            `;
            $('#custom-variables-container').append(html);

            // Initialize character count for the newly added row
            const $newRow = $('#custom-variables-container').children().last();
            const labelValue = data && data.label ? data.label : '';
            $newRow.find('.char-count').text(`${labelValue.length} karakter`);
        }

        function updateSelectedVariablesDisplay() {
            const container = $('#selected-variables-container');
            container.empty();

            if (selectedVariables.length === 0) {
                container.html('<span class="text-muted"><em>Belum ada variable yang dipilih</em></span>');
                return;
            }

            selectedVariables.forEach(variable => {
                // Try to find label from checkbox
                let label = $(`input[value="${variable}"]`).next('label').find('strong').text();

                // If not found, use the variable name itself as fallback
                if (!label || label.trim() === '') {
                    label = variable;
                }

                const html = `
                    <span class="selected-variable-item">
                        ${label}
                        <span class="remove-selected" data-variable="${variable}">×</span>
                    </span>
                `;
                container.append(html);
            });

            // Handle removal of selected variables
            $('.remove-selected').on('click', function() {
                const variable = $(this).data('variable');
                selectedVariables = selectedVariables.filter(v => v !== variable);
                $(`input[value="${variable}"]`).prop('checked', false);
                updateSelectedVariablesDisplay();
                updateVariablesInput();
            });
        }

        function updateCustomVariables() {
            customVariables = [];
            const configurations = [];
            const mappings = {};

            $('.custom-variable-row').each(function() {
                const name = $(this).find('input[name*="[name]"]').val()?.trim() || '';
                const label = $(this).find('textarea[name*="[label]"]').val()?.trim() || '';  // Changed to textarea
                const type = $(this).find('select[name*="[type]"]').val() || '';
                const options = $(this).find('textarea[name*="[options]"]').val()?.trim() || '';  // Changed to textarea
                const required = $(this).find('select[name*="[required]"]').val() || '0';
                const role = $(this).find('select[name*="[role]"]').val() || 'asesi';
                const mapping = $(this).find('select[name*="[mapping]"]').val() || '';

                if (name && label) {
                    const variable = {
                        name: name,
                        label: label,
                        type: type,
                        required: required === '1',
                        role: role || 'asesi'
                    };

                    if (options && ['checkbox', 'radio', 'select'].includes(type)) {
                        variable.options = options.split(',').map(opt => opt.trim());
                    }

                    customVariables.push(variable);
                    configurations.push(variable);

                    // Save mapping if exists
                    if (mapping) {
                        mappings[name] = mapping;
                    }
                }
            });

            // Update hidden inputs for field configurations
            $('#field_configurations').val(JSON.stringify(configurations));
            $('#field_mappings').val(JSON.stringify(mappings));
        }

        function updateVariablesInput() {
            const filteredSelectedVariables = selectedVariables.filter(v => v && typeof v === 'string' && v.trim() !== '');
            const filteredCustomVariables = customVariables
                .map(v => v.name)
                .filter(v => v && typeof v === 'string' && v.trim() !== '');
            const allVariables = [...filteredSelectedVariables, ...filteredCustomVariables];
            $('#variables-input').val(JSON.stringify(allVariables));

            console.log('Updated variables input:', allVariables);
        }

        // Form submission handler
        $('form').on('submit', function(e) {
            // Update all hidden inputs before submit
            updateCustomVariables();
            updateVariablesInput();

            console.log('Form submitting...');
            console.log('Variables:', $('#variables-input').val());
            console.log('Field Configurations:', $('#field_configurations').val());
            console.log('Field Mappings:', $('#field_mappings').val());

            // Optional: Disable submit button to prevent double submission
            $('#submit-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Updating...');
        });
    });

    // Function to display file size
    function displayFileSize(input) {
        const fileSizeInfo = document.getElementById('fileSizeInfo');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileSize = file.size;
            const fileSizeMB = (fileSize / (1024 * 1024)).toFixed(2);

            if (fileSize > 10 * 1024 * 1024) { // 10MB
                fileSizeInfo.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Ukuran file: ' + fileSizeMB + ' MB (Terlalu besar! Maksimal 10MB)';
                fileSizeInfo.className = 'text-danger d-block mt-1';
                input.value = ''; // Clear file
            } else {
                fileSizeInfo.innerHTML = '<i class="fas fa-check-circle"></i> Ukuran file: ' + fileSizeMB + ' MB';
                fileSizeInfo.className = 'text-success d-block mt-1';
            }
        } else {
            fileSizeInfo.innerHTML = '';
        }
    }
</script>
@endpush
