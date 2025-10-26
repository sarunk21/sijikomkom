@extends('components.templates.master-layout')

@section('title', 'APL02 - Create')
@section('page-title', 'Tambah APL02')

@section('content')

    <a href="{{ route('admin.apl-2.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali</span>
    </a>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Informasi Skema Sertifikasi -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Detail Skema Sertifikasi yang Dipilih</h5>
            <p class="card-text font-weight-bold text-primary" style="font-size: 1.2rem;">
                {{ $skema->nama }}
            </p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.apl-2.store') }}" method="POST">
                @csrf

                <input type="hidden" name="skema_id" value="{{ $skema->id }}">

                <!-- Variables Section -->
                <div class="mb-4">
                    <label class="form-label">Variables Template <span class="text-danger">*</span></label>
                    <p class="small text-muted">Pilih field dari database atau buat variable custom. Gunakan format ${variable} dalam template APL2</p>

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
                                            {{ $availableFields[$field] ?? $field }}
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
                                            {{ $availableFields[$field] ?? $field }}
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

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.apl-2.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
                    <button type="submit" class="btn btn-orange">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .text-orange {
            color: #f25c05;
        }

        .btn-orange {
            background-color: #f25c05;
            color: white;
        }

        .btn-orange:hover {
            background-color: #d94f04;
            color: white;
        }

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
            let customVariableIndex = 1;

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
    </script>
    @endpush

@endsection
