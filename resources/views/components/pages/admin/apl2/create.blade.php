@extends('components.templates.master-layout')

@section('title', 'APL02 - Create')
@section('page-title', 'Tambah APL02')

@section('content')

    <a href="{{ route('admin.apl-2.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
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
            <form action="{{ route('admin.apl-2.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="skema" class="form-label">Skema <span class="text-danger">*</span></label>
                    <select name="skema_id" id="skema_id" class="form-control @error('skema_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Pilih skema di sini...</option>
                        @foreach ($skema as $item)
                            <option {{ old('skema_id') == $item->id ? 'selected' : '' }} value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                    @error('skema_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="link_ujikom_asesor" class="form-label">Link Ujikom Asesor <span class="text-danger">*</span></label>
                    <input type="text" id="link_ujikom_asesor" name="link_ujikom_asesor" class="form-control @error('link_ujikom_asesor') is-invalid @enderror"
                        placeholder="Isi link ujikom asesor di sini..." value="{{ old('link_ujikom_asesor') }}" required>
                    @error('link_ujikom_asesor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="link_ujikom_asesi" class="form-label">Link Ujikom Asesi <span class="text-danger">*</span></label>
                    <input type="text" id="link_ujikom_asesi" name="link_ujikom_asesi" class="form-control @error('link_ujikom_asesi') is-invalid @enderror"
                        placeholder="Isi link ujikom asesi di sini..." value="{{ old('link_ujikom_asesi') }}" required>
                    @error('link_ujikom_asesi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.apl-2.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
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
