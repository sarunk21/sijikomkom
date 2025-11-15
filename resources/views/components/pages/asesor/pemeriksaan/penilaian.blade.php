<x-layout>
    <x-slot name="page_name">Penilaian BK/K - {{ $asesi->name }}</x-slot>
    <x-slot name="page_desc">Hasil Akhir Penilaian | {{ $jadwal->skema->nama_skema }}</x-slot>

    <x-navbar :lists="$lists" :active="$activeMenu"></x-navbar>

    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-award mr-2"></i>Penilaian Belum Kompeten (BK) / Kompeten (K)
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle mr-2"></i>
                    <strong>Semua syarat terpenuhi!</strong> Anda dapat memberikan penilaian akhir BK/K untuk asesi ini.
                </div>

                <!-- Info Asesi -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Informasi Asesi</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Nama</strong></td>
                                <td>: {{ $asesi->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>NIK</strong></td>
                                <td>: {{ $asesi->nik ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                                <td>: {{ $asesi->email }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Informasi Skema</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Skema</strong></td>
                                <td>: {{ $jadwal->skema->nama_skema }}</td>
                            </tr>
                            <tr>
                                <td><strong>TUK</strong></td>
                                <td>: {{ $jadwal->tuk }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal</strong></td>
                                <td>: {{ $jadwal->tanggal_mulai->format('d/m/Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Ringkasan Penilaian -->
                <h5 class="mb-3">
                    <i class="fas fa-clipboard-check mr-2"></i>Ringkasan Penilaian
                </h5>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card border-success">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-success">
                                    <i class="fas fa-check-circle mr-2"></i>Status Formulir
                                </h6>
                                @php
                                    $formulirStatus = $penilaian->formulir_status ?? [];
                                    $totalChecked = collect($formulirStatus)->where('is_checked', true)->count();
                                    $totalValid = collect($formulirStatus)->where('is_valid', true)->count();
                                    $totalFormulir = count($formulirStatus);
                                @endphp
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <div class="text-center p-3 border rounded bg-light">
                                            <h3 class="mb-0 text-primary">{{ $totalFormulir }}</h3>
                                            <small class="text-muted">Total Formulir</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center p-3 border rounded bg-light">
                                            <h3 class="mb-0 text-success">{{ $totalChecked }}</h3>
                                            <small class="text-muted">Sudah Diperiksa</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center p-3 border rounded bg-light">
                                            <h3 class="mb-0 text-success">{{ $totalValid }}</h3>
                                            <small class="text-muted">Valid/Sesuai</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card border-success">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-success">
                                    <i class="fas fa-file-alt mr-2"></i>FR AI 07
                                </h6>
                                <p class="mb-0">
                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                    FR AI 07 sudah diisi dan lengkap
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan FR AI 07 -->
                @if ($penilaian->fr_ai_07_data)
                    <h5 class="mb-3">
                        <i class="fas fa-file-signature mr-2"></i>Ringkasan FR AI 07
                    </h5>
                    <div class="card mb-4 border-info">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p>
                                        <strong>Unit Kompetensi:</strong>
                                        @if (($penilaian->fr_ai_07_data['unit_kompetensi'] ?? '') === 'ya')
                                            <span class="badge badge-success">Terpenuhi</span>
                                        @else
                                            <span class="badge badge-danger">Belum Terpenuhi</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p>
                                        <strong>Kriteria Unjuk Kerja:</strong>
                                        @if (($penilaian->fr_ai_07_data['kriteria_unjuk_kerja'] ?? '') === 'ya')
                                            <span class="badge badge-success">Terpenuhi</span>
                                        @else
                                            <span class="badge badge-danger">Belum Terpenuhi</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if (isset($penilaian->fr_ai_07_data['kesimpulan']))
                                <hr>
                                <p class="mb-0">
                                    <strong>Kesimpulan:</strong><br>
                                    {{ $penilaian->fr_ai_07_data['kesimpulan'] }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                <hr class="my-4">

                <!-- Form Penilaian -->
                <h5 class="mb-3">
                    <i class="fas fa-gavel mr-2"></i>Keputusan Penilaian Akhir
                </h5>

                <form method="POST" action="{{ route('asesor.pemeriksaan.save-penilaian', [$jadwal->id, $asesi->id]) }}">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="font-weight-bold mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Terdapat kesalahan:
                            </h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="card border-warning mb-4">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold h5">Hasil Akhir Penilaian: <span class="text-danger">*</span></label>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="card border-success hover-shadow" style="cursor: pointer;" onclick="selectHasil('kompeten')">
                                            <div class="card-body text-center p-4">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" id="hasil_kompeten"
                                                        name="hasil_akhir" value="kompeten" required>
                                                    <label class="custom-control-label d-block" for="hasil_kompeten" style="cursor: pointer;">
                                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                                        <h4 class="mb-0 text-success">KOMPETEN</h4>
                                                        <small class="text-muted">Asesi memenuhi seluruh kompetensi yang diujikan</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-danger hover-shadow" style="cursor: pointer;" onclick="selectHasil('belum_kompeten')">
                                            <div class="card-body text-center p-4">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" id="hasil_belum_kompeten"
                                                        name="hasil_akhir" value="belum_kompeten" required>
                                                    <label class="custom-control-label d-block" for="hasil_belum_kompeten" style="cursor: pointer;">
                                                        <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                                                        <h4 class="mb-0 text-danger">BELUM KOMPETEN</h4>
                                                        <small class="text-muted">Asesi belum memenuhi seluruh kompetensi yang diujikan</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <label class="font-weight-bold">Catatan Asesor:</label>
                                <textarea name="catatan_asesor" class="form-control" rows="5"
                                    placeholder="Tuliskan catatan atau alasan keputusan penilaian (opsional)...">{{ old('catatan_asesor', $penilaian->catatan_asesor ?? '') }}</textarea>
                                <small class="text-muted">Opsional - berikan penjelasan jika diperlukan</small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Keputusan penilaian ini bersifat final dan akan dicatat dalam sistem. Pastikan Anda sudah memeriksa semua formulir dengan teliti.
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('asesor.pemeriksaan.formulir-list', [$jadwal->id, $asesi->id]) }}"
                            class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Apakah Anda yakin dengan keputusan penilaian ini? Keputusan ini bersifat final.')">
                            <i class="fas fa-gavel mr-1"></i>Simpan Penilaian Final
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .hover-shadow:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
    </style>

    <script>
        function selectHasil(value) {
            document.getElementById('hasil_' + value).checked = true;
        }
    </script>
</x-layout>
