@extends('components.templates.master-layout')

@section('title', 'Upload Sertifikat Bertanda Tangan - Create')
@section('page-title', 'Upload Sertifikat Bertanda Tangan')

@section('content')

    <a href="{{ route('asesi.upload-sertifikat.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
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
            <form action="{{ route('asesi.upload-sertifikat.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="skema" class="form-label">Jadwal <span class="text-danger">*</span></label>
                    <select name="pendaftaran_id" id="pendaftaran_id" class="form-control @error('pendaftaran_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Pilih jadwal di sini...</option>
                        @foreach ($pendaftaran as $item)
                            <option value="{{ $item->id }}" {{ old('pendaftaran_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->skema->nama ?? '-' }} -
                                {{ $item->tuk->nama ?? '-' }} -
                                {{ $item->jadwal->tanggal_ujian ? \Carbon\Carbon::parse($item->jadwal->tanggal_ujian)->format('d M Y') : '-' }}
                            </option>
                        @endforeach
                    </select>
                    @error('pendaftaran_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="sertifikat" class="form-label">Sertifikat <span
                            class="text-danger">*</span></label>
                    <input type="file" id="sertifikat" name="sertifikat"
                        class="form-control @error('sertifikat') is-invalid @enderror" required>
                    @error('sertifikat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('asesi.upload-sertifikat.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
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
