@extends('components.templates.master-layout')

@section('title', 'Skema - Edit')
@section('page-title', 'Edit Skema')

@section('content')

    <a href="{{ route('admin.skema.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
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
            <form action="{{ route('admin.skema.update', $skema->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Skema <span class="text-danger">*</span></label>
                    <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror"
                        placeholder="Isi nama skema di sini..." value="{{ $skema->nama }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="kode" class="form-label">Kode Skema <span class="text-danger">*</span></label>
                    <input type="text" id="kode" name="kode" class="form-control @error('kode') is-invalid @enderror"
                        placeholder="Isi Kode Unik di sini..." value="{{ $skema->kode }}" required>
                    @error('kode')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" id="kategori" class="form-control @error('kategori') is-invalid @enderror" required>
                        <option value="" disabled>Pilih Kategori Skema di sini...</option>
                        <option value="Sertifikasi" {{ $skema->kategori == 'Sertifikasi' ? 'selected' : '' }}>Sertifikasi</option>
                        <option value="Pelatihan" {{ $skema->kategori == 'Pelatihan' ? 'selected' : '' }}>Pelatihan</option>
                    </select>
                    @error('kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="bidang" class="form-label">Bidang <span class="text-danger">*</span></label>
                    <select name="bidang" id="bidang" class="form-control @error('bidang') is-invalid @enderror" required>
                        <option value="" disabled>Pilih Bidang Skema di sini...</option>
                        <option value="S1 Sistem Informasi" {{ $skema->bidang == 'S1 Sistem Informasi' ? 'selected' : '' }}>S1 Sistem Informasi</option>
                        <option value="S1 Teknik Informatika" {{ $skema->bidang == 'S1 Teknik Informatika' ? 'selected' : '' }}>S1 Teknik Informatika</option>
                        <option value="D3 Sistem Informasi" {{ $skema->bidang == 'D3 Sistem Informasi' ? 'selected' : '' }}>D3 Sistem Informasi</option>
                    </select>
                    @error('bidang')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Asesor</label>
                    <div class="mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select-all-asesors">
                            <label class="form-check-label text-primary font-weight-bold" for="select-all-asesors">
                                <i class="fas fa-check-square"></i> Pilih Semua Asesor
                            </label>
                        </div>
                    </div>
                    <div class="card border-light">
                        <div class="card-body py-2">
                            <div class="row">
                                @foreach ($asesors as $asesor)
                                    @php
                                        $selected = false;
                                        if (old('asesors')) {
                                            $selected = in_array($asesor->id, old('asesors'));
                                        } else {
                                            $selected = $skema->asesors->contains($asesor->id);
                                        }
                                    @endphp
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="asesors[]" value="{{ $asesor->id }}" id="asesor_{{ $asesor->id }}"
                                                {{ $selected ? 'checked' : '' }}>
                                            <label class="form-check-label" for="asesor_{{ $asesor->id }}">
                                                <strong>{{ $asesor->name }}</strong>
                                                <br><small class="text-muted">{{ $asesor->email }}</small>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <small class="form-text text-muted">Pilih satu atau lebih asesor yang bisa menguji skema ini</small>
                    @error('asesors')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.skema.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
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
    </style>

    {{-- Script untuk select all asesor --}}
    @push('scripts')
    <script>
        $(document).ready(function() {
            // Select All functionality
            $('#select-all-asesors').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('input[name="asesors[]"]').prop('checked', isChecked);
            });

            // Update select all checkbox when individual checkboxes change
            $('input[name="asesors[]"]').on('change', function() {
                const totalCheckboxes = $('input[name="asesors[]"]').length;
                const checkedCheckboxes = $('input[name="asesors[]"]:checked').length;
                $('#select-all-asesors').prop('checked', totalCheckboxes === checkedCheckboxes);
            });

            // Update select all on page load
            setTimeout(function() {
                const totalCheckboxes = $('input[name="asesors[]"]').length;
                const checkedCheckboxes = $('input[name="asesors[]"]:checked').length;
                $('#select-all-asesors').prop('checked', totalCheckboxes === checkedCheckboxes);
            }, 100);
        });
    </script>
    @endpush

@endsection
