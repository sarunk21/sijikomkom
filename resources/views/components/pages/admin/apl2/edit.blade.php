@extends('components.templates.master-layout')

@section('title', 'APL 2 - Edit')
@section('page-title', 'Edit APL 2')

@section('content')

    <a href="{{ route('admin.apl-2.show', $apl2->id) }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali</span>
    </a>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Informasi Skema Sertifikasi -->

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Detail Skema Sertifikasi yang Dipilih</h5>
            <p class="card-text font-weight-bold text-primary" style="font-size: 1.2rem;">
                {{ $skema->nama }}
            </p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.apl-2.update', $apl2->id) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="skema_id" value="{{ $apl2->skema_id }}">

                <div class="mb-3">
                    <label for="question_text" class="form-label">Pertanyaan</label>
                    <input type="text" id="question_text" name="question_text"
                        class="form-control @error('question_text') is-invalid @enderror"
                        placeholder="Isi pertanyaan di sini..." value="{{ old('question_text', $apl2->question_text) }}"
                        required>
                    @error('question_text')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.apl-2.show', $apl2->skema_id) }}" class="btn btn-outline-danger mr-2">Batalkan</a>
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

@endsection
