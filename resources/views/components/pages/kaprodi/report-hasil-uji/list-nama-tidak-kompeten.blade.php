@extends('components.templates.master-layout')

@section('title', 'Report Hasil Ujikom - Nama Tidak Kompeten')
@section('page-title', 'Report Hasil Ujikom - Nama Tidak Kompeten')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Daftar Asesi Tidak Kompeten</h6>
                <a href="{{ route('kaprodi.report-hasil-uji.list-nama-tidak-kompeten.export-excel', request()->route('id')) }}" class="btn btn-success">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
            </div>
            <div class="table-responsive">
                <table id="reportTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Skema</th>
                            <th>Nama</th>
                            <th>NIM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $item)
                            <tr>
                                <td>{{ $item['skema'] }}</td>
                                <td>{{ $item['nama'] }}</td>
                                <td>{{ $item['nim'] }}</td>
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
                $('#reportTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari Report Hasil Ujikom...",
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
