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
            <form action="{{ route('admin.jadwal.update', $jadwal->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="skema" class="form-label">Skema</label>
                    <select name="skema_id" id="skema_id" class="form-control" required>
                        <option value="" disabled selected>Pilih skema di sini...</option>
                        @foreach ($skema as $item)
                            <option value="{{ $item->id }}" {{ $jadwal->skema_id == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tuk" class="form-label">TUK</label>
                    <select name="tuk_id" id="tuk_id" class="form-control" required>
                        <option value="" disabled>Pilih TUK di sini...</option>
                        @foreach ($tuk as $item)
                            <option value="{{ $item->id }}" {{ $jadwal->tuk_id == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="" disabled>Pilih status di sini...</option>
                        <option value="Aktif" {{ $jadwal->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Tidak Aktif" {{ $jadwal->status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="datetime-local" id="tanggal_ujian" name="tanggal_ujian" class="form-control"
                        placeholder="Isi tanggal di sini..." value="{{ date('Y-m-d H:i', strtotime($jadwal->tanggal_ujian)) }}" required>
                </div>

                <div class="mb-3">
                    <label for="kuota" class="form-label">Kuota</label>
                    <input type="number" id="kuota" name="kuota" class="form-control"
                        placeholder="Isi kuota di sini..." value="{{ $jadwal->kuota }}" required>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.jadwal.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
                    <button type="submit" class="btn btn-orange">Simpan Perubahan</button>
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
