@extends('components.templates.master-layout')

@section('title', 'Upload Sertifikat Bertanda Tangan')
@section('page-title', 'Upload Sertifikat Bertanda Tangan')

@section('content')
    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-filter mr-2"></i> Filter Sertifikat
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.upload-sertifikat-admin.index') }}" method="GET" id="filterForm">
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
                        <a href="{{ route('admin.upload-sertifikat-admin.index') }}" class="btn btn-secondary">
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
                <table id="pendaftaranTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama Asesi</th>
                            <th>Skema</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>TUK</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($uploadSertifikat as $item)
                            <tr>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ $item->user->name }} - {{ $item->user->nim }}
                                    </span>
                                </td>
                                <td>{{ $item->skema->nama }}</td>
                                <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                <td>
                                    @php
                                        $textClass = match ($item->status) {
                                            1 => 'warning',
                                            2 => 'success',
                                            3 => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="text-{{ $textClass }}">
                                        {{ $item->status_text }}
                                    </span>
                                </td>
                                <td>{{ $item->pendaftaran->tuk->nama }}</td>
                                <td class="text-center">
                                    {{-- Button Download Sertifikat --}}
                                    <a href="{{ asset('storage/' . $item->sertifikat) }}" target="_blank"
                                        class="btn btn-light btn-icon btn-sm border shadow-sm" title="Download Sertifikat">
                                        <i class="fas fa-download"></i>
                                    </a>

                                    {{-- Button Approve & Reject --}}
                                    <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                                        @if ($item->status == 1)
                                            <form action="{{ route('admin.upload-sertifikat-admin.update', $item->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="2">
                                                <button type="submit"
                                                    class="btn btn-light btn-icon btn-sm border shadow-sm" title="Approve">
                                                    <i class="fas fa-check text-success"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.upload-sertifikat-admin.update', $item->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="3">
                                                <button type="submit"
                                                    class="btn btn-light btn-icon btn-sm border shadow-sm" title="Reject">
                                                    <i class="fas fa-times text-danger"></i>
                                                </button>
                                            </form>
                                        @elseif ($item->status == 2 || $item->status == 3)
                                            -
                                        @endif
                                    </div>
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
                        searchPlaceholder: "Cari sertifikat...",
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
