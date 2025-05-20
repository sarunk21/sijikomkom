@extends('components.templates.master-layout')

@section('title', 'Informasi Skema')
@section('page-title', 'Informasi Skema')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.skema.create') }}" class="btn btn-dark"><i class="fas fa-plus mr-2"></i> Tambah Skema</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="skemaTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama Skema</th>
                            <th>Kode</th>
                            <th>Kategori Skema</th>
                            <th>Bidang</th>
                            <th class="text-center" style="width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge bg-primary text-white">System Analyst</span></td>
                            <td>SA.006</td>
                            <td>Sertifikasi</td>
                            <td>Sistem Informasi</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                                    <a href="{{ route('admin.skema.edit', 1) }}" class="btn btn-light btn-icon btn-sm border shadow-sm" title="Edit">
                                        <i class="fas fa-pen text-primary"></i>
                                    </a>
                                    <a href="#" class="btn btn-light btn-icon btn-sm border shadow-sm" title="Hapus">
                                        <i class="fas fa-trash text-danger"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <!-- Tambah baris lain jika perlu -->
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
