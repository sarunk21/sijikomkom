@extends('components.templates.master-layout')

@section('title', 'Jawaban Asesi')
@section('page-title', 'Jawaban Asesi')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-file-alt me-2"></i>
                Jawaban Asesi
            </h5>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="alert alert-info border-left-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Petunjuk:</strong> Berikut adalah jawaban yang telah diisi oleh asesi. Silakan review jawaban dan berikan penilaian kompetensi.
            </div>

            @foreach ($jawabanAsesi as $index => $form)
                <div class="card mb-3 shadow-sm" style="border-left: 4px solid #36b9cc;">
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge badge-info" style="font-size: 0.875rem; font-weight: 600; padding: 0.5rem 1rem;">
                                <i class="fas fa-question-circle mr-1"></i> Pertanyaan {{ $index + 1 }}
                            </span>
                        </div>

                        <h6 class="font-weight-bold text-dark mb-3" style="font-size: 1rem; line-height: 1.5;">
                            {{ $form->apl2->question_text ?? '' }}
                        </h6>

                        <div class="bg-light p-3 rounded border">
                            <label class="text-muted small font-weight-semibold mb-2 d-block">
                                <i class="fas fa-user-edit mr-1"></i> Jawaban Asesi:
                            </label>
                            <div class="text-dark" style="word-break: break-word; line-height: 1.6; white-space: normal;">
                                @if($form->apl2->question_type === 'file' && $form->answer_text)
                                    <a href="{{ asset('storage/' . $form->answer_text) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-download mr-1"></i> Download File
                                    </a>
                                    <small class="d-block mt-2 text-muted">
                                        <i class="fas fa-file mr-1"></i> {{ basename($form->answer_text) }}
                                    </small>
                                @else
                                    {{ trim($form->answer_text ?? '-') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="card shadow-sm mb-4 border-0 bg-light">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center text-muted">
                        <i class="fas fa-list-ol mr-2"></i>
                        <span>Total pertanyaan yang dijawab: <strong class="text-dark">{{ count($jawabanAsesi) }}</strong></span>
                    </div>
                </div>
            </div>

            {{-- Penilaian Kompetensi --}}
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-clipboard-check mr-2"></i> Penilaian Kompetensi
                    </h6>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-4">
                        <i class="fas fa-info-circle mr-1"></i>
                        Berdasarkan jawaban yang telah diberikan oleh asesi, tentukan status kompetensi:
                    </p>

                    <div class="d-flex justify-content-end align-items-center">
                        <form action="{{ route('asesor.hasil-ujikom.update', $id) }}" method="post"
                            onsubmit="return confirm('Apakah Anda yakin ingin menetapkan asesi sebagai BELUM KOMPETEN?');" class="d-inline mr-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="4">
                            <button type="submit" class="btn btn-danger px-4 py-2">
                                <i class="fas fa-times-circle mr-2"></i>
                                Belum Kompeten
                            </button>
                        </form>

                        <form action="{{ route('asesor.hasil-ujikom.update', $id) }}" method="post"
                            onsubmit="return confirm('Apakah Anda yakin ingin menetapkan asesi sebagai KOMPETEN?');" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="5">
                            <button type="submit" class="btn btn-success px-5 py-2">
                                <i class="fas fa-check-circle mr-2"></i>
                                Kompeten
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
