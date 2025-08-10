@extends('components.templates.master-layout')

@section('title', 'Upload Sertifikat Bertanda Tangan')
@section('page-title', 'Upload Sertifikat Bertanda Tangan')

@section('content')
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
                                    <a href="{{ asset('storage/sertifikat/' . $item->sertifikat) }}" target="_blank"
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
