@extends('components.templates.master-layout')

@section('title', 'Detail APL2')
@section('page-title', 'Detail APL2')

@section('content')

    <a href="{{ route('admin.apl-2.show-by-skema', $skema->id) }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali ke Daftar Pertanyaan</span>
    </a>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Informasi Skema Sertifikasi -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Detail Pertanyaan APL2</h5>
            <p class="card-text font-weight-bold text-primary" style="font-size: 1.2rem;">
                {{ $skema->nama }}
            </p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Pertanyaan:</h6>
                    <p>{{ $apl2->question_text }}</p>
                </div>
                <div class="col-md-6">
                    <h6>Tipe Pertanyaan:</h6>
                    <span class="badge badge-info">{{ ucfirst($apl2->question_type) }}</span>
                </div>
            </div>

            @if($apl2->question_options)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Opsi Jawaban:</h6>
                        <ul>
                            @foreach($apl2->question_options as $option)
                                <li>{{ $option }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if($apl2->bukti_isian_tes)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Bukti Isian Tes:</h6>
                        <p>{{ $apl2->bukti_isian_tes }}</p>
                    </div>
                </div>
            @endif

            @if($apl2->custom_data)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Custom Data:</h6>
                        <p>{{ $apl2->custom_data }}</p>
                    </div>
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-md-6">
                    <h6>Urutan:</h6>
                    <p>{{ $apl2->urutan }}</p>
                </div>
                <div class="col-md-6">
                    <h6>Pertanyaan BK/K:</h6>
                    @if($apl2->is_bk_k_question)
                        <span class="badge badge-success">Ya</span>
                    @else
                        <span class="badge badge-secondary">Tidak</span>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.apl-2.edit', $apl2->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Pertanyaan
                </a>
                <a href="{{ route('admin.apl-2.show-by-skema', $skema->id) }}" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Daftar Pertanyaan
                </a>
            </div>
        </div>
    </div>

@endsection
