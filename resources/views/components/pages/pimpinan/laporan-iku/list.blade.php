@extends('components.templates.master-layout')

@section('title', 'Laporan IKU 2')
@section('page-title', 'Laporan IKU 2')

@section('content')
    {{-- Filter Card --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">
            <h6 class="mb-0">Filter Data</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('pimpinan.laporan-iku.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="skema_id">Skema</label>
                            <select class="form-control" id="skema_id" name="skema_id">
                                <option value="">Semua Skema</option>
                                @foreach($skemas as $skema)
                                    <option value="{{ $skema->id }}" {{ request('skema_id') == $skema->id ? 'selected' : '' }}>
                                        {{ $skema->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
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
                        <a href="{{ route('pimpinan.laporan-iku.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo mr-1"></i> Reset Filter
                        </a>
                        <a href="{{ route('pimpinan.laporan-iku.export-excel', request()->query()) }}" class="btn btn-success">
                            <i class="fas fa-file-excel mr-1"></i> Export Excel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table Card --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="reportTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Skema</th>
                            <th>Prodi</th>
                            <th>Asesor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $item)
                            <tr>
                                <td>{{ $item->user->nim }}</td>
                                <td>{{ $item->user->name }}</td>
                                <td>{{ $item->skema->nama }}</td>
                                <td>{{ $item->user->jurusan }}</td>
                                <td>{{ $item->pendaftaran && $item->pendaftaran->pendaftaranUjikom && $item->pendaftaran->pendaftaranUjikom->asesor ? $item->pendaftaran->pendaftaranUjikom->asesor->name : '-' }}</td>
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
                        searchPlaceholder: "Cari Laporan IKU 2...",
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
