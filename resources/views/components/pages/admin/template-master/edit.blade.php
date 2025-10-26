@extends('components.templates.master-layout')

@section('title', 'Template Master - Edit')
@section('page-title', 'Edit Template Master')

@section('content')

    <a href="{{ route('admin.template-master.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali</span>
    </a>

    <div class="container-fluid">
        <!-- Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Edit Template Master</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.template-master.update', $template->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Nama Template -->
                            <div class="mb-3">
                                <label for="nama_template" class="form-label">Nama Template <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_template') is-invalid @enderror"
                                    id="nama_template" name="nama_template" value="{{ old('nama_template', $template->nama_template) }}" required>
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
                                        <option value="{{ $key }}" {{ old('tipe_template', $template->tipe_template) == $key ? 'selected' : '' }}>
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
                                        <option value="{{ $skema->id }}" {{ old('skema_id', $template->skema_id) == $skema->id ? 'selected' : '' }}>
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
                                    id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $template->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- File Template Saat Ini -->
                            <div class="mb-3">
                                <label class="form-label">File Template Saat Ini</label>
                                <div class="alert alert-info">
                                    <i class="fas fa-file-word"></i>
                                    <a href="{{ route('admin.template-master.download', $template->id) }}" target="_blank">
                                        {{ basename($template->file_path) }}
                                    </a>
                                    <small class="text-muted">(Klik untuk download)</small>
                                </div>
                            </div>

                            <!-- File Template Baru -->
                            <div class="mb-3">
                                <label for="file_template" class="form-label">File Template Baru (.docx) <small class="text-muted">(Opsional - kosongkan jika tidak ingin mengganti)</small></label>
                                <input type="file" class="form-control @error('file_template') is-invalid @enderror"
                                    id="file_template" name="file_template" accept=".docx">
                                <small class="form-text text-muted">Upload file template baru dalam format .docx. Gunakan format ${variable} untuk variable.</small>
                                @error('file_template')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- File TTD Digital Saat Ini -->
                            @if($template->ttd_path)
                            <div class="mb-3">
                                <label class="form-label">File TTD Digital Saat Ini</label>
                                <div class="alert alert-info">
                                    <i class="fas fa-image"></i>
                                    <a href="{{ $template->ttd_url }}" target="_blank">
                                        {{ basename($template->ttd_path) }}
                                    </a>
                                    <small class="text-muted">(Klik untuk lihat)</small>
                                </div>
                            </div>
                            @endif

                            <!-- File TTD Digital Baru -->
                            <div class="mb-3">
                                <label for="ttd_digital" class="form-label">File TTD Digital Baru <small class="text-muted">(Opsional)</small></label>
                                <input type="file" class="form-control @error('ttd_digital') is-invalid @enderror"
                                    id="ttd_digital" name="ttd_digital" accept="image/*">
                                <small class="form-text text-muted">Upload file gambar TTD digital baru (.png, .jpg, .jpeg). Gunakan variable ${ttd_digital} di template.</small>
                                @error('ttd_digital')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Variables Section -->
                            <div class="mb-4">
                                <label class="form-label">Variables Template <span class="text-danger">*</span></label>
                                <p class="small text-muted">Pilih field dari database atau buat variable custom. Gunakan format ${variable} dalam file .docx</p>

                                <!-- Available Database Fields -->
                                <div class="mb-3">
                                    <h6>Field Database yang Tersedia:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Data User/Asesi</h6>
                                            @foreach(['user.name', 'user.email', 'user.telephone', 'user.alamat', 'user.nik', 'user.nim'] as $field)
                                                <div class="form-check">
                                                    <input class="form-check-input database-field" type="checkbox"
                                                        value="{{ $field }}" id="field_{{ str_replace('.', '_', $field) }}"
                                                        {{ is_array(old('variables', $template->variables ?? [])) && in_array($field, old('variables', $template->variables ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="field_{{ str_replace('.', '_', $field) }}">
                                                        {{ $availableFields[$field] }}
                                                        <small class="text-muted d-block">Format: <code>${ {{ $field }} }</code></small>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-success">Data Skema & Jadwal</h6>
                                            @foreach(['skema.nama', 'skema.kode', 'skema.bidang', 'jadwal.tanggal_ujian', 'jadwal.waktu_mulai', 'jadwal.tuk.nama'] as $field)
                                                <div class="form-check">
                                                    <input class="form-check-input database-field" type="checkbox"
                                                        value="{{ $field }}" id="field_{{ str_replace('.', '_', $field) }}"
                                                        {{ is_array(old('variables', $template->variables ?? [])) && in_array($field, old('variables', $template->variables ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="field_{{ str_replace('.', '_', $field) }}">
                                                        {{ $availableFields[$field] }}
                                                        <small class="text-muted d-block">Format: <code>${ {{ $field }} }</code></small>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Selected Variables -->
                                <div class="mb-3">
                                    <h6>Variable yang Dipilih:</h6>
                                    <div id="selected-variables-container">
                                        <!-- Variables akan ditambahkan di sini via JavaScript -->
                                    </div>
                                </div>

                                <!-- Custom Variables -->
                                <div class="mb-3">
                                    <h6>Variable Custom:</h6>
                                    <div id="custom-variables-container">
                                        <!-- Custom variables akan ditambahkan di sini via JavaScript -->
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-custom-variable">
                                        <i class="fas fa-plus"></i> Tambah Variable Custom
                                    </button>
                                </div>

                                <!-- APL2 Configuration Section -->
                                <div id="apl2-config-section" class="mb-4" style="display: none;">
                                    <h5 class="text-primary mb-3">Konfigurasi APL2</h5>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Untuk Template APL2:</strong> Gunakan Custom Variables di atas untuk membuat pertanyaan.
                                        Setiap custom variable akan menjadi pertanyaan di form asesi.
                                        <br><br>
                                        <strong>Tips:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Untuk pertanyaan BK/K: Gunakan type "radio" dengan options "BK,K"</li>
                                            <li>Untuk pertanyaan biasa: Gunakan type "text" atau "textarea"</li>
                                            <li>Untuk pertanyaan dengan bukti: Gunakan type "file" atau tambahkan field bukti terpisah</li>
                                            <li>Jika pertanyaan lebih dari 2, akan dipisah ke kotak terpisah di hasil generate</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Hidden input untuk menyimpan semua variables -->
                                <input type="hidden" name="variables" id="variables-input" value="{{ old('variables', json_encode($template->variables ?? [])) }}">
                            </div>

                            <!-- Status Aktif -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                        {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Template Aktif
                                    </label>
                                </div>
                                <small class="form-text text-muted">Template yang aktif dapat digunakan untuk generate dokumen</small>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Template
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

            // Initialize with existing data
            @if($template->variables)
                @php
                    $variables = is_string($template->variables) ? json_decode($template->variables, true) : $template->variables;
                    $variables = is_array($variables) ? $variables : [];
                @endphp
                @foreach($variables as $variable)
                    @if(strpos($variable, '.') !== false)
                        // Database field
                        selectedVariables.push('{{ $variable }}');
                        $(`input[value="{{ $variable }}"]`).prop('checked', true);
                    @else
                        // Custom variable
                        customVariables.push('{{ $variable }}');
                    @endif
                @endforeach
            @endif

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

            // Handle template type change
            $('#tipe_template').on('change', function() {
                const selectedType = $(this).val();
                if (selectedType === 'APL2') {
                    $('#apl2-config-section').show();
                    // Limit custom variable types for APL2
                    limitCustomVariableTypes('APL2');
                } else if (selectedType === 'APL1') {
                    $('#apl2-config-section').hide();
                    // Limit custom variable types for APL1
                    limitCustomVariableTypes('APL1');
                } else {
                    $('#apl2-config-section').hide();
                    // Allow all types for other templates
                    limitCustomVariableTypes('ALL');
                }
            });

            // Initialize APL2 section visibility and type limits
            const currentType = $('#tipe_template').val();
            if (currentType === 'APL2') {
                $('#apl2-config-section').show();
                limitCustomVariableTypes('APL2');
            } else if (currentType === 'APL1') {
                limitCustomVariableTypes('APL1');
            } else {
                limitCustomVariableTypes('ALL');
            }

            // Handle custom variable addition
            $('#add-custom-variable').on('click', function() {
                addCustomVariableRow(customVariableIndex);
                customVariableIndex++;
            });

            // Handle custom variable removal
            $(document).on('click', '.remove-custom-variable', function() {
                $(this).closest('.custom-variable-row').remove();
                updateCustomVariables();
                updateVariablesInput();
            });

            // Handle custom variable input change
            $(document).on('input change', '.custom-variable-row input, .custom-variable-row select', function() {
                updateCustomVariables();
                updateVariablesInput();
            });

            function limitCustomVariableTypes(templateType) {
                $('.custom-variable-type').each(function() {
                    const $select = $(this);
                    const currentValue = $select.val();

                    // Clear existing options
                    $select.empty();

                    if (templateType === 'APL1') {
                        // APL1 hanya boleh text dan radio
                        $select.append('<option value="text">Text</option>');
                        $select.append('<option value="radio">Radio</option>');
                    } else if (templateType === 'APL2') {
                        // APL2 boleh semua tipe
                        $select.append('<option value="text">Text</option>');
                        $select.append('<option value="textarea">Textarea</option>');
                        $select.append('<option value="checkbox">Checkbox</option>');
                        $select.append('<option value="radio">Radio</option>');
                        $select.append('<option value="select">Select</option>');
                        $select.append('<option value="number">Number</option>');
                        $select.append('<option value="email">Email</option>');
                        $select.append('<option value="date">Date</option>');
                        $select.append('<option value="file">File Upload</option>');
                    } else {
                        // Template lain boleh semua tipe
                        $select.append('<option value="text">Text</option>');
                        $select.append('<option value="textarea">Textarea</option>');
                        $select.append('<option value="checkbox">Checkbox</option>');
                        $select.append('<option value="radio">Radio</option>');
                        $select.append('<option value="select">Select</option>');
                        $select.append('<option value="number">Number</option>');
                        $select.append('<option value="email">Email</option>');
                        $select.append('<option value="date">Date</option>');
                        $select.append('<option value="file">File Upload</option>');
                    }

                    // Restore previous value if still valid
                    if (currentValue && $select.find(`option[value="${currentValue}"]`).length > 0) {
                        $select.val(currentValue);
                    } else {
                        $select.val('text'); // Default to text
                    }
                });
            }

            function addCustomVariableRow(index, existingData = null) {
                const templateType = $('#tipe_template').val();

                let typeOptions = '';
                if (templateType === 'APL1') {
                    typeOptions = `
                        <option value="text">Text</option>
                        <option value="radio">Radio</option>
                    `;
                } else {
                    typeOptions = `
                        <option value="text">Text</option>
                        <option value="textarea">Textarea</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="radio">Radio</option>
                        <option value="select">Select</option>
                        <option value="number">Number</option>
                        <option value="email">Email</option>
                        <option value="date">Date</option>
                        <option value="file">File Upload</option>
                    `;
                }

                const html = `
                    <div class="custom-variable-row border rounded p-3 mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Nama Variable</label>
                                <input type="text" name="custom_variables[${index}][name]" class="form-control"
                                    placeholder="nama_variable" value="${existingData ? existingData.name || '' : ''}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Label</label>
                                <input type="text" name="custom_variables[${index}][label]" class="form-control"
                                    placeholder="Label Variable" value="${existingData ? existingData.label || '' : ''}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tipe</label>
                                <select name="custom_variables[${index}][type]" class="form-control custom-variable-type">
                                    ${typeOptions}
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Aksi</label>
                                <button type="button" class="btn btn-outline-danger btn-sm remove-custom-variable">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Options (untuk checkbox/radio/select)</label>
                                <input type="text" name="custom_variables[${index}][options]" class="form-control"
                                    placeholder="option1,option2,option3" value="${existingData ? existingData.options || '' : ''}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Required</label>
                                <select name="custom_variables[${index}][required]" class="form-control">
                                    <option value="0">Tidak</option>
                                    <option value="1">Ya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;
                $('#custom-variables-container').append(html);

                // Set values for existing data
                if (existingData) {
                    const $row = $('.custom-variable-row').last();
                    $row.find('select[name*="[type]"]').val(existingData.type || 'text');
                    $row.find('select[name*="[required]"]').val(existingData.required ? '1' : '0');
                }
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
                $('.custom-variable-row').each(function() {
                    const name = $(this).find('input[name*="[name]"]').val().trim();
                    const label = $(this).find('input[name*="[label]"]').val().trim();
                    const type = $(this).find('select[name*="[type]"]').val();
                    const options = $(this).find('input[name*="[options]"]').val().trim();
                    const required = $(this).find('select[name*="[required]"]').val();

                    if (name && label) {
                        const variable = {
                            name: name,
                            label: label,
                            type: type,
                            required: required === '1'
                        };

                        if (options && ['checkbox', 'radio', 'select'].includes(type)) {
                            variable.options = options.split(',').map(opt => opt.trim());
                        }

                        customVariables.push(variable);
                    }
                });
            }

            function updateVariablesInput() {
                // Filter out empty values
                const filteredSelectedVariables = selectedVariables.filter(v => v && v.trim() !== '');
                const filteredCustomVariables = customVariables.map(v => v.name).filter(v => v && v.trim() !== '');
                const allVariables = [...filteredSelectedVariables, ...filteredCustomVariables];
                $('#variables-input').val(JSON.stringify(allVariables));
            }

            // Initialize with existing custom variables
            @if($template->custom_variables)
                @php
                    $customVariables = is_string($template->custom_variables) ? json_decode($template->custom_variables, true) : $template->custom_variables;
                    $customVariables = is_array($customVariables) ? $customVariables : [];
                @endphp
                @foreach($customVariables as $index => $variable)
                    addCustomVariableRow({{ $index }}, @json($variable));
                    customVariableIndex = {{ $index + 1 }};
                @endforeach
            @endif

            // Initialize displays
            updateSelectedVariablesDisplay();
            updateCustomVariables();
            updateVariablesInput();
        });
    </script>
    @endpush
@endsection
