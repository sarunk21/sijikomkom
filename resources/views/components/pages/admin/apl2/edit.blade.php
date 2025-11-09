@extends('components.templates.master-layout')

@section('title', 'Bank Soal - Edit')
@section('page-title', 'Edit Bank Soal')

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
    <div class="card shadow-sm mb-4" style="border-left: 4px solid #4e73df;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-gradient rounded-circle p-3 mr-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-certificate text-white" style="font-size: 1.3rem;"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1" style="font-size: 0.85rem; font-weight: 500;">Detail Skema Sertifikasi yang Dipilih</h6>
                    <h5 class="mb-0 font-weight-bold text-primary">{{ $skema->nama }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-edit mr-2"></i> Form Edit Bank Soal
            </h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.apl-2.update', $apl2->id) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="skema_id" value="{{ $apl2->skema_id }}">

                <div class="mb-4">
                    <label for="question_text" class="form-label font-weight-semibold text-dark">
                        <i class="fas fa-question-circle text-primary mr-1"></i> Pertanyaan <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="question_text" name="question_text"
                        class="form-control form-control-lg @error('question_text') is-invalid @enderror"
                        placeholder="Masukkan pertanyaan di sini..." value="{{ old('question_text', $apl2->question_text) }}"
                        required>
                    @error('question_text')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="question_type" class="form-label font-weight-semibold text-dark">
                        <i class="fas fa-list-ul text-primary mr-1"></i> Tipe Soal <span class="text-danger">*</span>
                    </label>
                    <select id="question_type" name="question_type" class="form-control @error('question_type') is-invalid @enderror" required>
                        <option value="">-- Pilih Tipe Soal --</option>
                        <option value="text" {{ old('question_type', $apl2->question_type) == 'text' ? 'selected' : '' }}>Text</option>
                        <option value="textarea" {{ old('question_type', $apl2->question_type) == 'textarea' ? 'selected' : '' }}>Textarea</option>
                        <option value="checkbox" {{ old('question_type', $apl2->question_type) == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                        <option value="radio" {{ old('question_type', $apl2->question_type) == 'radio' ? 'selected' : '' }}>Radio Button</option>
                        <option value="select" {{ old('question_type', $apl2->question_type) == 'select' ? 'selected' : '' }}>Select</option>
                        <option value="file" {{ old('question_type', $apl2->question_type) == 'file' ? 'selected' : '' }}>File Upload</option>
                    </select>
                    @error('question_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4" id="question-options-container" style="display: none;">
                    <label for="question_options" class="form-label font-weight-semibold text-dark">
                        <i class="fas fa-check-square text-primary mr-1"></i> Opsi Jawaban
                    </label>
                    <input type="text" id="question_options" name="question_options" class="form-control @error('question_options') is-invalid @enderror"
                        placeholder="Pisahkan dengan koma (contoh: Opsi 1, Opsi 2, Opsi 3)" value="{{ old('question_options', is_array($apl2->question_options) ? implode(', ', $apl2->question_options) : $apl2->question_options) }}">
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> Untuk tipe checkbox, radio, dan select, pisahkan setiap opsi dengan koma
                    </small>
                    @error('question_options')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="border-top pt-4 mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.apl-2.show', $apl2->skema_id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times mr-2"></i> Batalkan
                        </a>
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle question type change
        $('#question_type').on('change', function() {
            const selectedType = $(this).val();
            const optionsContainer = $('#question-options-container');
            const optionsInput = $('#question_options');

            if (selectedType === 'checkbox' || selectedType === 'radio' || selectedType === 'select') {
                // Show options field for checkbox, radio, and select
                optionsInput.attr('placeholder', 'Pisahkan dengan koma (contoh: Opsi 1, Opsi 2, Opsi 3)');
                optionsContainer.slideDown(200);
            } else {
                // Hide options for text/textarea/file
                optionsContainer.slideUp(200);
            }
        });

        // Initialize on page load
        $('#question_type').trigger('change');
    });
</script>
@endpush
