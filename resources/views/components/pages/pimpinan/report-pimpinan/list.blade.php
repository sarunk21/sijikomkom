@extends('components.templates.master-layout')

@section('title', 'Report')
@section('page-title', 'Report')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="pendaftaranTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Skema</th>
                            <th>Jumlah Asesi</th>
                            <th>Tanggal</th>
                            <th>Jumlah Kompeten</th>
                            <th>Jumlah Tidak kompeten</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $item)
                            <tr>
                                <td>{{ $item->skema->nama ?? '' }}</td>
                                <td>{{ $item->jumlah_asesi()->count() }}</td>
                                <td>{{ $item->tanggal_ujian }}</td>
                                <td>{{ $item->jumlah_kompeten()->count() }}</td>
                                <td>{{ $item->jumlah_tidak_kompeten()->count() }}</td>
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
                $('#pendaftaranTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari Report Pimpinan...",
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
