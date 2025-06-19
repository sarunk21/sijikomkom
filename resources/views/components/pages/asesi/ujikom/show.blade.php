@extends('components.templates.master-layout')

@section('title', 'Ujian Kompetensi')
@section('page-title', 'Ujian Kompetensi')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <h5 class="card-title mb-3">Form Ujian Kompetensi</h5>
                    @if ($apl2->isNotEmpty())
                        @foreach ($apl2 as $form)
                            <div class="mb-4">
                                <h6 class="text-primary">{{ $form->skema->nama }}</h6>
                                @if ($form->link_ujikom_asesi)
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <iframe class="embed-responsive-item" src="{{ $form->link_ujikom_asesi }}"
                                            frameborder="0" allowfullscreen>
                                        </iframe>
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ $form->link_ujikom_asesi }}" target="_blank"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-external-link-alt mr-1"></i>
                                            Buka di Tab Baru
                                        </a>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Link form ujian kompetensi belum tersedia.
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Belum ada form ujian kompetensi yang tersedia.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
