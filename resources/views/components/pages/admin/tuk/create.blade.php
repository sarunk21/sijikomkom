@extends('components.templates.master-layout')

@section('title', 'TUK - Create')
@section('page-title', 'Tambah TUK')

@section('content')

    <a href="{{ route('admin.tuk.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali</span>
    </a>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="nama_tuk" class="form-label">Nama TUK</label>
                    <input type="text" id="nama_tuk" name="nama_tuk" class="form-control"
                        placeholder="Isi nama TUK di sini..." required>
                </div>

                <div class="mb-3">
                    <label for="kode" class="form-label">Kode</label>
                    <input type="text" id="kode" name="kode" class="form-control"
                        placeholder="Isi Kode Unik di sini..." required>
                </div>

                <div class="mb-3">
                    <label for="jenis_tuk" class="form-label">Jenis TUK</label>
                    <select name="jenis_tuk" id="jenis_tuk" class="form-control" required>
                        <option value="" disabled selected>Pilih Jenis TUK di sini...</option>
                        <option value="Lab">Lab</option>
                        <option value="Kantor">Kantor</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea name="alamat" id="alamat" class="form-control" required></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.tuk.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
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
