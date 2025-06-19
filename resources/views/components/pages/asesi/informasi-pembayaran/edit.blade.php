@extends('components.templates.master-layout')

@section('title', 'Informasi Pembayaran - Edit')
@section('page-title', 'Edit Informasi Pembayaran')

@section('content')

    <a href="{{ route('asesi.daftar-ujikom.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
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
            <div class="row">
                <div class="col-12">
                    <h5 class="card-title mb-3">Edit Informasi Pembayaran</h5>
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle mr-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="mb-1">Informasi Rekening Pembayaran</h6>
                                <p class="mb-0">Silakan transfer ke rekening berikut:</p>
                                <div class="mt-2">
                                    <strong>Bank BCA</strong><br>
                                    <strong>No. Rekening: 1234567890</strong><br>
                                    <strong>Atas Nama: UPN "Veteran" Jakarta</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('asesi.informasi-pembayaran.update', $pembayaran->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="jadwal_id" class="form-label">Jadwal Ujian <span class="text-danger">*</span></label>
                    <select name="jadwal_id" id="jadwal_id" class="form-control @error('jadwal_id') is-invalid @enderror"
                        disabled>
                        <option value="" disabled selected>Jadwal Ujian di sini...</option>
                        @foreach ($jadwal as $item)
                            <option value="{{ $item->id }}"
                                {{ old('jadwal_id', $pembayaran->jadwal_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->skema->nama }} - {{ $item->tanggal_ujian }} - {{ $item->tuk->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('jadwal_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran <span
                            class="text-danger">*</span></label>
                    <input type="file" id="bukti_pembayaran" name="bukti_pembayaran"
                        class="form-control @error('bukti_pembayaran') is-invalid @enderror" required>
                    @error('bukti_pembayaran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('asesi.daftar-ujikom.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
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
