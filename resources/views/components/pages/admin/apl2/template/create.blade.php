@extends('components.templates.master-layout')

@section('title', 'Tambah Template Bank Soal')
@section('page-title', 'Tambah Template Bank Soal')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Template Bank Soal</h1>
        <a href="{{ route('admin.apl-2.template.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Petunjuk Penggunaan --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-info-circle me-2"></i>Petunjuk Penggunaan Template Bank Soal
            </h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <h5><i class="fas fa-lightbulb me-2"></i>Cara Menggunakan Template Bank Soal:</h5>
                <ol>
                    <li><strong>Upload Template Word:</strong> Upload file dokumen Word (.docx) yang akan digunakan sebagai template</li>
                    <li><strong>Gunakan Custom Variables:</strong> Di dokumen Word, gunakan variable seperti <code>{nama_lengkap}</code>, <code>{email}</code>, dll</li>
                    <li><strong>Buat Pertanyaan Bank Soal:</strong> Gunakan Custom Variables untuk membuat pertanyaan yang akan ditampilkan di form asesi</li>
                    <li><strong>Variable BK/K:</strong> Untuk pertanyaan kompetensi, gunakan type "radio" dengan options "BK,K"</li>
                    <li><strong>Upload TTD Digital:</strong> Upload file gambar TTD yang akan digunakan untuk tanda tangan digital</li>
                </ol>

                <h6 class="mt-3"><i class="fas fa-download me-2"></i>Download Sample Template:</h6>
                <a href="{{ asset('storage/samples/template_apl2_sample.docx') }}" class="btn btn-success btn-sm" target="_blank">
                    <i class="fas fa-download me-1"></i>Download Sample Template Bank Soal
                </a>
                <small class="text-muted d-block mt-1">
                    <i class="fas fa-info-circle me-1"></i>
                    Sample ini berisi contoh penggunaan variable seperti ${nama_lengkap}, ${pertanyaan_bk_k_1}, dll.
                </small>

                <h6 class="mt-3"><i class="fas fa-code me-2"></i>Variable yang Tersedia:</h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Data Asesi:</strong>
                        <ul class="small">
                            <li><code>${nama_lengkap}</code> - Nama lengkap asesi</li>
                            <li><code>${email}</code> - Email asesi</li>
                            <li><code>${no_hp}</code> - Nomor HP asesi</li>
                            <li><code>${alamat}</code> - Alamat asesi</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <strong>Data Skema:</strong>
                        <ul class="small">
                            <li><code>${nama_skema}</code> - Nama skema sertifikasi</li>
                            <li><code>${kode_skema}</code> - Kode skema</li>
                            <li><code>${tanggal_sertifikasi}</code> - Tanggal sertifikasi</li>
                        </ul>
                    </div>
                </div>

                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Penting:</strong> Pastikan semua variable yang digunakan di dokumen Word sudah didefinisikan di Custom Variables atau Database Fields.
                </div>

                <div class="alert alert-light mt-3">
                    <h6><i class="fas fa-file-word me-2"></i>Cara Menggunakan Variable di Dokumen Word:</h6>
                    <ol class="mb-0">
                        <li>Buka dokumen Word yang akan digunakan sebagai template</li>
                        <li>Gunakan variable dengan format <code>${nama_variable}</code></li>
                        <li>Contoh: <code>${nama_lengkap}</code>, <code>${pertanyaan_bk_k_1}</code>, <code>${pengalaman_kerja}</code></li>
                        <li>Variable akan diganti otomatis dengan data dari form asesi saat generate dokumen</li>
                        <li>Download sample template di atas untuk melihat contoh penggunaan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Template Bank Soal</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.apl-2.template.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_template">Nama Template <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_template') is-invalid @enderror"
                                   id="nama_template" name="nama_template" value="{{ old('nama_template') }}" required>
                            @error('nama_template')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="skema_id">Skema <span class="text-danger">*</span></label>
                            <select class="form-control @error('skema_id') is-invalid @enderror" id="skema_id" name="skema_id" required>
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
                    </div>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                              id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="file_template">File Template (.docx) <span class="text-danger">*</span></label>
                            <input type="file" class="form-control-file @error('file_template') is-invalid @enderror"
                                   id="file_template" name="file_template" accept=".docx" required>
                            <small class="form-text text-muted">Maksimal 10MB</small>
                            @error('file_template')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ttd_digital">Tanda Tangan Digital</label>
                            <input type="file" class="form-control-file @error('ttd_digital') is-invalid @enderror"
                                   id="ttd_digital" name="ttd_digital" accept=".png,.jpg,.jpeg">
                            <small class="form-text text-muted">Format: PNG, JPG, JPEG. Maksimal 2MB</small>
                            @error('ttd_digital')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="variables">Variables <span class="text-danger">*</span></label>
                    <div class="mb-3">
                        <label class="form-label">Pilih Field yang Tersedia</label>
                        <div class="row">
                            @foreach($availableFields as $value => $label)
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input database-field" type="checkbox"
                                               value="{{ $value }}" id="field_{{ $loop->index }}">
                                        <label class="form-check-label" for="field_{{ $loop->index }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Field yang Dipilih</label>
                        <div id="selected-fields-container" class="border rounded p-3" style="min-height: 100px;">
                            <p class="text-muted text-center">Belum ada field yang dipilih</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Custom Variables</label>
                        <div id="custom-variables-container">
                            <!-- Custom variables akan ditambahkan di sini -->
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-custom-variable">
                            <i class="fas fa-plus"></i> Tambah Variable Custom
                        </button>
                    </div>

                    <!-- Hidden input untuk menyimpan semua variables -->
                    <input type="hidden" name="variables" id="variables-input">
                </div>

                <div class="alert alert-success">
                    <h6><i class="fas fa-lightbulb me-2"></i>Contoh Penggunaan Custom Variables untuk Bank Soal:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Pertanyaan BK/K (Kompetensi):</strong>
                            <div class="bg-light p-3 rounded mt-2">
                                <small>
                                    <strong>Name:</strong> <code>pertanyaan_bk_k_1</code><br>
                                    <strong>Label:</strong> Apakah Anda mampu mengaplikasikan keterampilan dasar komunikasi?<br>
                                    <strong>Type:</strong> radio<br>
                                    <strong>Options:</strong> BK,K<br>
                                    <strong>Required:</strong> Ya
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <strong>Pertanyaan Biasa:</strong>
                            <div class="bg-light p-3 rounded mt-2">
                                <small>
                                    <strong>Name:</strong> <code>pengalaman_kerja</code><br>
                                    <strong>Label:</strong> Jelaskan pengalaman kerja Anda<br>
                                    <strong>Type:</strong> textarea<br>
                                    <strong>Required:</strong> Ya
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <strong>Tips Penggunaan:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Untuk pertanyaan BK/K: Gunakan type "radio" dengan options "BK,K"</li>
                            <li>Untuk pertanyaan biasa: Gunakan type "text" atau "textarea"</li>
                            <li>Untuk pertanyaan dengan bukti: Gunakan type "file" atau tambahkan field bukti terpisah</li>
                            <li>Setiap custom variable akan menjadi pertanyaan di form asesi</li>
                        </ul>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Template
                    </button>
                    <a href="{{ route('admin.apl-2.template.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .selected-variable-item {
        display: inline-block;
        margin: 2px;
    }

    .remove-selected {
        cursor: pointer;
        font-weight: bold;
    }

    .remove-selected:hover {
        opacity: 0.7;
    }

    .custom-variable-row {
        background-color: #f8f9fa;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let selectedVariables = [];
    let customVariables = [];
    let customVariableIndex = 0;

    // Handle database field selection
    $('.database-field').on('change', function() {
        const fieldValue = $(this).val();
        const fieldLabel = $(this).next('label').text();

        if ($(this).is(':checked')) {
            if (!selectedVariables.includes(fieldValue)) {
                selectedVariables.push(fieldValue);
                updateSelectedFieldsDisplay();
                updateVariablesInput();
            }
        } else {
            selectedVariables = selectedVariables.filter(v => v !== fieldValue);
            updateSelectedFieldsDisplay();
            updateVariablesInput();
        }
    });

    // Handle custom variable addition
    $('#add-custom-variable').on('click', function() {
        addCustomVariableRow(customVariableIndex);
        customVariableIndex++;
    });

    // Add default BK/K question on page load
    $(document).ready(function() {
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

    // Handle custom variable type change for BK/K auto-fill
    $(document).on('change', 'select[name*="[type]"]', function() {
        const type = $(this).val();
        const row = $(this).closest('.custom-variable-row');
        const optionsInput = row.find('input[name*="[options]"]');

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

    // Handle removal of selected variables
    $(document).on('click', '.remove-selected', function() {
        const variable = $(this).data('variable');
        selectedVariables = selectedVariables.filter(v => v !== variable);
        $(`.database-field[value="${variable}"]`).prop('checked', false);
        updateSelectedFieldsDisplay();
        updateVariablesInput();
    });

    function updateSelectedFieldsDisplay() {
        const container = $('#selected-fields-container');
        container.empty();

        if (selectedVariables.length === 0) {
            container.html('<p class="text-muted text-center">Belum ada field yang dipilih</p>');
            return;
        }

        selectedVariables.forEach(variable => {
            const label = $(`.database-field[value="${variable}"]`).next('label').text();
            const html = `
                <span class="selected-variable-item badge badge-primary mr-2 mb-2" style="font-size: 0.9em;">
                    ${label}
                    <span class="remove-selected ml-2" data-variable="${variable}" style="cursor: pointer; color: white;">×</span>
                </span>
            `;
            container.append(html);
        });
    }

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
                        <div>
                            <button type="button" class="btn btn-danger btn-sm remove-custom-variable">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Options (untuk checkbox/radio/select)</label>
                        <input type="text" name="custom_variables[${index}][options]" class="form-control"
                            placeholder="option1,option2,option3">
                    </div>
                    <div class="col-md-3">
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

    // Initialize with empty array
    updateVariablesInput();
});
</script>
@endpush
