@extends('components.templates.master-layout')

@section('title', 'Upload Sertifikat Bertanda Tangan - Edit')
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
            <form action="{{ route('asesi.upload-sertifikat.update', $uploadSertifikat->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="jadwal" class="form-label">Jadwal</label>
                    <input type="text" class="form-control" value="{{ $uploadSertifikat->skema->nama ?? '-' }} - {{ $uploadSertifikat->pendaftaran->tuk->nama ?? '-' }} - {{ $uploadSertifikat->pendaftaran->jadwal->tanggal_ujian ? \Carbon\Carbon::parse($uploadSertifikat->pendaftaran->jadwal->tanggal_ujian)->format('d M Y') : '-' }}" disabled>
                    <input type="hidden" name="pendaftaran_id" value="{{ $uploadSertifikat->pendaftaran_id }}">
                </div>

                <div class="mb-3">
                    <label for="sertifikat" class="form-label">Upload Sertifikat Baru</label>
                    <input type="file" id="sertifikat" name="sertifikat"
                        class="form-control @error('sertifikat') is-invalid @enderror">
                    @error('sertifikat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if ($uploadSertifikat->sertifikat)
                        <small class="form-text text-muted mt-1">
                            Sertifikat saat ini: <a href="{{ asset('storage/' . $uploadSertifikat->sertifikat) }}" target="_blank">Lihat File</a>
                        </small>
                    @endif
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
