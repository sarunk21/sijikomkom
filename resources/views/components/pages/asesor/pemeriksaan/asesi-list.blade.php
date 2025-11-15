@extends('components.templates.master-layout')

@section('title', 'Daftar Asesi - ' . $jadwal->skema->nama)
@section('page-title', 'Daftar Asesi - ' . $jadwal->skema->nama)

@section('content')
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

                <!-- Info Jadwal & Generate FR AK 05 -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <h6 class="mb-1"><strong>{{ $jadwal->skema->nama }}</strong></h6>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('d/m/Y') }}
                            <span class="mx-2">|</span>
                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $jadwal->tuk->nama ?? '-' }}
                        </p>
                    </div>

                    @php
                        // Check if all asesi have been assessed
                        $totalAsesi = $asesis->count();
                        $assessedAsesi = $asesis->filter(function($a) {
                            $p = $a->asesiPenilaian->first();
                            return $p && $p->hasil_akhir !== 'belum_dinilai';
                        })->count();
                        $allAssessed = $totalAsesi > 0 && $totalAsesi == $assessedAsesi;

                        // Check if FR AK 05 template exists for this skema
                        $frAk05Template = \App\Models\TemplateMaster::where('skema_id', $jadwal->skema_id)
                            ->where('tipe_template', 'FR_AK_05')
                            ->where('is_active', true)
                            ->first();
                    @endphp

                    @if($allAssessed && $frAk05Template)
                        <div>
                            <a href="{{ route('asesor.fr-ak-05.form', $jadwal->id) }}"
                               class="btn btn-success btn-lg shadow-sm">
                                <i class="fas fa-file-alt mr-2"></i>Generate FR AK 05
                            </a>
                            <small class="d-block text-success mt-1">
                                <i class="fas fa-check-circle"></i> {{ $totalAsesi }} asesi telah dinilai
                            </small>
                        </div>
                    @elseif($allAssessed && !$frAk05Template)
                        <div>
                            <button class="btn btn-secondary btn-lg shadow-sm" disabled title="Template FR AK 05 belum diupload">
                                <i class="fas fa-file-alt mr-2"></i>Generate FR AK 05
                            </button>
                            <small class="d-block text-warning mt-1">
                                <i class="fas fa-exclamation-triangle"></i> Template FR AK 05 belum diupload
                            </small>
                        </div>
                    @elseif(!$allAssessed && $frAk05Template)
                        <div>
                            <button class="btn btn-secondary btn-lg shadow-sm" disabled title="Belum semua asesi dinilai">
                                <i class="fas fa-file-alt mr-2"></i>Generate FR AK 05
                            </button>
                            <small class="d-block text-muted mt-1">
                                <i class="fas fa-info-circle"></i> {{ $assessedAsesi }}/{{ $totalAsesi }} asesi telah dinilai
                            </small>
                        </div>
                    @else
                        <div>
                            <button class="btn btn-secondary btn-lg shadow-sm" disabled title="Template belum diupload dan belum semua asesi dinilai">
                                <i class="fas fa-file-alt mr-2"></i>Generate FR AK 05
                            </button>
                            <small class="d-block text-muted mt-1">
                                <i class="fas fa-info-circle"></i> {{ $assessedAsesi }}/{{ $totalAsesi }} dinilai | Template belum ada
                            </small>
                        </div>
                    @endif
                </div>

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
                                                        <div class="progress-bar {{ $totalChecked === $totalFormulir ? 'bg-success' : 'bg-warning' }}" role="progressbar"
                                                            style="width: {{ ($totalChecked / $totalFormulir) * 100 }}%">
                                                            {{ $totalChecked }}/{{ $totalFormulir }} Formulir
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="badge badge-secondary">Belum ada formulir</span>
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
@endsection
