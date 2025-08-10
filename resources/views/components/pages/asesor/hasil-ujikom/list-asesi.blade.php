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

            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">
                    <b>{{ $jadwal->skema->nama }}</b> - <b>{{ $jadwal->tuk->nama }}</b>
                </h5>
                <p class="card-text">
                    {{ $jadwal->tanggal_ujian }}
                </p>
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
