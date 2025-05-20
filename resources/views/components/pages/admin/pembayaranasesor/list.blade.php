@extends('components.templates.master-layout')

@section('title', 'Pembayaran Asesor')
@section('page-title', 'Pembayaran Asesor')

@section('content')
    <div class="mb-3">
        <a href="#" class="btn btn-dark"><i class="fas fa-plus mr-2"></i> Upload Bukti Pembayaran</a>
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
                            <th>TUK</th>
                            <th>Bukti Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Asesi 1</td>
                            <td>asesi1@gmail.com</td>
                            <td>System Analyst</td>
                            <td>2025-01-01</td>
                            <td>TUK 1</td>
                            <td>
                                <a href="#" class="btn btn-light btn-icon btn-sm border shadow-sm"
                                    title="Lihat Bukti">
                                    <i class="fas fa-eye text-primary"></i>
                                </a>
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
                $('#skemaTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari TUK...",
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
