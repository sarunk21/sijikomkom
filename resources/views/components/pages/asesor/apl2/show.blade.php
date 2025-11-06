@extends('components.templates.master-layout')

@section('title', 'APL2 - Review Portofolio')
@section('page-title', 'APL2 - Review Portofolio')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0">APL2 - Review Portofolio</h4>
                <p class="text-muted mb-0">Review dan nilai portofolio asesi</p>
            </div>
            <div>
                <button type="button" class="btn btn-info me-2" id="preview-btn">
                    <i class="fas fa-eye"></i> Preview Data
                </button>
                <a href="{{ route('asesor.apl2.export-docx', $pendaftaran->id) }}" class="btn btn-success">
                    <i class="fas fa-download"></i> Export DOCX
                </a>
            </div>
        </div>

        <!-- Form Review APL2 -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clipboard-check"></i> Review APL2 - Portofolio
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('asesor.apl2.update', $pendaftaran->id) }}" method="POST" id="apl2-review-form">
                    @csrf
                    @method('PUT')

                    <!-- Informasi Asesi -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary">Informasi Asesi</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Nama:</strong></td>
                                    <td>{{ $pendaftaran->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $pendaftaran->user->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Skema:</strong></td>
                                    <td>{{ $pendaftaran->skema->nama ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if(!empty($pendaftaran->custom_variables))
                                            <span class="badge badge-success">Sudah Mengisi</span>
                                        @else
                                            <span class="badge badge-warning">Belum Mengisi</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Petunjuk Penilaian</h6>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Petunjuk:</strong>
                                <ul class="mb-0 mt-2">
                                    <li><strong>BK</strong> = Belum Kompeten</li>
                                    <li><strong>K</strong> = Kompeten</li>
                                    <li>Beri catatan jika diperlukan</li>
                                    <li>Pastikan semua soal dinilai</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Review Custom Variables APL2 -->
                    @if($template->custom_variables && count($template->custom_variables) > 0)
                        @foreach($template->custom_variables as $index => $variable)
                            @php
                                // Filter by role - hanya tampilkan untuk asesor atau both
                                $variableRole = $variable['role'] ?? 'asesi';
                                if ($variableRole !== 'asesor' && $variableRole !== 'both') {
                                    continue;
                                }
                            @endphp
                            <div class="question-section border p-4 mb-4 rounded">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="text-primary mb-3">
                                            Pertanyaan {{ $index + 1 }}: {{ $variable['label'] }}
                                        </h6>

                                        <!-- Jawaban Asesi -->
                                        @if($pendaftaran->custom_variables && isset($pendaftaran->custom_variables[$variable['name']]))
                                            <div class="mb-3">
                                                <h6 class="text-success">Jawaban Asesi:</h6>
                                                <div class="bg-light p-3 rounded">
                                                    <p><strong>Jawaban:</strong> {{ $pendaftaran->custom_variables[$variable['name']] }}</p>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Asesi belum mengisi jawaban untuk pertanyaan ini.
                                            </div>
                                        @endif

                                        <!-- Penilaian Asesor -->
                                        <div class="mb-3">
                                            <h6 class="text-primary">Penilaian Asesor:</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="assessments[{{ $variable['name'] }}]"
                                                            value="BK"
                                                            id="asesor_bk_{{ $variable['name'] }}"
                                                            {{ old('assessments.' . $variable['name']) == 'BK' || (($pendaftaran->asesor_assessment ?? [])[$variable['name']]['assessment'] ?? '') == 'BK' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="asesor_bk_{{ $variable['name'] }}">
                                                            <strong>BK</strong> - Belum Kompeten
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="assessments[{{ $variable['name'] }}]"
                                                            value="K"
                                                            id="asesor_k_{{ $variable['name'] }}"
                                                            {{ old('assessments.' . $variable['name']) == 'K' || (($pendaftaran->asesor_assessment ?? [])[$variable['name']]['assessment'] ?? '') == 'K' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="asesor_k_{{ $variable['name'] }}">
                                                            <strong>K</strong> - Kompeten
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Catatan Asesor:</label>
                                                    <textarea name="notes[{{ $variable['name'] }}]" class="form-control" rows="3"
                                                        placeholder="Tambahkan catatan atau feedback...">{{ old('notes.' . $variable['name']) ?? (($pendaftaran->asesor_assessment ?? [])[$variable['name']]['notes'] ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Tidak ada pertanyaan yang dikonfigurasi untuk template APL2 ini.
                        </div>
                    @endif

                    <!-- Digital Signature Section -->
                    <div class="border p-4 mb-4 rounded">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-signature"></i> Tanda Tangan Digital Asesor
                        </h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="signature-pad-container">
                                    <canvas id="signature-pad" width="600" height="200" class="border"></canvas>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-signature">
                                            <i class="fas fa-eraser"></i> Hapus Tanda Tangan
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Petunjuk:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Gunakan mouse atau touch untuk menandatangani</li>
                                        <li>Pastikan tanda tangan jelas dan terbaca</li>
                                        <li>Tanda tangan akan tersimpan secara digital</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="signature" id="signature-data">
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('asesor.apl2.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Penilaian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .question-section {
            background-color: #f8f9fa;
        }

        .signature-pad-container {
            background-color: white;
            border-radius: 8px;
            padding: 10px;
        }

        #signature-pad {
            cursor: crosshair;
            background-color: white;
        }

        .form-check-label {
            font-weight: normal;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize signature pad
            const canvas = document.getElementById('signature-pad');
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgb(0, 0, 0)'
            });

            // Clear signature
            $('#clear-signature').on('click', function() {
                signaturePad.clear();
            });

            // Handle form submission
            $('#apl2-review-form').on('submit', function(e) {
                // Get signature data
                if (!signaturePad.isEmpty()) {
                    $('#signature-data').val(signaturePad.toDataURL());
                }

                // Validate that all questions are assessed
                let allAssessed = true;
                $('input[name*="assessments"]').each(function() {
                    const questionId = $(this).attr('name').match(/\[(\d+)\]/)[1];
                    const hasAssessment = $(`input[name="assessments[${questionId}]"]:checked`).length > 0;
                    if (!hasAssessment) {
                        allAssessed = false;
                        $(this).closest('.question-section').addClass('border-danger');
                    } else {
                        $(this).closest('.question-section').removeClass('border-danger');
                    }
                });

                if (!allAssessed) {
                    e.preventDefault();
                    alert('Silakan berikan penilaian untuk semua soal sebelum menyimpan.');
                    return false;
                }
            });

            // Auto-save functionality (optional)
            let autoSaveTimeout;
            $('input, textarea, select').on('input change', function() {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(function() {
                    // Auto-save logic here if needed
                    console.log('Auto-saving review...');
                }, 5000);
            });
        });

        // Preview functionality
        $('#preview-btn').on('click', function() {
            $('#previewModal').modal('show');
            $('#preview-content').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

            $.ajax({
                url: '{{ route("asesor.apl2.preview-data", $pendaftaran->id) }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        let content = '<div class="preview-data">';
                        content += '<h6>Data Pendaftaran:</h6>';
                        content += '<p><strong>Nama:</strong> ' + response.pendaftaran.user_name + '</p>';
                        content += '<p><strong>Skema:</strong> ' + response.pendaftaran.skema_name + '</p>';
                        content += '<hr>';
                        content += '<h6>Data Template:</h6>';

                        if (response.data.soal_apl2) {
                            content += '<h6>Soal APL2:</h6>';
                            content += '<pre class="bg-light p-3">' + response.data.soal_apl2 + '</pre>';
                        }

                        if (response.data.jawaban_apl2) {
                            content += '<h6>Jawaban Asesi:</h6>';
                            content += '<pre class="bg-light p-3">' + response.data.jawaban_apl2 + '</pre>';
                        }

                        if (response.data.asesor_penilaian) {
                            content += '<h6>Penilaian Asesor:</h6>';
                            content += '<pre class="bg-light p-3">' + response.data.asesor_penilaian + '</pre>';
                        }

                        content += '</div>';
                        $('#preview-content').html(content);
                    } else {
                        $('#preview-content').html('<div class="alert alert-danger">' + response.error + '</div>');
                    }
                },
                error: function() {
                    $('#preview-content').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat preview data.</div>');
                }
            });
        });
    </script>
    @endpush

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview Data APL2</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="preview-content">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
