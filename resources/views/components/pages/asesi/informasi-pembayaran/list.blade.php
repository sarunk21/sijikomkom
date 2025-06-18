@extends('components.templates.master-layout')

@section('title', 'Informasi Pembayaran')
@section('page-title', 'Informasi Pembayaran')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="pendaftaranTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Skema</th>
                            <th>Tanggal Asesmen</th>
                            <th>TUK</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembayaran as $item)
                            <tr>
                                <td>{{ $item->skema->nama_skema }}</td>
                                <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                <td>{{ $item->tuk->nama_tuk }}</td>
                                <td>
                                    @if ($item->status == 1)
                                        <span class="badge badge-success">Aktif</span>
                                    @elseif ($item->status == 2)
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-outline-warning btn-sm shadow-sm">
                                    Bayar
                                    </button>
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
                $('#pendaftaranTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari skema...",
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
