@extends('components.templates.master-layout')

@section('title', 'Ujian Kompetensi')
@section('page-title', 'Ujian Kompetensi')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-file-alt me-2"></i>
                Form Ujian Kompetensi
            </h5>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Petunjuk:</strong> Silakan isi jawaban untuk setiap pertanyaan di bawah ini dengan lengkap dan
                jelas.
            </div>

            {{-- Hanya bisa disubmit sekali, jika sudah disubmit maka tidak bisa diubah --}}
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Jawaban hanya bisa disubmit sekali. Jika sudah disubmit, maka tidak bisa diubah. Harap diisi dengan
                benar.
            </div>


            <form action="{{ route('asesi.ujikom.store.jawaban', $pendaftaran->id) }}" method="post">
                @csrf

                @foreach ($apl2 as $index => $form)
                    <div class="card mb-4 border-left-primary">
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="answer_{{ $form->id }}" class="form-label fw-bold">
                                    <i class="fas fa-question-circle me-1"></i>
                                    Pertanyaan {{ $index + 1 }}:
                                </label>
                                <div class="mb-2">
                                    {{ $form->question_text }} <span class="text-danger">*</span>
                                </div>
                                <textarea name="answers[{{ $form->id }}]" id="answer_{{ $form->id }}"
                                    class="form-control @error('answers.' . $form->id) is-invalid @enderror" rows="5"
                                    placeholder="Tulis jawaban Anda di sini..." required>{{ old('answers.' . $form->id) ?? $form->responses->where('pendaftaran_id', $pendaftaran->id)->first()->answer_text ?? '' }}</textarea>

                                @error('answers.' . $form->id)
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        <i class="fas fa-list me-1"></i>
                        Total pertanyaan: <strong>{{ count($apl2) }}</strong>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i>
                            Submit Jawaban
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
