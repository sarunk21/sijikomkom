@extends('components.templates.master-layout')

@section('title', 'Verifikasi Peserta')
@section('page-title', 'Verifikasi Peserta')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Jadwal Ujian Kompetensi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="jadwalTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Skema</th>
                            <th>TUK</th>
                            <th>Tanggal Ujian</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jadwalList as $jadwal)
                            <tr>
                                <td>{{ $jadwal->skema->nama }}</td>
                                <td>{{ $jadwal->tuk->nama }}</td>
                                <td>{{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('d-m-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($jadwal->tanggal_selesai)->format('d-m-Y') }}</td>
                                <td>
                                    <span class="badge badge-{{ $jadwal->status == 5 ? 'warning' : ($jadwal->status == 6 ? 'info' : 'success') }}">
                                        {{ $jadwal->status_text }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('asesor.verifikasi-peserta.show-asesi', $jadwal->id) }}"
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-users"></i> Lihat Asesi
                                    </a>
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
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#jadwalTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari jadwal...",
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
