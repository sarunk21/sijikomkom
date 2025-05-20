@extends('components.templates.master-layout')

@section('title', 'Jadwal - Edit')
@section('page-title', 'Edit Jadwal')

@section('content')

    <a href="{{ route('admin.jadwal.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali</span>
    </a>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.jadwal.update', 1) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="skema" class="form-label">Skema</label>
                    <input type="text" id="skema" name="skema" class="form-control"
                        placeholder="Isi skema di sini..." value="Skema 1" required>
                </div>

                <div class="mb-3">
                    <label for="asesor" class="form-label">Asesor</label>
                    <input type="text" id="asesor" name="asesor" class="form-control"
                        placeholder="Isi asesor di sini..." value="Asesor 1" required>
                </div>

                <div class="mb-3">
                    <label for="tuk" class="form-label">TUK</label>
                    <select name="tuk" id="tuk" class="form-control" required>
                        <option value="" disabled>Pilih TUK di sini...</option>
                        <option value="Lab" selected>Lab</option>
                        <option value="Kantor">Kantor</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="" disabled>Pilih status di sini...</option>
                        <option value="Aktif" selected>Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control"
                        placeholder="Isi tanggal di sini..." value="2025-01-01" required>
                </div>

                <div class="mb-3">
                    <label for="kuota" class="form-label">Kuota</label>
                    <input type="number" id="kuota" name="kuota" class="form-control"
                        placeholder="Isi kuota di sini..." value="10" required>
                </div>

                <div class="mb-3">
                    <label for="peserta" class="form-label">Peserta</label>
                    <input type="number" id="peserta" name="peserta" class="form-control"
                        placeholder="Isi peserta di sini..." value="0" required>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.jadwal.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
                    <button type="submit" class="btn btn-orange">Simpan Perubahan</button>
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
