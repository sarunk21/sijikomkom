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


            <form action="{{ route('asesi.ujikom.store.jawaban', $pendaftaran->id) }}" method="post" enctype="multipart/form-data">
                @csrf

                @foreach ($apl2 as $index => $form)
                    <div class="card mb-4 shadow-sm" style="border-left: 4px solid #4e73df;">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <span class="badge badge-primary px-3 py-2" style="font-size: 0.875rem; font-weight: 600;">
                                    <i class="fas fa-question-circle mr-1"></i> Pertanyaan {{ $index + 1 }}
                                </span>
                            </div>
                            <h6 class="font-weight-bold text-dark mb-3" style="font-size: 1.1rem;">
                                {{ $form->question_text }} <span class="text-danger">*</span>
                            </h6>

                            @php
                                $existingAnswer = old('answers.' . $form->id) ?? ($form->responses->where('pendaftaran_id', $pendaftaran->id)->first()?->answer_text ?? '');
                            @endphp

                            @if($form->question_type === 'text')
                                <input type="text" name="answers[{{ $form->id }}]" id="answer_{{ $form->id }}"
                                    class="form-control @error('answers.' . $form->id) is-invalid @enderror"
                                    placeholder="Tulis jawaban Anda di sini..." value="{{ $existingAnswer }}" required>

                            @elseif($form->question_type === 'textarea')
                                <textarea name="answers[{{ $form->id }}]" id="answer_{{ $form->id }}"
                                    class="form-control @error('answers.' . $form->id) is-invalid @enderror" rows="5"
                                    placeholder="Tulis jawaban Anda di sini..." required>{{ $existingAnswer }}</textarea>

                            @elseif($form->question_type === 'checkbox')
                                @php
                                    // Ensure options are array and trim each value
                                    $rawOptions = is_array($form->question_options) ? $form->question_options : explode(',', $form->question_options);
                                    $options = array_map('trim', $rawOptions);

                                    // Trim selected options as well
                                    $rawSelected = is_array($existingAnswer) ? $existingAnswer : (!empty($existingAnswer) ? explode(',', $existingAnswer) : []);
                                    $selectedOptions = array_map('trim', $rawSelected);
                                @endphp
                                @foreach($options as $option)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="answers[{{ $form->id }}][]"
                                            id="answer_{{ $form->id }}_{{ $loop->index }}" value="{{ $option }}"
                                            {{ in_array($option, $selectedOptions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="answer_{{ $form->id }}_{{ $loop->index }}">
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach

                            @elseif($form->question_type === 'radio')
                                @php
                                    $rawOptions = is_array($form->question_options) ? $form->question_options : explode(',', $form->question_options);
                                    $options = array_map('trim', $rawOptions);
                                @endphp
                                @foreach($options as $option)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="answers[{{ $form->id }}]"
                                            id="answer_{{ $form->id }}_{{ $loop->index }}" value="{{ $option }}"
                                            {{ trim($existingAnswer) === $option ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="answer_{{ $form->id }}_{{ $loop->index }}">
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach

                            @elseif($form->question_type === 'select')
                                @php
                                    $rawOptions = is_array($form->question_options) ? $form->question_options : explode(',', $form->question_options);
                                    $options = array_map('trim', $rawOptions);
                                @endphp
                                <select name="answers[{{ $form->id }}]" id="answer_{{ $form->id }}"
                                    class="form-control @error('answers.' . $form->id) is-invalid @enderror" required>
                                    <option value="">-- Pilih Jawaban --</option>
                                    @foreach($options as $option)
                                        <option value="{{ $option }}" {{ trim($existingAnswer) === $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>

                            @elseif($form->question_type === 'file')
                                <input type="file" name="files[{{ $form->id }}]" id="answer_{{ $form->id }}"
                                    class="form-control-file @error('answers.' . $form->id) is-invalid @enderror"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                                <small class="form-text text-muted mt-2">
                                    <i class="fas fa-info-circle"></i> Format yang diperbolehkan: PDF, DOC, DOCX, JPG, PNG (Maks. 2MB)
                                </small>
                                @if($existingAnswer)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $existingAnswer) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-file mr-1"></i> Lihat File yang Diupload
                                        </a>
                                    </div>
                                @endif

                            @else
                                <textarea name="answers[{{ $form->id }}]" id="answer_{{ $form->id }}"
                                    class="form-control @error('answers.' . $form->id) is-invalid @enderror" rows="5"
                                    placeholder="Tulis jawaban Anda di sini..." required>{{ $existingAnswer }}</textarea>
                            @endif

                            @error('answers.' . $form->id)
                                <div class="invalid-feedback d-block mt-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
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
