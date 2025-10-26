@extends('components.templates.master-layout')

@section('title', 'Edit Template APL2')
@section('page-title', 'Edit Template APL2')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Template APL2</h6>
                </div>
                <div class="card-body">
                    <!-- Petunjuk Penggunaan Template APL2 -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Petunjuk Penggunaan Template APL2</h6>
                        <p class="mb-2">Template APL2 digunakan untuk membuat dokumen portofolio asesi. Berikut cara menggunakannya:</p>
                        <ul class="mb-2">
                            <li>Upload file template Word (.docx) yang sudah disiapkan</li>
                            <li>Konfigurasi custom variables sesuai kebutuhan</li>
                            <li>Template akan otomatis mengganti variable dengan data asesi</li>
                        </ul>
                        <small>
                            <strong>Download Sample:</strong>
                            <a href="{{ asset('storage/samples/template_apl2_sample.docx') }}" class="text-primary" download>Template APL2 Sample.docx</a>
                        </small>
                    </div>

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
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Template APL2</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.apl-2.template.update', $template->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_template">Nama Template <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_template') is-invalid @enderror"
                                   id="nama_template" name="nama_template"
                                   value="{{ old('nama_template', $template->nama_template) }}" required>
                            @error('nama_template')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="skema_id">Skema Sertifikasi <span class="text-danger">*</span></label>
                            <select class="form-control @error('skema_id') is-invalid @enderror"
                                    id="skema_id" name="skema_id" required>
                                <option value="">Pilih Skema</option>
                                @foreach($allSkema as $s)
                                    <option value="{{ $s->id }}"
                                            {{ old('skema_id', $template->skema_id) == $s->id ? 'selected' : '' }}>
                                        {{ $s->nama }}
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
                    <label for="deskripsi">Deskripsi Template</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                              id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $template->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="file_template">File Template Word (.docx)</label>
                            <input type="file" class="form-control @error('file_template') is-invalid @enderror"
                                   id="file_template" name="file_template" accept=".docx">
                            @error('file_template')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($template->file_path)
                                <small class="form-text text-muted">
                                    File saat ini: <a href="{{ asset('storage/' . $template->file_path) }}" target="_blank">Download</a>
                                </small>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ttd_digital">File TTD Digital (.png, .jpg, .jpeg)</label>
                            <input type="file" class="form-control @error('ttd_digital') is-invalid @enderror"
                                   id="ttd_digital" name="ttd_digital" accept=".png,.jpg,.jpeg">
                            @error('ttd_digital')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($template->ttd_path)
                                <small class="form-text text-muted">
                                    TTD saat ini: <a href="{{ asset('storage/' . $template->ttd_path) }}" target="_blank">Download</a>
                                </small>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Custom Variables Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-cogs me-2"></i>Custom Variables (Pertanyaan APL2)
                        </h6>
                        <small class="text-muted">Konfigurasi pertanyaan yang akan ditampilkan di form APL2 asesi</small>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h6><i class="fas fa-lightbulb me-2"></i>Contoh Penggunaan:</h6>
                            <ul class="mb-2">
                                <li><strong>Pertanyaan BK/K:</strong> Name: <code>pertanyaan_bk_k_1</code>, Type: <code>radio</code>, Options: <code>BK,K</code></li>
                                <li><strong>Pertanyaan Text:</strong> Name: <code>pengalaman_kerja</code>, Type: <code>textarea</code></li>
                                <li><strong>Upload File:</strong> Name: <code>bukti_kompetensi</code>, Type: <code>file</code></li>
                            </ul>
                            <small><strong>Tips:</strong> Gunakan nama variable yang jelas dan konsisten untuk memudahkan maintenance.</small>
                        </div>

                        <div id="custom-variables-container">
                            @if(old('custom_variables'))
                                @foreach(old('custom_variables') as $index => $variable)
                                    <div class="custom-variable-row border p-3 mb-3">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Name (Variable)</label>
                                                <input type="text" class="form-control" name="custom_variables[{{ $index }}][name]"
                                                       value="{{ $variable['name'] ?? '' }}" placeholder="pertanyaan_bk_k_1" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Label (Pertanyaan)</label>
                                                <input type="text" class="form-control" name="custom_variables[{{ $index }}][label]"
                                                       value="{{ $variable['label'] ?? '' }}" placeholder="Apakah Anda mampu mengaplikasikan keterampilan dasar komunikasi?" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Type</label>
                                                <select class="form-control" name="custom_variables[{{ $index }}][type]" required>
                                                    <option value="text" {{ ($variable['type'] ?? '') == 'text' ? 'selected' : '' }}>Text</option>
                                                    <option value="textarea" {{ ($variable['type'] ?? '') == 'textarea' ? 'selected' : '' }}>Textarea</option>
                                                    <option value="radio" {{ ($variable['type'] ?? '') == 'radio' ? 'selected' : '' }}>Radio</option>
                                                    <option value="checkbox" {{ ($variable['type'] ?? '') == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                                    <option value="select" {{ ($variable['type'] ?? '') == 'select' ? 'selected' : '' }}>Select</option>
                                                    <option value="number" {{ ($variable['type'] ?? '') == 'number' ? 'selected' : '' }}>Number</option>
                                                    <option value="email" {{ ($variable['type'] ?? '') == 'email' ? 'selected' : '' }}>Email</option>
                                                    <option value="date" {{ ($variable['type'] ?? '') == 'date' ? 'selected' : '' }}>Date</option>
                                                    <option value="file" {{ ($variable['type'] ?? '') == 'file' ? 'selected' : '' }}>File</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-danger btn-block remove-custom-variable">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <label>Options (untuk radio, checkbox, select)</label>
                                                <input type="text" class="form-control" name="custom_variables[{{ $index }}][options]"
                                                       value="{{ $variable['options'] ?? '' }}" placeholder="BK,K (pisahkan dengan koma)">
                                                <small class="form-text text-muted">Contoh: BK,K atau Ya,Tidak</small>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mt-4">
                                                    <input type="checkbox" class="form-check-input" name="custom_variables[{{ $index }}][required]"
                                                           value="1" {{ ($variable['required'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label">Required</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif($template->custom_variables && count($template->custom_variables) > 0)
                                @foreach($template->custom_variables as $index => $variable)
                                    <div class="custom-variable-row border p-3 mb-3">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Name (Variable)</label>
                                                <input type="text" class="form-control" name="custom_variables[{{ $index }}][name]"
                                                       value="{{ $variable['name'] ?? '' }}" placeholder="pertanyaan_bk_k_1" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Label (Pertanyaan)</label>
                                                <input type="text" class="form-control" name="custom_variables[{{ $index }}][label]"
                                                       value="{{ $variable['label'] ?? '' }}" placeholder="Apakah Anda mampu mengaplikasikan keterampilan dasar komunikasi?" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Type</label>
                                                <select class="form-control" name="custom_variables[{{ $index }}][type]" required>
                                                    <option value="text" {{ ($variable['type'] ?? '') == 'text' ? 'selected' : '' }}>Text</option>
                                                    <option value="textarea" {{ ($variable['type'] ?? '') == 'textarea' ? 'selected' : '' }}>Textarea</option>
                                                    <option value="radio" {{ ($variable['type'] ?? '') == 'radio' ? 'selected' : '' }}>Radio</option>
                                                    <option value="checkbox" {{ ($variable['type'] ?? '') == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                                    <option value="select" {{ ($variable['type'] ?? '') == 'select' ? 'selected' : '' }}>Select</option>
                                                    <option value="number" {{ ($variable['type'] ?? '') == 'number' ? 'selected' : '' }}>Number</option>
                                                    <option value="email" {{ ($variable['type'] ?? '') == 'email' ? 'selected' : '' }}>Email</option>
                                                    <option value="date" {{ ($variable['type'] ?? '') == 'date' ? 'selected' : '' }}>Date</option>
                                                    <option value="file" {{ ($variable['type'] ?? '') == 'file' ? 'selected' : '' }}>File</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-danger btn-block remove-custom-variable">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <label>Options (untuk radio, checkbox, select)</label>
                                                <input type="text" class="form-control" name="custom_variables[{{ $index }}][options]"
                                                       value="{{ $variable['options'] ?? '' }}" placeholder="BK,K (pisahkan dengan koma)">
                                                <small class="form-text text-muted">Contoh: BK,K atau Ya,Tidak</small>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mt-4">
                                                    <input type="checkbox" class="form-check-input" name="custom_variables[{{ $index }}][required]"
                                                           value="1" {{ ($variable['required'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label">Required</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <button type="button" class="btn btn-success" id="add-custom-variable">
                            <i class="fas fa-plus"></i> Tambah Custom Variable
                        </button>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                               value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Template Aktif
                        </label>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Template APL2
                    </button>
                    <a href="{{ route('admin.apl-2.template.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let customVariableIndex = {{ old('custom_variables') ? count(old('custom_variables')) : ($template->custom_variables ? count($template->custom_variables) : 0) }};

    // Add custom variable
    $('#add-custom-variable').on('click', function() {
        const html = `
            <div class="custom-variable-row border p-3 mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <label>Name (Variable)</label>
                        <input type="text" class="form-control" name="custom_variables[${customVariableIndex}][name]"
                               placeholder="pertanyaan_bk_k_1" required>
                    </div>
                    <div class="col-md-4">
                        <label>Label (Pertanyaan)</label>
                        <input type="text" class="form-control" name="custom_variables[${customVariableIndex}][label]"
                               placeholder="Apakah Anda mampu mengaplikasikan keterampilan dasar komunikasi?" required>
                    </div>
                    <div class="col-md-3">
                        <label>Type</label>
                        <select class="form-control" name="custom_variables[${customVariableIndex}][type]" required>
                            <option value="text">Text</option>
                            <option value="textarea">Textarea</option>
                            <option value="radio">Radio</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="select">Select</option>
                            <option value="number">Number</option>
                            <option value="email">Email</option>
                            <option value="date">Date</option>
                            <option value="file">File</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block remove-custom-variable">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Options (untuk radio, checkbox, select)</label>
                        <input type="text" class="form-control" name="custom_variables[${customVariableIndex}][options]"
                               placeholder="BK,K (pisahkan dengan koma)">
                        <small class="form-text text-muted">Contoh: BK,K atau Ya,Tidak</small>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" name="custom_variables[${customVariableIndex}][required]" value="1">
                            <label class="form-check-label">Required</label>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#custom-variables-container').append(html);
        customVariableIndex++;
    });

    // Remove custom variable
    $(document).on('click', '.remove-custom-variable', function() {
        $(this).closest('.custom-variable-row').remove();
    });

    // Auto-fill options for radio type
    $(document).on('change', 'select[name*="[type]"]', function() {
        const type = $(this).val();
        const optionsInput = $(this).closest('.custom-variable-row').find('input[name*="[options]"]');

        if (type === 'radio') {
            optionsInput.attr('placeholder', 'BK,K (pisahkan dengan koma)');
        } else if (type === 'checkbox') {
            optionsInput.attr('placeholder', 'Ya,Tidak (pisahkan dengan koma)');
        } else if (type === 'select') {
            optionsInput.attr('placeholder', 'Opsi1,Opsi2,Opsi3 (pisahkan dengan koma)');
        } else {
            optionsInput.attr('placeholder', '');
        }
    });
});
</script>
@endpush
