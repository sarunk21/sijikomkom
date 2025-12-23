@extends('components.templates.master-layout')

@section('title', 'Report - Asesi Kompeten')
@section('page-title', 'Report - Asesi Kompeten')

@section('content')
    {{-- Filter Card --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">
            <h6 class="mb-0">Filter Data</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('pimpinan.report-pimpinan.list-nama-kompeten', $id) }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="asesor_id">Asesor</label>
                            <select class="form-control" id="asesor_id" name="asesor_id">
                                <option value="">Semua Asesor</option>
                                @foreach($asesors as $asesor)
                                    <option value="{{ $asesor->id }}" {{ request('asesor_id') == $asesor->id ? 'selected' : '' }}>
                                        {{ $asesor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter mr-1"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('pimpinan.report-pimpinan.list-nama-kompeten', $id) }}" class="btn btn-secondary">
                            <i class="fas fa-redo mr-1"></i> Reset Filter
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Daftar Asesi Kompeten</h6>
                <a href="{{ route('pimpinan.report-pimpinan.list-nama-kompeten.export-excel', $id) . (request('asesor_id') ? '?asesor_id=' . request('asesor_id') : '') }}" class="btn btn-success">
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
                            <th>Asesor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $item)
                            <tr>
                                <td>{{ $item['skema'] }}</td>
                                <td>{{ $item['nama'] }}</td>
                                <td>{{ $item['nim'] }}</td>
                                <td>{{ $item['asesor'] ?: '-' }}</td>
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
                    ordering: false,
                    language: {
                        searchPlaceholder: "Cari Report...",
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
