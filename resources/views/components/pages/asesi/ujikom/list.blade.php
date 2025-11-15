@extends('components.templates.master-layout')

@section('title', 'Ujikom')
@section('page-title', 'Ujian Kompetensi')

@section('content')
    <div class="card shadow-sm">
        @if (session('success'))
            <div class="alert alert-success mb-3">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif
        <div class="card-body">
            @if ($jadwals->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Belum ada jadwal ujikom yang sedang berlangsung.
                </div>
            @else
                <div class="table-responsive">
                    <table id="ujikomTable" class="table table-striped table-hover align-middle w-100">
                        <thead class="thead-light">
                            <tr>
                                <th>Skema</th>
                                <th>Tanggal Ujian</th>
                                <th>TUK</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jadwals as $jadwal)
                                <tr>
                                    <td>
                                        <strong>{{ $jadwal->skema->nama ?? '-' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $jadwal->skema->kode ?? '' }}</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('d/m/Y') }}</td>
                                    <td>{{ $jadwal->tuk->nama ?? $jadwal->tuk }}</td>
                                    <td>
                                        <span class="badge badge-success">
                                            <i class="fas fa-play-circle me-1"></i>Berlangsung
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('asesi.formulir.index', $jadwal->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-clipboard-list me-1"></i>Lihat Formulir
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
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
                $('#ujikomTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari Ujian Kompetensi...",
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
