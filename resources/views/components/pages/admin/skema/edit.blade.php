@extends('components.templates.master-layout')

@section('title', 'Skema - Edit')
@section('page-title', 'Edit Skema')

@section('content')

    <a href="{{ route('admin.skema.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali</span>
    </a>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama_skema" class="form-label">Nama Skema</label>
                    <input type="text" id="nama_skema" name="nama_skema" class="form-control"
                        placeholder="Isi nama skema di sini..." value="System Analyst" required>
                </div>

                <div class="mb-3">
                    <label for="kode" class="form-label">Kode</label>
                    <input type="text" id="kode" name="kode" class="form-control"
                        placeholder="Isi Kode Unik di sini..." value="SA.006" required>
                </div>

                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <select name="kategori" id="kategori" class="form-control" required>
                        <option value="" disabled>Pilih Kategori di sini...</option>
                        <option value="Sertifikasi" selected>Sertifikasi</option>
                        <option value="Pelatihan">Pelatihan</option>
                        <!-- tambahkan sesuai kebutuhan -->
                    </select>
                </div>

                <div class="mb-4">
                    <label for="bidang" class="form-label">Bidang</label>
                    <select name="bidang" id="bidang" class="form-control" required>
                        <option value="" disabled>Pilih Bidang di sini...</option>
                        <option value="Sistem Informasi" selected>Sistem Informasi</option>
                        <option value="Teknik Informatika">Teknik Informatika</option>
                        <!-- tambahkan bidang lainnya -->
                    </select>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.skema.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
                    <button type="submit" class="btn btn-orange">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tambahan styling warna btn-orange jika belum ada --}}
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
