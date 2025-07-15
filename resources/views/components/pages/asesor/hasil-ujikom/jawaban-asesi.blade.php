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

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Petunjuk:</strong> Silakan lihat jawaban asesi untuk setiap pertanyaan di bawah ini.
            </div>

            {{-- Hanya bisa disubmit sekali, jika sudah disubmit maka tidak bisa diubah --}}
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Jawaban hanya bisa dilihat. Jika sudah disubmit, maka tidak bisa diubah. Harap diisi dengan
                benar.
            </div>


            <form method="post" onsubmit="return false;">
                @csrf
                @method('PUT')
                @foreach ($jawabanAsesi as $index => $form)
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
                                    placeholder="Tulis jawaban Anda di sini..." required>{{ old('answers.' . $form->id) ?? ($form->responses != null ? $form->responses->where('pendaftaran_id', $pendaftaran->id)->first()->answer_text : '') }}</textarea>

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
                        Total pertanyaan: <strong>{{ count($jawabanAsesi) }}</strong>
                    </div>
                </div>
            </form>

            {{-- Button Update Status Kompenten atau tidak kompenten, buat 2 button ny, 1 button untuk kompenten dan 1 button untuk tidak kompenten --}}
            <div class="d-flex justify-content-end align-items-center mt-4 gap-2">
                <form action="{{ route('asesor.hasil-ujikom.update', $id) }}" method="post"
                    onsubmit="return confirm('Apakah Anda yakin ingin menetapkan asesi tidak kompenten?');" class="mr-2">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="4">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle me-1"></i>
                        Tidak Kompenten
                    </button>
                </form>
                <form action="{{ route('asesor.hasil-ujikom.update', $id) }}" method="post"
                    onsubmit="return confirm('Apakah Anda yakin ingin menetapkan asesi kompenten?');">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="5">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle me-1"></i>
                        Kompenten
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
