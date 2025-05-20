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
            <form action="" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="skema" class="form-label">Skema</label>
                    <input type="text" id="skema" name="skema" class="form-control"
                        placeholder="Isi skema di sini..." required>
                </div>

                <div class="mb-3">
                    <label for="asesor" class="form-label">Asesor</label>
                    <input type="text" id="asesor" name="asesor" class="form-control"
                        placeholder="Isi asesor di sini..." required>
                </div>

                <div class="mb-3">
                    <label for="tuk" class="form-label">TUK</label>
                    <select name="tuk" id="tuk" class="form-control" required>
                        <option value="" disabled selected>Pilih TUK di sini...</option>
                        <option value="Lab">Lab</option>
                        <option value="Kantor">Kantor</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="" disabled selected>Pilih status di sini...</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="kuota" class="form-label">Kuota</label>
                    <input type="number" id="kuota" name="kuota" class="form-control"
                        placeholder="Isi kuota di sini..." required>
                </div>

                <div class="mb-3">
                    <label for="peserta" class="form-label">Peserta</label>
                    <input type="number" id="peserta" name="peserta" class="form-control"
                        placeholder="Isi peserta di sini..." required>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.jadwal.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
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
