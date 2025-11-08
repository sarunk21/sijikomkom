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
                    <label for="question_text" class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                    <input type="text" id="question_text" name="question_text"
                        class="form-control @error('question_text') is-invalid @enderror"
                        placeholder="Isi pertanyaan di sini..." value="{{ old('question_text', $apl2->question_text) }}"
                        required>
                    @error('question_text')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="question_type" class="form-label">Tipe Soal <span class="text-danger">*</span></label>
                    <select id="question_type" name="question_type" class="form-control @error('question_type') is-invalid @enderror" required>
                        <option value="">Pilih Tipe Soal</option>
                        <option value="text" {{ old('question_type', $apl2->question_type) == 'text' ? 'selected' : '' }}>Text</option>
                        <option value="textarea" {{ old('question_type', $apl2->question_type) == 'textarea' ? 'selected' : '' }}>Textarea</option>
                        <option value="checkbox" {{ old('question_type', $apl2->question_type) == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                        <option value="radio" {{ old('question_type', $apl2->question_type) == 'radio' ? 'selected' : '' }}>Radio Button</option>
                        <option value="select" {{ old('question_type', $apl2->question_type) == 'select' ? 'selected' : '' }}>Select</option>
                        <option value="file" {{ old('question_type', $apl2->question_type) == 'file' ? 'selected' : '' }}>File Upload</option>
                        <option value="bk_k_checkbox" {{ old('question_type', $apl2->question_type) == 'bk_k_checkbox' ? 'selected' : '' }}>BK/K Checkbox</option>
                    </select>
                    @error('question_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3" id="question-options-container" style="display: none;">
                    <label for="question_options" class="form-label">Opsi Jawaban</label>
                    <input type="text" id="question_options" name="question_options" class="form-control @error('question_options') is-invalid @enderror"
                        placeholder="Pisahkan dengan koma (contoh: BK, K, Ya, Tidak)" value="{{ old('question_options', is_array($apl2->question_options) ? implode(', ', $apl2->question_options) : $apl2->question_options) }}">
                    <small class="form-text text-muted">Untuk tipe checkbox dan radio, pisahkan opsi dengan koma</small>
                    @error('question_options')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="bukti_isian_tes" class="form-label">Bukti Isian Tes</label>
                    <textarea id="bukti_isian_tes" name="bukti_isian_tes" class="form-control @error('bukti_isian_tes') is-invalid @enderror" rows="3"
                        placeholder="Deskripsi bukti isian tes yang diperlukan...">{{ old('bukti_isian_tes', $apl2->bukti_isian_tes) }}</textarea>
                    @error('bukti_isian_tes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" id="is_bk_k_question" name="is_bk_k_question" class="form-check-input" value="1" {{ old('is_bk_k_question', $apl2->is_bk_k_question) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_bk_k_question">
                            Soal untuk BK/K (Belum Kompeten/Kompeten)
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="urutan" class="form-label">Urutan</label>
                    <input type="number" id="urutan" name="urutan" class="form-control @error('urutan') is-invalid @enderror"
                        placeholder="Urutan soal" value="{{ old('urutan', $apl2->urutan ?? 1) }}" min="1">
                    @error('urutan')
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

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle question type change
        $('#question_type').on('change', function() {
            const selectedType = $(this).val();
            const optionsContainer = $('#question-options-container');
            const optionsInput = $('#question_options');
            const bkKCheckbox = $('#is_bk_k_question');

            if (selectedType === 'bk_k_checkbox') {
                // Auto-fill options for BK/K checkbox
                optionsInput.val('Belum Kompeten,Kompeten');
                bkKCheckbox.prop('checked', true);
                optionsContainer.show();
            } else if (selectedType === 'checkbox' || selectedType === 'radio' || selectedType === 'select') {
                // Show placeholder for other types
                optionsInput.attr('placeholder', 'Pisahkan dengan koma (contoh: Opsi 1, Opsi 2, Opsi 3)');
                optionsContainer.show();
            } else {
                // Clear options for text/textarea/file
                optionsInput.val('');
                bkKCheckbox.prop('checked', false);
                optionsContainer.hide();
            }
        });

        // Initialize on page load
        $('#question_type').trigger('change');
    });
</script>
@endpush
