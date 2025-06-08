@extends('components.templates.master-layout')

@section('title', 'TUK - Edit')
@section('page-title', 'Edit TUK')

@section('content')

    <a href="{{ route('admin.tuk.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali</span>
    </a>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.tuk.update', $tuk->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama TUK <span class="text-danger">*</span></label>
                    <input type="text" id="nama" name="nama" class="form-control"
                        placeholder="Isi nama TUK di sini..." value="{{ $tuk->nama }}" required>
                </div>

                <div class="mb-3">
                    <label for="kode" class="form-label">Kode TUK <span class="text-danger">*</span></label>
                    <input type="text" id="kode" name="kode" class="form-control"
                        placeholder="Isi Kode Unik di sini..." value="{{ $tuk->kode }}" required>
                </div>

                <div class="mb-3">
                    <label for="kategori" class="form-label">Jenis TUK <span class="text-danger">*</span></label>
                    <select name="kategori" id="kategori" class="form-control" required>
                        <option value="" disabled>Pilih Jenis TUK di sini...</option>
                        <option value="Lab" {{ $tuk->kategori == 'Lab' ? 'selected' : '' }}>Lab</option>
                        <option value="Kantor" {{ $tuk->kategori == 'Kantor' ? 'selected' : '' }}>Kantor</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                    <textarea name="alamat" id="alamat" class="form-control" required>{{ $tuk->alamat }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.tuk.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
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
