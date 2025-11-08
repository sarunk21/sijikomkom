@extends('components.templates.master-layout')

@section('title', 'Pembayaran Asesor')
@section('page-title', 'Pembayaran Asesor')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-filter mr-2"></i> Filter Pembayaran Asesor
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pembayaran-asesor.index') }}" method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-semibold">Tanggal Dari</label>
                        <input type="date"
                               name="tanggal_dari"
                               class="form-control"
                               value="{{ request('tanggal_dari') }}"
                               id="tanggal_dari">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-semibold">Tanggal Sampai</label>
                        <input type="date"
                               name="tanggal_sampai"
                               class="form-control"
                               value="{{ request('tanggal_sampai') }}"
                               id="tanggal_sampai">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-semibold">Skema Sertifikasi</label>
                        <select name="skema_id" class="form-control" id="skema_id">
                            <option value="">-- Semua Skema --</option>
                            @foreach($skemas as $skema)
                                <option value="{{ $skema->id }}" {{ request('skema_id') == $skema->id ? 'selected' : '' }}>
                                    {{ $skema->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-2"></i> Filter
                        </button>
                        <a href="{{ route('admin.pembayaran-asesor.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo mr-2"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="skemaTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Skema</th>
                            <th>Tanggal Ujian</th>
                            <th>Bukti Pembayaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembayaranAsesor as $item)
                            <tr>
                                <td>{{ $item->asesor->name }}</td>
                                <td>{{ $item->asesor->email }}</td>
                                <td>{{ $item->jadwal->skema->nama }}</td>
                                <td>{{ $item->jadwal->tanggal_ujian }}</td>
                                <td>
                                    @if($item->bukti_pembayaran)
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.pembayaran-asesor.show', $item->id) }}"
                                                class="btn btn-light btn-icon btn-sm border shadow-sm" title="Lihat Bukti" target="_blank">
                                                <i class="fas fa-eye text-primary"></i>
                                            </a>
                                            <a href="{{ route('admin.pembayaran-asesor.download', $item->id) }}"
                                                class="btn btn-light btn-icon btn-sm border shadow-sm" title="Download Bukti">
                                                <i class="fas fa-download text-success"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted">Belum ada bukti</span>
                                    @endif
                                </td>
                                <td>{{ $item->status_text }}</td>
                                <td>
                                    <a href="{{ route('admin.pembayaran-asesor.edit', $item->id) }}"
                                        class="btn btn-light btn-icon btn-sm border shadow-sm" title="Edit">
                                        <i class="fas fa-edit text-primary"></i>
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
                $('#skemaTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari Pembayaran Asesor...",
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
