@extends('components.templates.master-layout')

@section('title', 'Report')
@section('page-title', 'Report')

@section('content')
    {{-- Filter Card --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">
            <h6 class="mb-0">Filter Data</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('pimpinan.report-pimpinan.index') }}">
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
                            <label for="tuk_id">TUK</label>
                            <select class="form-control" id="tuk_id" name="tuk_id">
                                <option value="">Semua TUK</option>
                                @foreach($tuks as $tuk)
                                    <option value="{{ $tuk->id }}" {{ request('tuk_id') == $tuk->id ? 'selected' : '' }}>
                                        {{ $tuk->nama }}
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
                        <a href="{{ route('pimpinan.report-pimpinan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo mr-1"></i> Reset Filter
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
                                <td>
                                    <a href="{{ route('pimpinan.report-pimpinan.list-nama-kompeten', $item->id) }}" class="btn btn-primary btn-sm">
                                        {{ $item->jumlah_kompeten()->count() }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('pimpinan.report-pimpinan.list-nama-tidak-kompeten', $item->id) }}" class="btn btn-primary btn-sm">
                                        {{ $item->jumlah_tidak_kompeten()->count() }}
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
