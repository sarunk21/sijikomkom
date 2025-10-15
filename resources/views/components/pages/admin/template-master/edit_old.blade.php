@extends('components.templates.master-layout')

@section('title', 'Template Master - Edit')
@section('page-title', 'Edit Template Master')

@section('content')

    <a href="{{ route('admin.template-master.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali</span>
    </a>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.template-master.update', $template->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_template" class="form-label">Nama Template <span class="text-danger">*</span></label>
                            <input type="text" id="nama_template" name="nama_template"
                                class="form-control @error('nama_template') is-invalid @enderror"
                                placeholder="Isi nama template di sini..."
                                value="{{ old('nama_template', $template->nama_template) }}" required>
                            @error('nama_template')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tipe_template" class="form-label">Tipe Template <span class="text-danger">*</span></label>
                            <select name="tipe_template" id="tipe_template"
                                class="form-control @error('tipe_template') is-invalid @enderror" required>
                                <option value="" disabled>Pilih Tipe Template...</option>
                                @foreach($tipeTemplateOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('tipe_template', $template->tipe_template) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipe_template')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="skema_id" class="form-label">Skema <span class="text-danger">*</span></label>
                            <select name="skema_id" id="skema_id"
                                class="form-control @error('skema_id') is-invalid @enderror" required>
                                <option value="" disabled>Pilih Skema...</option>
                                @foreach($skemas as $skema)
                                    <option value="{{ $skema->id }}" {{ old('skema_id', $template->skema_id) == $skema->id ? 'selected' : '' }}>
                                        {{ $skema->nama }} ({{ $skema->kode }})
                                    </option>
                                @endforeach
                            </select>
                            @error('skema_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi"
                                class="form-control @error('deskripsi') is-invalid @enderror"
                                placeholder="Deskripsi template (opsional)...">{{ old('deskripsi', $template->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="template_file" class="form-label">File Template (.docx)</label>
                            <input type="file" id="template_file" name="template_file"
                                class="form-control @error('template_file') is-invalid @enderror"
                                accept=".docx">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti file template. Format: .docx, Maksimal 10MB</small>
                            @if($template->file_path)
                                <div class="mt-2">
                                    <a href="{{ route('admin.template-master.download', $template->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download"></i> Download Template Saat Ini
                                    </a>
                                </div>
                            @endif
                            @error('template_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="ttd_file" class="form-label">File TTD Digital</label>
                            <input type="file" id="ttd_file" name="ttd_file"
                                class="form-control @error('ttd_file') is-invalid @enderror"
                                accept=".png,.jpg,.jpeg">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti TTD. Format: PNG, JPG, JPEG. Maksimal 2MB</small>
                            @if($template->ttd_path)
                                <div class="mt-2">
                                    <img src="{{ $template->ttd_url }}" alt="TTD" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                            @error('ttd_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Variables Section -->
                <div class="mb-4">
                    <label class="form-label">Variables Template <span class="text-danger">*</span></label>
                    <p class="small text-muted">Definisikan variable yang bisa diubah dalam template. Gunakan format @{{variable}} dalam file .docx</p>

                    <div id="variables-container">
                        @if(old('variables'))
                            @foreach(old('variables') as $index => $variable)
                                <div class="input-group mb-2 variable-row">
                                    <input type="text" name="variables[]"
                                        class="form-control @error('variables.' . $index) is-invalid @enderror"
                                        value="{{ $variable }}"
                                        placeholder="Nama variable (contoh: nama_asesi)">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-danger remove-variable">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @error('variables.' . $index)
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        @elseif($template->variables)
                            @foreach($template->variables as $index => $variable)
                                <div class="input-group mb-2 variable-row">
                                    <input type="text" name="variables[]"
                                        class="form-control"
                                        value="{{ $variable }}"
                                        placeholder="Nama variable (contoh: nama_asesi)">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-danger remove-variable">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="input-group mb-2 variable-row">
                                <input type="text" name="variables[]" class="form-control"
                                    placeholder="Nama variable (contoh: nama_asesi)">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-danger remove-variable">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <button type="button" id="add-variable" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Variable
                    </button>

                    @error('variables')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status Aktif -->
                <div class="mb-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                            {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">
                            <strong>Template Aktif</strong>
                            <br><small class="text-muted">Template yang aktif dapat digunakan untuk generate dokumen</small>
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.template-master.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
                    <button type="submit" class="btn btn-orange">
                        <i class="fas fa-save"></i> Update Template
                    </button>
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
    </style>

    {{-- Script untuk manage variables --}}
    @push('scripts')
    <script>
        $(document).ready(function() {
            // Tambah variable
            $('#add-variable').on('click', function() {
                const variableRow = `
                    <div class="input-group mb-2 variable-row">
                        <input type="text" name="variables[]" class="form-control"
                            placeholder="Nama variable (contoh: nama_asesi)">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-danger remove-variable">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#variables-container').append(variableRow);
            });

            // Hapus variable
            $(document).on('click', '.remove-variable', function() {
                $(this).closest('.variable-row').remove();
            });

            // Minimal 1 variable
            $('form').on('submit', function() {
                if ($('.variable-row').length === 0) {
                    alert('Minimal harus ada 1 variable.');
                    return false;
                }
            });
        });
    </script>
    @endpush

@endsection
