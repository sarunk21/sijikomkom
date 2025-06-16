@extends('components.templates.master-layout')

@section('title', 'Asesmen')
@section('page-title', 'Asesmen')

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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- @foreach ($hasilUjikom as $item)
                            <tr>
                                <td>{{ $item->skema->nama_skema }}</td>
                                <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                <td>
                                    <span class="badge badge-success">
                                        @if ($item->verif_stage == 1)
                                            Verifikasi
                                        @elseif ($item->verif_stage == 2)
                                            Verifikasi
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $item->tuk->nama_tuk }}</td>
                                <td>
                                    <span>-</span>
                                </td>
                            </tr>
                        @endforeach -->
                        <tr>
                            <td>
                                <span class="badge badge-secondary">
                                    Asesi 1
                                </span>
                            </td>
                            <td>System Analyst</td>
                            <td>20/11/2024</td>
                            <td>
                                <span class="text-success">
                                Kompeten
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-outline-warning btn-sm shadow-sm">
                                   Mulai
                                </button>
                            </td>
                        </tr>
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
