@extends('components.templates.master-layout')

@section('title', 'Template Master - Create')
@section('page-title', 'Tambah Template Master')

@section('content')

    <a href="{{ route('admin.template-master.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
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
                    <p class="mb-2 small"><strong>Untuk APL2 dengan Checkbox K/BK:</strong></p>
                    <ul class="small mb-2" style="line-height: 1.8;">
                        <li><strong>Per Pertanyaan (Flexible):</strong> <code>${nama_variable_k}</code> dan <code>${nama_variable_bk}</code></li>
                        <li>Contoh: Jika nama variable = "pertanyaan_1", gunakan <code>${pertanyaan_1_k}</code> dan <code>${pertanyaan_1_bk}</code></li>
                        <li><strong>Legacy (Kolom):</strong> <code>${k_checkbox}</code> dan <code>${bk_checkbox}</code> untuk semua pertanyaan dalam 1 kolom</li>
                        <li>Gunakan di tabel DOCX sesuai layout yang diinginkan</li>
                    </ul>
                    <p class="mb-2 small"><strong>Role-based Custom Fields:</strong></p>
                    <ul class="small mb-2" style="line-height: 1.8;">
                        <li><strong>Asesi</strong> → Field hanya ditampilkan dan diisi oleh asesi/peserta</li>
                        <li><strong>Asesor</strong> → Field hanya ditampilkan dan diisi oleh asesor/penguji</li>
                        <li><strong>Keduanya</strong> → Field ditampilkan untuk asesi dan asesor</li>
                    </ul>
                    <p class="mb-2 small"><strong>Khusus untuk FR AK 05 (Tabel Dinamis Asesi):</strong></p>
                    <ul class="small mb-2" style="line-height: 1.8;">
                        <li><strong>Header Otomatis:</strong> <code>${skema.judul}</code>, <code>${skema.nomor}</code>, <code>${tuk}</code>, <code>${nama_asesor}</code>, <code>${tanggal}</code></li>
                        <li><strong>Tabel Asesi (Auto-Clone):</strong> Buat 1 row dengan <code>${no}</code>, <code>${nama_asesi}</code>, <code>${checkbox_k}</code>, <code>${checkbox_bk}</code>, <code>${keterangan}</code></li>
                        <li>Row tersebut akan <strong>otomatis di-clone</strong> sebanyak jumlah asesi yang dinilai</li>
                        <li>Checkbox K/BK otomatis tercentang sesuai hasil penilaian (☑ = ya, ☐ = tidak)</li>
                        <li><strong>Statistik:</strong> <code>${total_asesi}</code>, <code>${asesi_kompeten}</code>, <code>${asesi_tidak_kompeten}</code></li>
                    </ul>
                    <div class="mt-3">
                        <a href="{{ asset('PETUNJUK_FR_AK_05.md') }}" target="_blank" class="btn btn-sm btn-primary me-2">
                            <i class="fas fa-book me-1"></i> Petunjuk Lengkap FR AK 05
                        </a>
                        @if(file_exists(public_path('storage/templates/sample_apl1_template.docx')))
                            <a href="{{ asset('storage/templates/sample_apl1_template.docx') }}" class="btn btn-sm btn-success me-2" download>
                                <i class="fas fa-download me-1"></i> Sample APL1
                            </a>
                        @endif
                        @if(file_exists(public_path('storage/templates/sample_apl2_template.docx')))
                            <a href="{{ asset('storage/templates/sample_apl2_template.docx') }}" class="btn btn-sm btn-success me-2" download>
                                <i class="fas fa-download me-1"></i> Sample APL2
                            </a>
                        @endif
                        @if(file_exists(public_path('storage/templates/sample_fr_ak_05_template.docx')))
                            <a href="{{ asset('storage/templates/sample_fr_ak_05_template.docx') }}" class="btn btn-sm btn-success" download>
                                <i class="fas fa-download me-1"></i> Sample FR AK 05
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Form Template Master</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.template-master.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Nama Template -->
                            <div class="mb-3">
                                <label for="nama_template" class="form-label">Nama Template <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_template') is-invalid @enderror"
                                    id="nama_template" name="nama_template" value="{{ old('nama_template') }}" required>
                                @error('nama_template')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tipe Template -->
                            <div class="mb-3">
                                <label for="tipe_template" class="form-label">Tipe Template <span class="text-danger">*</span></label>
                                <select class="form-control @error('tipe_template') is-invalid @enderror"
                                    id="tipe_template" name="tipe_template" required>
                                    <option value="">Pilih Tipe Template</option>
                                    @foreach($tipeTemplateOptions as $key => $label)
                                        <option value="{{ $key }}" {{ old('tipe_template') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipe_template')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Skema -->
                            <div class="mb-3">
                                <label for="skema_id" class="form-label">Skema <span class="text-danger">*</span></label>
                                <select class="form-control @error('skema_id') is-invalid @enderror"
                                    id="skema_id" name="skema_id" required>
                                    <option value="">Pilih Skema</option>
                                    @foreach($skemas as $skema)
                                        <option value="{{ $skema->id }}" {{ old('skema_id') == $skema->id ? 'selected' : '' }}>
                                            {{ $skema->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('skema_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                    id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- File Template -->
                            <div class="mb-3">
                                <label for="file_template" class="form-label">File Template (.docx) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('file_template') is-invalid @enderror"
                                    id="file_template" name="file_template" accept=".docx" required>
                                <small class="form-text text-muted">Upload file template dalam format .docx. Gunakan format ${variable} untuk variable.</small>
                                @error('file_template')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Variables Section with Tabs -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Variables Template <span class="text-danger">*</span></label>
                                <p class="small text-muted mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Pilih field dari database atau buat variable custom. Gunakan format <code>${variable}</code> dalam file .docx
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
                                            <i class="fas fa-plus-circle me-2"></i>Custom Fields (untuk APL2)
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

                                                $skemaFields = array_filter($availableFields, function($key) {
                                                    return strpos($key, 'skema.') === 0;
                                                }, ARRAY_FILTER_USE_KEY);

                                                $jadwalFields = array_filter($availableFields, function($key) {
                                                    return strpos($key, 'jadwal.') === 0;
                                                }, ARRAY_FILTER_USE_KEY);

                                                $systemFields = array_filter($availableFields, function($key) {
                                                    return strpos($key, 'system.') === 0;
                                                }, ARRAY_FILTER_USE_KEY);
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
                                                                value="{{ $field }}" id="field_{{ str_replace('.', '_', $field) }}">
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
                                                                value="{{ $field }}" id="field_{{ str_replace('.', '_', $field) }}">
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
                                                                value="{{ $field }}" id="field_{{ str_replace('.', '_', $field) }}">
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
                                                                value="{{ $field }}" id="field_{{ str_replace('.', '_', $field) }}">
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

                                        @if(file_exists(public_path('storage/templates/sample_apl1_template.docx')))
                                            <div class="mt-3 text-end">
                                                <a href="{{ asset('storage/templates/sample_apl1_template.docx') }}" class="btn btn-success" download>
                                                    <i class="fas fa-download me-1"></i> Download Sample Template APL1
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Custom Fields Tab -->
                                    <div class="tab-pane fade" id="custom-fields" role="tabpanel">
                                        <div class="alert alert-warning border mb-3">
                                            <small>
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                <strong>Untuk APL2:</strong> Custom fields akan menjadi pertanyaan di form asesi dan variable di template DOCX.
                                                Field ini akan otomatis membuat form input untuk asesi dan bisa di-mapping ke database.
                                                <br>
                                                <strong>Format di template:</strong> Gunakan <code>${variable}</code> di file DOCX.
                                                Contoh: <code>${pertanyaan_1}</code>, <code>${nama_perusahaan}</code>
                                            </small>
                                        </div>

                                        <div id="custom-variables-container">
                                            <!-- Will be populated by JavaScript -->
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <button type="button" class="btn btn-primary" id="add-custom-variable">
                                                <i class="fas fa-plus me-1"></i> Tambah Custom Field
                                            </button>

                                            @if(file_exists(public_path('storage/templates/sample_apl2_template.docx')))
                                                <a href="{{ asset('storage/templates/sample_apl2_template.docx') }}" class="btn btn-success" download>
                                                    <i class="fas fa-download me-1"></i> Download Sample Template APL2
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden inputs -->
                                <input type="hidden" name="variables" id="variables-input">
                                <input type="hidden" name="field_configurations" id="field_configurations">
                                <input type="hidden" name="field_mappings" id="field_mappings">
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Template
                                </button>
                            </div>
                        </form>
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

    @push('scripts')
    <script>
        $(document).ready(function() {
            let selectedVariables = [];
            let customVariables = [];
            let customVariableIndex = 0;

            // Handle template type change
            $('#tipe_template').on('change', function() {
                const selectedType = $(this).val();
                if (selectedType === 'APL2') {
                    $('#apl2-config-section').show();
                    // Add default BK/K question if no custom variables exist
                    if ($('.custom-variable-row').length === 0) {
                        addCustomVariableRow(customVariableIndex);
                        customVariableIndex++;
                        // Pre-fill with BK/K example
                        const lastRow = $('.custom-variable-row').last();
                        lastRow.find('input[name*="[name]"]').val('pertanyaan_bk_k_1');
                        lastRow.find('input[name*="[label]"]').val('Apakah Anda mampu mengaplikasikan keterampilan dasar komunikasi?');
                        lastRow.find('select[name*="[type]"]').val('radio');
                        lastRow.find('input[name*="[options]"]').val('BK,K');
                        lastRow.find('select[name*="[required]"]').val('1');
                        updateCustomVariables();
                        updateVariablesInput();
                    }
                } else {
                    $('#apl2-config-section').hide();
                }
            });

            // Handle database field selection
            $('.database-field').on('change', function() {
                const fieldValue = $(this).val();
                const fieldLabel = $(this).next('label').text();

                if ($(this).is(':checked')) {
                    if (!selectedVariables.includes(fieldValue)) {
                        selectedVariables.push(fieldValue);
                        updateSelectedVariablesDisplay();
                        updateVariablesInput();
                    }
                } else {
                    selectedVariables = selectedVariables.filter(v => v !== fieldValue);
                    updateSelectedVariablesDisplay();
                    updateVariablesInput();
                }
            });

            // Handle custom variable addition
            $('#add-custom-variable').on('click', function(e) {
                e.preventDefault(); // Prevent form submission
                addCustomVariableRow(customVariableIndex);
                customVariableIndex++;
            });

            // Handle custom variable removal
            $(document).on('click', '.remove-custom-variable', function(e) {
                e.preventDefault(); // Prevent form submission
                $(this).closest('.custom-variable-row').remove();
                updateCustomVariables();
                updateVariablesInput();
            });

            // Handle custom variable input change
            $(document).on('input change', '.custom-variable-row input, .custom-variable-row select', function() {
                updateCustomVariables();
                updateVariablesInput();
            });

            // Handle custom variable type change for BK/K auto-fill
            $(document).on('change', 'select[name*="[type]"]', function() {
                const type = $(this).val();
                const row = $(this).closest('.custom-variable-row');
                const optionsInput = row.find('input[name*="[options]"]');
                const labelInput = row.find('input[name*="[label]"]');

                if (type === 'radio') {
                    // Suggest BK/K options for radio type
                    if (!optionsInput.val()) {
                        optionsInput.attr('placeholder', 'Contoh: BK,K atau Belum Kompeten,Kompeten');
                    }
                } else if (['checkbox', 'select'].includes(type)) {
                    optionsInput.attr('placeholder', 'Pisahkan dengan koma (contoh: Opsi 1, Opsi 2)');
                } else {
                    optionsInput.val('');
                    optionsInput.attr('placeholder', '');
                }
            });

            // Handle custom variable blur - remove empty rows
            $(document).on('blur', 'input[name="custom_variables[]"]', function() {
                const value = $(this).val().trim();
                if (!value && $('.custom-variable-row').length > 1) {
                    $(this).closest('.custom-variable-row').remove();
                }
                updateCustomVariables();
                updateVariablesInput();
            });

            function addCustomVariableRow(index) {
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
                                    placeholder="contoh: pertanyaan_1">
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Akan menjadi <code>$\${nama_variable}</code> di template DOCX
                                </small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-semibold">
                                    Label/Pertanyaan <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="custom_variables[${index}][label]" class="form-control"
                                    placeholder="Apakah Anda mampu...">
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Label yang ditampilkan di form
                                </small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-semibold">
                                    Untuk Siapa? <span class="text-danger">*</span>
                                </label>
                                <select name="custom_variables[${index}][role]" class="form-control">
                                    <option value="asesi">Asesi (Peserta)</option>
                                    <option value="asesor">Asesor (Penguji)</option>
                                    <option value="both">Keduanya</option>
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
                                    <option value="text">Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="radio">Radio</option>
                                    <option value="select">Select</option>
                                    <option value="number">Number</option>
                                    <option value="email">Email</option>
                                    <option value="date">Date</option>
                                    <option value="file">File Upload</option>
                                    <option value="signature_pad">Signature Pad</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">
                                    Options <span class="text-muted small">(untuk checkbox/radio/select)</span>
                                </label>
                                <input type="text" name="custom_variables[${index}][options]" class="form-control"
                                    placeholder="Contoh: BK,K atau Belum Kompeten,Kompeten">
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Pisahkan dengan koma
                                </small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label small fw-semibold">Required?</label>
                                <select name="custom_variables[${index}][required]" class="form-control">
                                    <option value="0">Tidak</option>
                                    <option value="1">Ya</option>
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
            }

            function updateSelectedVariablesDisplay() {
                const container = $('#selected-variables-container');
                container.empty();

                selectedVariables.forEach(variable => {
                    const label = $(`input[value="${variable}"]`).next('label').text();
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
                    const name = $(this).find('input[name*="[name]"]').val().trim();
                    const label = $(this).find('input[name*="[label]"]').val().trim();
                    const type = $(this).find('select[name*="[type]"]').val();
                    const options = $(this).find('input[name*="[options]"]').val().trim();
                    const required = $(this).find('select[name*="[required]"]').val();
                    const role = $(this).find('select[name*="[role]"]').val();
                    const mapping = $(this).find('select[name*="[mapping]"]').val();

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
                // Filter out empty values
                const filteredSelectedVariables = selectedVariables.filter(v => v && v.trim() !== '');
                const filteredCustomVariables = customVariables.map(v => v.name).filter(v => v && v.trim() !== '');
                const allVariables = [...filteredSelectedVariables, ...filteredCustomVariables];
                $('#variables-input').val(JSON.stringify(allVariables));
            }


            // Initialize with old values if any
            @if(old('variables'))
                try {
                    const oldVariables = JSON.parse('{{ old("variables") }}');
                    oldVariables.forEach(variable => {
                        if (variable.includes('.')) {
                            // Database field
                            selectedVariables.push(variable);
                            $(`input[value="${variable}"]`).prop('checked', true);
                        } else {
                            // Custom variable
                            addCustomVariableRow(variable);
                        }
                    });
                    updateSelectedVariablesDisplay();
                    updateCustomVariables();
                    updateVariablesInput();
                } catch (e) {
                    console.error('Error parsing old variables:', e);
                }
            @endif
        });

        // Dynamic Field Configuration
        let fieldConfigCount = 1;

        // Add field configuration
        $('#add-field-config').click(function() {
            const fieldConfigHtml = `
                <div class="field-config-item border rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Nama Field</label>
                            <input type="text" class="form-control field-name" placeholder="nama_field" value="">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Label</label>
                            <input type="text" class="form-control field-label" placeholder="Nama Field" value="">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tipe</label>
                            <select class="form-control field-type">
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="radio">Radio</option>
                                <option value="select">Select</option>
                                <option value="number">Number</option>
                                <option value="email">Email</option>
                                <option value="date">Date</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Options (untuk checkbox/radio/select)</label>
                            <input type="text" class="form-control field-options" placeholder="option1,option2,option3" value="">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Required</label>
                            <select class="form-control field-required">
                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Database Mapping</label>
                            <select class="form-control field-mapping">
                                <option value="">Custom Field</option>
                                <option value="user.name">Nama User</option>
                                <option value="user.email">Email User</option>
                                <option value="user.telephone">Telepon User</option>
                                <option value="user.alamat">Alamat User</option>
                                <option value="user.nik">NIK User</option>
                                <option value="user.nim">NIM User</option>
                                <option value="user.tempat_lahir">Tempat Lahir</option>
                                <option value="user.tanggal_lahir">Tanggal Lahir</option>
                                <option value="user.jenis_kelamin">Jenis Kelamin</option>
                                <option value="user.pekerjaan">Pekerjaan</option>
                                <option value="user.pendidikan">Pendidikan</option>
                                <option value="user.jurusan">Jurusan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Aksi</label>
                            <div>
                                <button type="button" class="btn btn-danger btn-sm remove-field-config">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#field-configurations-container').append(fieldConfigHtml);
            fieldConfigCount++;
        });

        // Remove field configuration
        $(document).on('click', '.remove-field-config', function() {
            if ($('.field-config-item').length > 1) {
                $(this).closest('.field-config-item').remove();
            }
        });

        // Update field configurations
    </script>
    @endpush
@endsection
