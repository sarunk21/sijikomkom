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

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="card-title mb-1">
                        <b>{{ $jadwal->skema->nama }}</b> - <b>{{ $jadwal->tuk->nama }}</b>
                    </h5>
                    <p class="card-text text-muted mb-0">
                        {{ $jadwal->tanggal_ujian }}
                    </p>
                </div>

                @php
                    // Check if all asesi have been assessed
                    $totalAsesi = $asesi->count();
                    $assessedAsesi = $asesi->whereIn('status', [4, 5])->count();
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
                           class="btn btn-success shadow-sm">
                            <i class="fas fa-file-alt me-2"></i>Generate FR AK 05
                        </a>
                        <small class="d-block text-muted mt-1">
                            <i class="fas fa-check-circle text-success"></i> Semua asesi telah dinilai
                        </small>
                    </div>
                @elseif($allAssessed && !$frAk05Template)
                    {{-- All asesi assessed BUT template NOT exists --}}
                    <div>
                        <button class="btn btn-secondary shadow-sm" disabled title="Template FR AK 05 belum diupload">
                            <i class="fas fa-file-alt me-2"></i>Generate FR AK 05
                        </button>
                        <small class="d-block text-warning mt-1">
                            <i class="fas fa-exclamation-triangle"></i> Template FR AK 05 belum diupload
                        </small>
                    </div>
                @elseif(!$allAssessed && $frAk05Template)
                    {{-- Template exists BUT not all asesi assessed --}}
                    <div>
                        <button class="btn btn-secondary shadow-sm" disabled title="Belum semua asesi dinilai">
                            <i class="fas fa-file-alt me-2"></i>Generate FR AK 05
                        </button>
                        <small class="d-block text-muted mt-1">
                            <i class="fas fa-info-circle"></i> {{ $assessedAsesi }}/{{ $totalAsesi }} asesi telah dinilai
                        </small>
                    </div>
                @elseif(!$allAssessed && !$frAk05Template)
                    {{-- Template NOT exists AND not all asesi assessed --}}
                    <div>
                        <button class="btn btn-secondary shadow-sm" disabled title="Template belum diupload dan belum semua asesi dinilai">
                            <i class="fas fa-file-alt me-2"></i>Generate FR AK 05
                        </button>
                        <small class="d-block text-muted mt-1">
                            <i class="fas fa-info-circle"></i> {{ $assessedAsesi }}/{{ $totalAsesi }} asesi dinilai | Template belum diupload
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
                            <tr>
                                <td>{{ $item->asesi->name }} - {{ $item->asesi->nim }}</td>
                                <td>{{ $item->status_text }}</td>
                                <td>
                                    @if ($item->status == 3)
                                        <a href="{{ route('asesor.hasil-ujikom.show-jawaban-asesi', $item->pendaftaran->id) }}"
                                            class="btn btn-outline-warning btn-sm shadow-sm">
                                            Mulai Pemeriksaan
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Optional CSS for btn-icon --}}
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
