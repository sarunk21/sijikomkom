@extends('components.templates.master-layout')

@section('title', 'Pembayaran Asesor - Create')
@section('page-title', 'Tambah Pembayaran Asesor')

@section('content')

    <a href="{{ route('admin.pembayaran-asesor.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
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
            <form action="{{ route('admin.pembayaran-asesor.update', $pembayaranAsesor->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" name="asesor_id" value="{{ $pembayaranAsesor->asesor_id }}">
                <input type="hidden" name="jadwal_id" value="{{ $pembayaranAsesor->jadwal_id }}">

                <div class="mb-3">
                    <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran <span
                            class="text-danger">*</span></label>
                    <input type="file" id="bukti_pembayaran" name="bukti_pembayaran"
                        class="form-control @error('bukti_pembayaran') is-invalid @enderror"
                        placeholder="Isi bukti pembayaran di sini..." required
                        accept="image/*">
                    @error('bukti_pembayaran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.pembayaran-asesor.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
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
