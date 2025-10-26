@extends('components.templates.master-layout')

@section('title', 'Template Master - Create')
@section('page-title', 'Tambah Template Master')

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

                            <!-- File TTD Digital -->
                            <div class="mb-3">
                                <label for="ttd_digital" class="form-label">File TTD Digital (Opsional)</label>
                                <input type="file" class="form-control @error('ttd_digital') is-invalid @enderror"
                                    id="ttd_digital" name="ttd_digital" accept="image/*">
                                <small class="form-text text-muted">Upload file gambar TTD digital (.png, .jpg, .jpeg). Gunakan variable ${ttd_digital} di template.</small>
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
                                                        value="{{ $field }}" id="field_{{ str_replace('.', '_', $field) }}">
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
                                                        value="{{ $field }}" id="field_{{ str_replace('.', '_', $field) }}">
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
                                        <div class="custom-variable-row border rounded p-3 mb-3">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="form-label">Nama Variable</label>
                                                    <input type="text" name="custom_variables[0][name]" class="form-control"
                                                        placeholder="nama_variable">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Label</label>
                                                    <input type="text" name="custom_variables[0][label]" class="form-control"
                                                        placeholder="Label Variable">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Tipe</label>
                                                    <select name="custom_variables[0][type]" class="form-control">
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
                                                    <input type="text" name="custom_variables[0][options]" class="form-control"
                                                        placeholder="option1,option2,option3">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Required</label>
                                                    <select name="custom_variables[0][required]" class="form-control">
                                                        <option value="0">Tidak</option>
                                                        <option value="1">Ya</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-custom-variable">
                                        <i class="fas fa-plus"></i> Tambah Variable Custom
                                    </button>
                                </div>

                            <!-- Hidden input untuk menyimpan semua variables -->
                            <input type="hidden" name="variables" id="variables-input">
                        </div>

                        <!-- Dynamic Field Configuration -->
                        <div class="mb-4">
                            <label class="form-label">Konfigurasi Field Dinamis</label>
                            <small class="form-text text-muted mb-2">
                                Konfigurasi field yang akan ditampilkan di form asesi (text, checkbox, radio, select, textarea)
                            </small>
                            <div id="field-configurations-container">
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
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add-field-config">
                                <i class="fas fa-plus"></i> Tambah Field
                            </button>
                            <input type="hidden" id="field_configurations" name="field_configurations">
                            <input type="hidden" id="field_mappings" name="field_mappings">
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
                                </ul>
                            </div>
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
            $('#add-custom-variable').on('click', function() {
                addCustomVariableRow(customVariableIndex);
                customVariableIndex++;
            });

            // Handle custom variable removal
            $(document).on('click', '.remove-custom-variable', function() {
                if ($('.custom-variable-row').length > 1) {
                    $(this).closest('.custom-variable-row').remove();
                    updateCustomVariables();
                    updateVariablesInput();
                }
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
                    <div class="custom-variable-row border rounded p-3 mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Nama Variable</label>
                                <input type="text" name="custom_variables[${index}][name]" class="form-control"
                                    placeholder="nama_variable">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Label</label>
                                <input type="text" name="custom_variables[${index}][label]" class="form-control"
                                    placeholder="Label Variable">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tipe</label>
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
                                    placeholder="option1,option2,option3">
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
                updateFieldConfigurations();
            }
        });

        // Update field configurations
        function updateFieldConfigurations() {
            const configurations = [];
            const mappings = {};

            $('.field-config-item').each(function() {
                const name = $(this).find('.field-name').val();
                const label = $(this).find('.field-label').val();
                const type = $(this).find('.field-type').val();
                const options = $(this).find('.field-options').val();
                const required = $(this).find('.field-required').val() === '1';
                const mapping = $(this).find('.field-mapping').val();

                if (name && label) {
                    const config = {
                        name: name,
                        label: label,
                        type: type,
                        required: required
                    };

                    if (options && ['checkbox', 'radio', 'select'].includes(type)) {
                        config.options = options.split(',').map(opt => opt.trim());
                    }

                    configurations.push(config);

                    if (mapping) {
                        mappings[name] = mapping;
                    }
                }
            });

            $('#field_configurations').val(JSON.stringify(configurations));
            $('#field_mappings').val(JSON.stringify(mappings));
        }

        // Update field configurations on change
        $(document).on('change input', '.field-config-item input, .field-config-item select', function() {
            updateFieldConfigurations();
        });

        // Initialize field configurations
        updateFieldConfigurations();
    </script>
    @endpush
@endsection
