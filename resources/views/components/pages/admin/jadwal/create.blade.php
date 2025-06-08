@extends('components.templates.master-layout')

@section('title', 'Jadwal - Create')
@section('page-title', 'Tambah Jadwal')

@section('content')

    <a href="{{ route('admin.jadwal.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali</span>
    </a>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.jadwal.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="skema" class="form-label">Skema <span class="text-danger">*</span></label>
                    <select name="skema_id" id="skema_id" class="form-control" required>
                        <option value="" disabled selected>Pilih skema di sini...</option>
                        @foreach ($skema as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tuk" class="form-label">TUK <span class="text-danger">*</span></label>
                    <select name="tuk_id" id="tuk_id" class="form-control" required>
                        <option value="" disabled selected>Pilih TUK di sini...</option>
                        @foreach ($tuk as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="" disabled selected>Pilih status di sini...</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="datetime-local" id="tanggal_ujian" name="tanggal_ujian" class="form-control" min="{{ date('Y-m-d H:i') }}" required>
                </div>

                <div class="mb-3">
                    <label for="kuota" class="form-label">Kuota <span class="text-danger">*</span></label>
                    <input type="number" id="kuota" name="kuota" class="form-control"
                        placeholder="Isi kuota di sini..." required>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.jadwal.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
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
