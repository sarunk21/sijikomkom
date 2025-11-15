<x-layout>
    <x-slot name="page_name">Daftar Asesi - {{ $jadwal->skema->nama_skema }}</x-slot>
    <x-slot name="page_desc">{{ $jadwal->tuk }} | {{ $jadwal->tanggal_mulai->format('d/m/Y') }}</x-slot>

    <x-navbar :lists="$lists" :active="$activeMenu"></x-navbar>

    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-users mr-2"></i>Daftar Asesi untuk Pemeriksaan
                </h5>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                @if ($asesis->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>Belum ada asesi yang terdaftar dalam jadwal ini.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Asesi</th>
                                    <th width="15%">NIK</th>
                                    <th width="20%">Status Pemeriksaan</th>
                                    <th width="15%">Hasil Akhir</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($asesis as $index => $asesi)
                                    @php
                                        $penilaian = $asesi->asesiPenilaian->first();
                                        $canGiveHasilAkhir = $penilaian ? $penilaian->canGiveHasilAkhir() : false;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $asesi->name }}</strong>
                                            <br><small class="text-muted">{{ $asesi->email }}</small>
                                        </td>
                                        <td>{{ $asesi->nik ?? '-' }}</td>
                                        <td>
                                            @if ($penilaian)
                                                @php
                                                    $formulirStatus = $penilaian->formulir_status ?? [];
                                                    $totalChecked = collect($formulirStatus)->where('is_checked', true)->count();
                                                    $totalFormulir = count($formulirStatus);
                                                @endphp
                                                @if ($totalFormulir > 0)
                                                    <div class="progress" style="height: 25px;">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: {{ ($totalChecked / $totalFormulir) * 100 }}%">
                                                            {{ $totalChecked }}/{{ $totalFormulir }} Formulir
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="badge badge-secondary">Belum ada formulir</span>
                                                @endif

                                                @if ($penilaian->fr_ai_07_completed)
                                                    <small class="text-success d-block mt-1">
                                                        <i class="fas fa-check-circle mr-1"></i>FR AI 07 Selesai
                                                    </small>
                                                @else
                                                    <small class="text-warning d-block mt-1">
                                                        <i class="fas fa-exclamation-circle mr-1"></i>FR AI 07 Belum
                                                    </small>
                                                @endif
                                            @else
                                                <span class="badge badge-secondary">Belum Diperiksa</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($penilaian && $penilaian->hasil_akhir !== 'belum_dinilai')
                                                @if ($penilaian->hasil_akhir === 'kompeten')
                                                    <span class="badge badge-success badge-lg">
                                                        <i class="fas fa-check-circle mr-1"></i>KOMPETEN
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger badge-lg">
                                                        <i class="fas fa-times-circle mr-1"></i>BELUM KOMPETEN
                                                    </span>
                                                @endif
                                                <br><small class="text-muted">{{ $penilaian->penilaian_at->format('d/m/Y H:i') }}</small>
                                            @else
                                                <span class="badge badge-secondary">Belum Dinilai</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('asesor.pemeriksaan.formulir-list', [$jadwal->id, $asesi->id]) }}"
                                                class="btn btn-sm btn-primary btn-block mb-1">
                                                <i class="fas fa-clipboard-check mr-1"></i>Periksa
                                            </a>

                                            @if ($canGiveHasilAkhir && $penilaian->hasil_akhir === 'belum_dinilai')
                                                <a href="{{ route('asesor.pemeriksaan.penilaian', [$jadwal->id, $asesi->id]) }}"
                                                    class="btn btn-sm btn-success btn-block">
                                                    <i class="fas fa-award mr-1"></i>Beri Nilai
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0 text-primary">{{ $asesis->count() }}</h3>
                                        <small class="text-muted">Total Asesi</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0 text-success">
                                            {{ $asesis->filter(function ($a) {
                                                    $p = $a->asesiPenilaian->first();
                                                    return $p && $p->hasil_akhir === 'kompeten';
                                                })->count() }}
                                        </h3>
                                        <small class="text-muted">Kompeten</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-danger">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0 text-danger">
                                            {{ $asesis->filter(function ($a) {
                                                    $p = $a->asesiPenilaian->first();
                                                    return $p && $p->hasil_akhir === 'belum_kompeten';
                                                })->count() }}
                                        </h3>
                                        <small class="text-muted">Belum Kompeten</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-secondary">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0 text-secondary">
                                            {{ $asesis->filter(function ($a) {
                                                    $p = $a->asesiPenilaian->first();
                                                    return !$p || $p->hasil_akhir === 'belum_dinilai';
                                                })->count() }}
                                        </h3>
                                        <small class="text-muted">Belum Dinilai</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
