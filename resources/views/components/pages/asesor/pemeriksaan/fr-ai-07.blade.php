<x-layout>
    <x-slot name="page_name">FR AI 07 - {{ $asesi->name }}</x-slot>
    <x-slot name="page_desc">Penilaian Asesor | {{ $jadwal->skema->nama_skema }}</x-slot>

    <x-navbar :lists="$lists" :active="$activeMenu"></x-navbar>

    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt mr-2"></i>FR AI 07 - Penilaian Asesor
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>FR AI 07</strong> adalah formulir penilaian asesor yang WAJIB diisi sebelum memberikan penilaian Belum Kompeten (BK) atau Kompeten (K).
                </div>

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

                <form method="POST" action="{{ route('asesor.pemeriksaan.save-fr-ai-07', [$jadwal->id, $asesi->id]) }}">
                    @csrf

                    <!-- Unit Kompetensi -->
                    <h5 class="mb-3">
                        <i class="fas fa-list-check mr-2"></i>1. Penilaian Unit Kompetensi
                    </h5>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Apakah asesi menunjukkan bukti kompetensi pada setiap unit kompetensi?</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="unit_kompeten"
                                                name="fr_ai_07_data[unit_kompetensi]" value="ya"
                                                {{ ($penilaian->fr_ai_07_data['unit_kompetensi'] ?? '') === 'ya' ? 'checked' : '' }} required>
                                            <label class="custom-control-label" for="unit_kompeten">
                                                <i class="fas fa-check-circle text-success mr-1"></i>Ya, semua unit kompetensi terpenuhi
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="unit_belum_kompeten"
                                                name="fr_ai_07_data[unit_kompetensi]" value="tidak"
                                                {{ ($penilaian->fr_ai_07_data['unit_kompetensi'] ?? '') === 'tidak' ? 'checked' : '' }} required>
                                            <label class="custom-control-label" for="unit_belum_kompeten">
                                                <i class="fas fa-times-circle text-danger mr-1"></i>Tidak, ada unit yang belum terpenuhi
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Catatan Unit Kompetensi:</label>
                                <textarea name="fr_ai_07_data[catatan_unit]" class="form-control" rows="3"
                                    placeholder="Jelaskan hasil penilaian unit kompetensi...">{{ $penilaian->fr_ai_07_data['catatan_unit'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Kriteria Unjuk Kerja -->
                    <h5 class="mb-3">
                        <i class="fas fa-clipboard-list mr-2"></i>2. Penilaian Kriteria Unjuk Kerja (KUK)
                    </h5>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Apakah seluruh Kriteria Unjuk Kerja (KUK) dapat didemonstrasikan atau ditunjukkan buktinya?</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="kuk_kompeten"
                                                name="fr_ai_07_data[kriteria_unjuk_kerja]" value="ya"
                                                {{ ($penilaian->fr_ai_07_data['kriteria_unjuk_kerja'] ?? '') === 'ya' ? 'checked' : '' }} required>
                                            <label class="custom-control-label" for="kuk_kompeten">
                                                <i class="fas fa-check-circle text-success mr-1"></i>Ya, semua KUK terpenuhi
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="kuk_belum_kompeten"
                                                name="fr_ai_07_data[kriteria_unjuk_kerja]" value="tidak"
                                                {{ ($penilaian->fr_ai_07_data['kriteria_unjuk_kerja'] ?? '') === 'tidak' ? 'checked' : '' }} required>
                                            <label class="custom-control-label" for="kuk_belum_kompeten">
                                                <i class="fas fa-times-circle text-danger mr-1"></i>Tidak, ada KUK yang belum terpenuhi
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Catatan KUK:</label>
                                <textarea name="fr_ai_07_data[catatan_kuk]" class="form-control" rows="3"
                                    placeholder="Jelaskan hasil penilaian KUK...">{{ $penilaian->fr_ai_07_data['catatan_kuk'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Rekomendasi Perbaikan -->
                    <h5 class="mb-3">
                        <i class="fas fa-lightbulb mr-2"></i>3. Rekomendasi Perbaikan (Jika Ada)
                    </h5>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Saran/Rekomendasi untuk Asesi:</label>
                                <textarea name="fr_ai_07_data[rekomendasi]" class="form-control" rows="4"
                                    placeholder="Berikan rekomendasi perbaikan jika diperlukan...">{{ $penilaian->fr_ai_07_data['rekomendasi'] ?? '' }}</textarea>
                                <small class="text-muted">Opsional - isi jika ada saran perbaikan untuk asesi</small>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Area yang Perlu Ditingkatkan:</label>
                                <textarea name="fr_ai_07_data[area_perbaikan]" class="form-control" rows="3"
                                    placeholder="Sebutkan area yang perlu ditingkatkan...">{{ $penilaian->fr_ai_07_data['area_perbaikan'] ?? '' }}</textarea>
                                <small class="text-muted">Opsional</small>
                            </div>
                        </div>
                    </div>

                    <!-- Kesimpulan Asesor -->
                    <h5 class="mb-3">
                        <i class="fas fa-file-signature mr-2"></i>4. Kesimpulan Asesor
                    </h5>
                    <div class="card mb-4 border-primary">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Kesimpulan Umum:</label>
                                <textarea name="fr_ai_07_data[kesimpulan]" class="form-control" rows="4"
                                    placeholder="Tuliskan kesimpulan umum dari penilaian..." required>{{ $penilaian->fr_ai_07_data['kesimpulan'] ?? '' }}</textarea>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Tanggal Penilaian:</label>
                                <input type="date" name="fr_ai_07_data[tanggal_penilaian]" class="form-control"
                                    value="{{ $penilaian->fr_ai_07_data['tanggal_penilaian'] ?? now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('asesor.pemeriksaan.formulir-list', [$jadwal->id, $asesi->id]) }}"
                            class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save mr-1"></i>Simpan FR AI 07
                        </button>
                    </div>
                </form>

                @if ($penilaian->fr_ai_07_completed)
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle mr-2"></i>
                        FR AI 07 sudah diisi dan tersimpan. Anda dapat mengeditnya kapan saja sebelum memberikan penilaian BK/K.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
