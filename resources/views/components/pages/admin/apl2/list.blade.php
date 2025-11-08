@extends('components.templates.master-layout')

@section('title', 'Bank Soal')
@section('page-title', 'Bank Soal')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="skemaTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Skema</th>
                            <th class="text-center">Jumlah Pertanyaan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($skema as $item)
                            <tr>
                                <td class="align-middle">
                                    <span class="badge badge-secondary">
                                    {{ $item->nama }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">{{ $item->apl2->count() ?? 0 }}</td>
                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                                        <a href="{{ route('admin.apl-2.show-by-skema', $item->id) }}" class="btn btn-light btn-icon btn-sm border shadow-sm" title="Lihat Pertanyaan">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>
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
                $('#skemaTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari Bank Soal...",
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
