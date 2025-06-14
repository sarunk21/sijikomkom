@extends('components.templates.master-layout')

@section('title', 'Skema - Create')
@section('page-title', 'Tambah Skema')

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
            <form action="{{ route('admin.skema.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Skema <span class="text-danger">*</span></label>
                    <input type="text" id="nama" name="nama"
                        class="form-control @error('nama') is-invalid @enderror" placeholder="Isi nama skema di sini..."
                        value="{{ old('nama') }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="kode" class="form-label">Kode Skema <span class="text-danger">*</span></label>
                    <input type="text" id="kode" name="kode"
                        class="form-control @error('kode') is-invalid @enderror" placeholder="Isi Kode Unik di sini..."
                        value="{{ old('kode') }}" required>
                    @error('kode')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" id="kategori" class="form-control @error('kategori') is-invalid @enderror" required>
                        <option value="" disabled selected>Pilih Kategori Skema di sini...</option>
                        <option value="Sertifikasi" {{ old('kategori') == 'Sertifikasi' ? 'selected' : '' }}>Sertifikasi</option>
                        <option value="Pelatihan" {{ old('kategori') == 'Pelatihan' ? 'selected' : '' }}>Pelatihan</option>
                    </select>
                    @error('kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="bidang" class="form-label">Bidang <span class="text-danger">*</span></label>
                    <select name="bidang" id="bidang" class="form-control @error('bidang') is-invalid @enderror"
                        required>
                        <option value="" disabled selected>Pilih Bidang Skema di sini...</option>
                        <option value="S1 Sistem Informasi" {{ old('bidang') == 'S1 Sistem Informasi' ? 'selected' : '' }}>S1 Sistem Informasi</option>
                        <option value="S1 Teknik Informatika" {{ old('bidang') == 'S1 Teknik Informatika' ? 'selected' : '' }}>S1 Teknik Informatika</option>
                        <option value="D3 Sistem Informasi" {{ old('bidang') == 'D3 Sistem Informasi' ? 'selected' : '' }}>D3 Sistem Informasi</option>
                    </select>
                    @error('bidang')
                        <div class="invalid-feedback">{{ $message }}</div>
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

@endsection
