@extends('components.templates.master-layout')

@section('title', 'APL02 - List Pertanyaan')
@section('page-title', 'List Pertanyaan APL02')

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
            <a href="{{ route('admin.apl-2.create.question', $skema->id) }}" class="btn btn-dark mb-3"><i
                    class="fas fa-plus mr-2"></i> Tambah Pertanyaan</a>

            <div class="table-responsive">
                <table id="apl2Table" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Pertanyaan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questions as $item)
                            <tr>
                                <td class="align-middle">
                                    {{ $item->question_text }}
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                                        <button type="button" class="btn btn-light btn-icon btn-sm border shadow-sm view-detail-btn"
                                            data-id="{{ $item->id }}"
                                            data-question="{{ $item->question_text }}"
                                            data-type="{{ $item->question_type }}"
                                            data-options="{{ is_array($item->question_options) ? implode(', ', $item->question_options) : $item->question_options }}"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye text-info"></i>
                                        </button>

                                        <a href="{{ route('admin.apl-2.edit', $item->id) }}"
                                            class="btn btn-light btn-icon btn-sm border shadow-sm" title="Edit">
                                            <i class="fas fa-pen text-warning"></i>
                                        </a>

                                        <form action="{{ route('admin.apl-2.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light btn-icon btn-sm border shadow-sm" title="Hapus">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if ($questions->isEmpty())
                            <tr>
                                <td colspan="2" class="text-center">Tidak ada pertanyaan</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="detailModalLabel">
                        <i class="fas fa-info-circle mr-2"></i> Detail Pertanyaan APL2
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="font-weight-bold text-dark mb-2">
                            <i class="fas fa-question-circle text-primary mr-1"></i> Pertanyaan:
                        </label>
                        <div class="bg-light p-3 rounded border" id="modal-question"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark mb-2">
                                <i class="fas fa-list-ul text-primary mr-1"></i> Tipe Pertanyaan:
                            </label>
                            <div>
                                <span class="badge bg-primary" id="modal-type"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="modal-options-container" style="display: none;">
                        <label class="font-weight-bold text-dark mb-2">
                            <i class="fas fa-check-square text-primary mr-1"></i> Opsi Jawaban:
                        </label>
                        <div class="bg-light p-3 rounded border" id="modal-options"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Tutup
                    </button>
                    <a href="#" id="modal-edit-btn" class="btn btn-warning">
                        <i class="fas fa-edit mr-1"></i> Edit Pertanyaan
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // View detail button click
        $('.view-detail-btn').on('click', function() {
            const id = $(this).data('id');
            const question = $(this).data('question');
            const type = $(this).data('type');
            const options = $(this).data('options');

            // Set modal content
            $('#modal-question').text(question);

            // Set type badge with proper label
            let typeLabel = type;
            switch(type) {
                case 'text': typeLabel = 'Text'; break;
                case 'textarea': typeLabel = 'Textarea'; break;
                case 'checkbox': typeLabel = 'Checkbox'; break;
                case 'radio': typeLabel = 'Radio Button'; break;
                case 'select': typeLabel = 'Select'; break;
                case 'file': typeLabel = 'File Upload'; break;
            }
            $('#modal-type').text(typeLabel);

            // Show/hide options
            if ((type === 'checkbox' || type === 'radio' || type === 'select') && options) {
                $('#modal-options').text(options);
                $('#modal-options-container').show();
            } else {
                $('#modal-options-container').hide();
            }

            // Set edit button URL
            $('#modal-edit-btn').attr('href', '/admin/apl-2/' + id + '/edit');

            // Show modal
            $('#detailModal').modal('show');
        });
    });
</script>
@endpush
