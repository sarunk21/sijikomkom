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
                                        <div class="input-group mb-2 custom-variable-row">
                                            <input type="text" name="custom_variables[]" class="form-control"
                                                placeholder="Nama variable custom (contoh: nama_perusahaan)">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-danger remove-custom-variable">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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
                addCustomVariableRow('');
            });

            // Handle custom variable removal
            $(document).on('click', '.remove-custom-variable', function() {
                $(this).closest('.custom-variable-row').remove();
                updateCustomVariables();
                updateVariablesInput();
            });

            // Handle custom variable input change
            $(document).on('input', 'input[name="custom_variables[]"]', function() {
                updateCustomVariables();
                updateVariablesInput();
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

            function addCustomVariableRow(value = '') {
                const html = `
                    <div class="input-group mb-2 custom-variable-row">
                        <input type="text" name="custom_variables[]" class="form-control"
                            value="${value}" placeholder="Nama variable custom (contoh: nama_perusahaan)">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-danger remove-custom-variable">
                                <i class="fas fa-trash"></i>
                            </button>
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
                $('input[name="custom_variables[]"]').each(function() {
                    const value = $(this).val().trim();
                    if (value) {
                        customVariables.push(value);
                    }
                });
            }

            function updateVariablesInput() {
                // Filter out empty values
                const filteredSelectedVariables = selectedVariables.filter(v => v && v.trim() !== '');
                const filteredCustomVariables = customVariables.filter(v => v && v.trim() !== '');
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
    </script>
    @endpush
@endsection
