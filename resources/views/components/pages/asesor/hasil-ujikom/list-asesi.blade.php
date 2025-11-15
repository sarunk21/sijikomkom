@extends('components.templates.master-layout')

@section('title', 'Asesmen')
@section('page-title', 'Asesmen')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                <div>
                    <h5 class="card-title mb-1">
                        <b>{{ $jadwal->skema->nama }}</b> - <b>{{ $jadwal->tuk->nama }}</b>
                    </h5>
                    <p class="card-text text-muted mb-0">
                        <i class="fas fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('d/m/Y') }}
                        <span class="mx-2">|</span>
                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $jadwal->tuk->nama ?? '-' }}
                    </p>
                </div>

                @php
                    // Get asesi IDs dari jadwal ini
                    $asesiIds = $asesi->pluck('asesi_id');
                    
                    // Check if all asesi have been assessed menggunakan AsesiPenilaian (sistem baru)
                    $totalAsesi = $asesi->count();
                    $assessedAsesi = \App\Models\AsesiPenilaian::where('jadwal_id', $jadwal->id)
                        ->whereIn('user_id', $asesiIds)
                        ->where('hasil_akhir', '!=', 'belum_dinilai')
                        ->count();
                    $allAssessed = $totalAsesi > 0 && $totalAsesi == $assessedAsesi;

                    // Check if FR AK 05 template exists for this skema
                    $frAk05Template = \App\Models\TemplateMaster::where('skema_id', $jadwal->skema_id)
                        ->where('tipe_template', 'FR_AK_05')
                        ->where('is_active', true)
                        ->first();
                @endphp

                @if($allAssessed && $frAk05Template)
                    {{-- All asesi assessed AND template exists --}}
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
                    {{-- All asesi assessed BUT template NOT exists --}}
                    <div>
                        <button class="btn btn-secondary btn-lg shadow-sm" disabled title="Template FR AK 05 belum diupload">
                            <i class="fas fa-file-alt mr-2"></i>Generate FR AK 05
                        </button>
                        <small class="d-block text-warning mt-1">
                            <i class="fas fa-exclamation-triangle"></i> Template FR AK 05 belum diupload
                        </small>
                    </div>
                @elseif(!$allAssessed && $frAk05Template)
                    {{-- Template exists BUT not all asesi assessed --}}
                    <div>
                        <button class="btn btn-secondary btn-lg shadow-sm" disabled title="Belum semua asesi dinilai">
                            <i class="fas fa-file-alt mr-2"></i>Generate FR AK 05
                        </button>
                        <small class="d-block text-muted mt-1">
                            <i class="fas fa-info-circle"></i> {{ $assessedAsesi }}/{{ $totalAsesi }} asesi telah dinilai
                        </small>
                    </div>
                @elseif(!$allAssessed && !$frAk05Template)
                    {{-- Template NOT exists AND not all asesi assessed --}}
                    <div>
                        <button class="btn btn-secondary btn-lg shadow-sm" disabled title="Template belum diupload dan belum semua asesi dinilai">
                            <i class="fas fa-file-alt mr-2"></i>Generate FR AK 05
                        </button>
                        <small class="d-block text-muted mt-1">
                            <i class="fas fa-info-circle"></i> {{ $assessedAsesi }}/{{ $totalAsesi }} dinilai | Template belum diupload
                        </small>
                    </div>
                @endif
            </div>

            <div class="table-responsive">
                <table id="asesiTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama Asesi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($asesi as $item)
                            @php
                                // Get penilaian dari sistem baru
                                $penilaian = \App\Models\AsesiPenilaian::where('jadwal_id', $jadwal->id)
                                    ->where('user_id', $item->asesi_id)
                                    ->first();
                                
                                // Hitung progress formulir
                                $formulirStatus = $penilaian ? ($penilaian->formulir_status ?? []) : [];
                                $totalChecked = collect($formulirStatus)->where('is_checked', true)->count();
                                $totalFormulir = count($formulirStatus);
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $item->asesi->name }}</strong>
                                    <br><small class="text-muted">{{ $item->asesi->nim ?? '-' }}</small>
                                </td>
                                <td>
                                    @if ($penilaian && $penilaian->hasil_akhir !== 'belum_dinilai')
                                        {{-- Sudah dinilai --}}
                                        <span class="badge badge-{{ $penilaian->hasil_akhir === 'kompeten' ? 'success' : 'danger' }} badge-lg">
                                            <i class="fas fa-{{ $penilaian->hasil_akhir === 'kompeten' ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                            {{ $penilaian->hasil_akhir === 'kompeten' ? 'KOMPETEN' : 'BELUM KOMPETEN' }}
                                        </span>
                                        <br><small class="text-muted">{{ $penilaian->penilaian_at ? $penilaian->penilaian_at->format('d/m/Y H:i') : '-' }}</small>
                                    @elseif ($totalFormulir > 0)
                                        {{-- Sedang dalam pemeriksaan --}}
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar {{ $totalChecked === $totalFormulir ? 'bg-success' : 'bg-warning' }}" 
                                                 role="progressbar" style="width: {{ ($totalChecked / $totalFormulir) * 100 }}%">
                                                {{ $totalChecked }}/{{ $totalFormulir }} Formulir
                                            </div>
                                        </div>
                                    @elseif ($item->status == 3)
                                        <span class="badge badge-warning">Ujikom Selesai</span>
                                    @elseif ($item->status == 2)
                                        <span class="badge badge-info">Ujikom Berlangsung</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $item->status_text }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($penilaian && $penilaian->hasil_akhir !== 'belum_dinilai')
                                        <a href="{{ route('asesor.pemeriksaan.formulir-list', [$jadwal->id, $item->asesi_id]) }}"
                                            class="btn btn-outline-secondary btn-sm shadow-sm">
                                            <i class="fas fa-eye"></i> Lihat Hasil
                                        </a>
                                    @elseif (in_array($item->status, [2, 3]))
                                        {{-- Sistem Bank Soal (Baru) --}}
                                        <a href="{{ route('asesor.pemeriksaan.formulir-list', [$jadwal->id, $item->asesi_id]) }}"
                                            class="btn btn-outline-primary btn-sm shadow-sm">
                                            <i class="fas fa-clipboard-check"></i> Mulai Pemeriksaan
                                        </a>
                                    @else
                                        <span class="text-muted small">Belum dapat diperiksa</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Optional CSS for btn-icon and badge --}}
    <style>
        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            padding: 0;
            line-height: 1;
            font-size: 0.85rem;
        }
        .badge-lg {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }
    </style>

    {{-- Scripts --}}
    @push('scripts')
        <!-- DataTables sudah dimuat global dari layout -->
        <script>
            $(document).ready(function() {
                $('#asesiTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari asesi...",
                        search: "",
                        lengthMenu: "_MENU_ data per halaman",
                        zeroRecords: "Data tidak ditemukan",
                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                        infoEmpty: "Tidak ada data",
                        paginate: {
                            previous: "Sebelumnya",
                            next: "Berikutnya"
                        }
                    },
                    columnDefs: [{
                        targets: -1,
                        orderable: false
                    }]
                });
            });
        </script>
    @endpush
@endsection
