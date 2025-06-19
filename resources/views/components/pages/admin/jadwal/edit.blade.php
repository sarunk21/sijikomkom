@extends('components.templates.master-layout')

@section('title', 'Jadwal - Edit')
@section('page-title', 'Edit Jadwal')

@section('content')

    <a href="{{ route('admin.jadwal.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
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
            <form action="{{ route('admin.jadwal.update', $jadwal->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="skema" class="form-label">Skema</label>
                    <select name="skema_id" id="skema_id" class="form-control @error('skema_id') is-invalid @enderror"
                        required>
                        <option value="" disabled selected>Pilih skema di sini...</option>
                        @foreach ($skema as $item)
                            <option {{ old('skema_id', $jadwal->skema_id) == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
                                {{ $item->nama }}</option>
                        @endforeach
                    </select>
                    @error('skema_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tuk" class="form-label">TUK</label>
                    <select name="tuk_id" id="tuk_id" class="form-control @error('tuk_id') is-invalid @enderror"
                        required>
                        <option value="" disabled>Pilih TUK di sini...</option>
                        @foreach ($tuk as $item)
                            <option {{ old('tuk_id', $jadwal->tuk_id) == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
                                {{ $item->nama }}</option>
                        @endforeach
                    </select>
                    @error('tuk_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror"
                        required>
                        <option value="" disabled>Pilih status di sini...</option>
                        <option value="1" {{ old('status', $jadwal->status) == 1 ? 'selected' : '' }}>Aktif</option>
                        <option value="2" {{ old('status', $jadwal->status) == 2 ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="3" {{ old('status', $jadwal->status) == 3 ? 'selected' : '' }}>Selesai</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="datetime-local" id="tanggal_ujian" name="tanggal_ujian"
                        class="form-control @error('tanggal_ujian') is-invalid @enderror"
                        placeholder="Isi tanggal di sini..."
                        value="{{ old('tanggal_ujian', date('Y-m-d H:i', strtotime($jadwal->tanggal_ujian))) }}" required>
                    @error('tanggal_ujian')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal Selesai</label>
                    <input type="datetime-local" id="tanggal_selesai" name="tanggal_selesai"
                        class="form-control @error('tanggal_selesai') is-invalid @enderror"
                        placeholder="Isi tanggal selesai di sini..."
                        value="{{ old('tanggal_selesai', date('Y-m-d H:i', strtotime($jadwal->tanggal_selesai))) }}" required>
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal Maksimal Pendaftaran</label>
                    <input type="datetime-local" id="tanggal_maksimal_pendaftaran" name="tanggal_maksimal_pendaftaran"
                        class="form-control @error('tanggal_maksimal_pendaftaran') is-invalid @enderror"
                        placeholder="Isi tanggal maksimal pendaftaran di sini..."
                        value="{{ old('tanggal_maksimal_pendaftaran', date('Y-m-d H:i', strtotime($jadwal->tanggal_maksimal_pendaftaran))) }}" required>
                </div>

                <div class="mb-3">
                    <label for="kuota" class="form-label">Kuota</label>
                    <input type="number" id="kuota" name="kuota"
                        class="form-control @error('kuota') is-invalid @enderror" placeholder="Isi kuota di sini..."
                        value="{{ old('kuota', $jadwal->kuota) }}" required>
                    @error('kuota')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
