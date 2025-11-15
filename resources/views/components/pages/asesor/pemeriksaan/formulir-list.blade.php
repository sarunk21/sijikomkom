@extends('components.templates.master-layout')

@section('title', 'Daftar Formulir - ' . $asesi->name)
@section('page-title', 'Daftar Formulir - ' . $asesi->name)

@section('content')
        <!-- Info Asesi -->
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-user mr-2"></i>Informasi Asesi</h5>
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
                        <h5 class="mb-3"><i class="fas fa-clipboard-check mr-2"></i>Status Penilaian</h5>
                        <div class="row">
                            <div class="col-6">
                                @php
                                    $totalFormulir = $bankSoals->count();
                                    $selesaiDiperiksa = $responses->filter(fn($r) => $r->is_asesor_completed)->count();
                                @endphp
                                <div class="border rounded p-3 text-center {{ $selesaiDiperiksa === $totalFormulir && $totalFormulir > 0 ? 'bg-success text-white' : 'bg-light' }}">
                                    <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                                    <div><small>Formulir: {{ $selesaiDiperiksa }}/{{ $totalFormulir }}</small></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3 text-center {{ $penilaian->hasil_akhir !== 'belum_dinilai' ? 'bg-success text-white' : 'bg-light' }}">
                                    <i class="fas {{ $penilaian->hasil_akhir !== 'belum_dinilai' ? 'fa-award' : 'fa-hourglass-half' }} fa-2x mb-2"></i>
                                    <div><small>Hasil Akhir</small></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card shadow-sm mb-3">
            <div class="card-body text-center">
                @if ($penilaian->canGiveHasilAkhir())
                    <a href="{{ route('asesor.pemeriksaan.penilaian', [$jadwal->id, $asesi->id]) }}"
                        class="btn btn-success btn-lg">
                        <i class="fas fa-award mr-2"></i>Berikan Penilaian BK/K
                    </a>
                @else
                    <button type="button" class="btn btn-secondary btn-lg" disabled
                        title="Selesaikan semua formulir terlebih dahulu">
                        <i class="fas fa-lock mr-2"></i>Penilaian BK/K (Terkunci)
                    </button>
                @endif
            </div>
        </div>

        <!-- Daftar Formulir -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list mr-2"></i>Daftar Formulir untuk Diperiksa
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

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                @if ($bankSoals->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>Belum ada formulir yang tersedia untuk skema ini.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Formulir</th>
                                    <th width="12%">Target</th>
                                    <th width="15%">Status Asesi</th>
                                    <th width="15%">Status Asesor</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bankSoals as $index => $bankSoal)
                                    @php
                                        $response = $responses->get($bankSoal->id);
                                        $asesiStatus = $response ? $response->status : 'not_started';
                                        $asesorCompleted = $response ? $response->is_asesor_completed : false;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $bankSoal->nama }}</strong>
                                            @if ($bankSoal->keterangan)
                                                <br><small class="text-muted">{{ $bankSoal->keterangan }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ strtoupper($bankSoal->target) }}</span>
                                        </td>
                                        <td>
                                            @if ($bankSoal->target === 'asesor')
                                                <span class="badge badge-info">
                                                    <i class="fas fa-minus mr-1"></i>N/A
                                                </span>
                                                <br><small class="text-muted">Diisi Asesor</small>
                                            @else
                                                @if ($asesiStatus === 'submitted' || $asesiStatus === 'reviewed')
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle mr-1"></i>Submit
                                                    </span>
                                                @elseif ($asesiStatus === 'draft')
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-save mr-1"></i>Draft
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">
                                                        <i class="fas fa-circle mr-1"></i>Belum Isi
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($asesorCompleted)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-double mr-1"></i>Selesai
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-hourglass-half mr-1"></i>Belum
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('asesor.pemeriksaan.review', [$jadwal->id, $asesi->id, $bankSoal->id]) }}"
                                                class="btn btn-sm btn-primary mb-1">
                                                <i class="fas fa-clipboard-check mr-1"></i>
                                                {{ $asesorCompleted ? 'Edit Review' : 'Review' }}
                                            </a>

                                            @if ($response && $response->is_asesor_completed && $bankSoal->file_path)
                                                <a href="{{ route('asesor.pemeriksaan.generate-template', [$jadwal->id, $asesi->id, $bankSoal->id]) }}"
                                                    class="btn btn-sm btn-success">
                                                    <i class="fas fa-file-download mr-1"></i>Generate
                                                </a>
                                            @elseif (!$bankSoal->file_path && $asesorCompleted)
                                                <button class="btn btn-sm btn-secondary" disabled title="Template belum diupload">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>No Template
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <hr class="my-4">
                <a href="{{ route('asesor.hasil-ujikom.show', $jadwal->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali ke Daftar Asesi
                </a>
            </div>
        </div>
@endsection
