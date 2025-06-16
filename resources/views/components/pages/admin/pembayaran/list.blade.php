@extends('components.templates.master-layout')

@section('title', 'Informasi Pembayaran')
@section('page-title', 'Informasi Pembayaran')

@section('content')

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="skemaTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Skema</th>
                            <th>Tanggal Pembayaran</th>
                            <th>Bukti Pembayaran</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembayaranAsesi as $item)
                            <tr>
                                <td>{{ $item->user->name }}</td>
                                <td>{{ $item->user->email }}</td>
                                <td>{{ $item->jadwal->skema->nama }}</td>
                                <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                <td>
                                    <a href="{{ asset('storage/' . $item->bukti_pembayaran) }}"
                                        class="btn btn-light btn-icon btn-sm border shadow-sm" title="Lihat Bukti">
                                        <i class="fas fa-eye text-primary"></i>
                                    </a>
                                </td>
                                <td>{{ $item->status_text }}</td>
                                <td class="text-center">
                                    @if ($item->status == 1)
                                        <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                                            <form action="{{ route('admin.pembayaran-asesi.update', $item->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="3">
                                                <button type="submit"
                                                    class="btn btn-light btn-icon btn-sm border shadow-sm" title="Approve">
                                                    <i class="fas fa-check text-success"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.pembayaran-asesi.update', $item->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="2">
                                                <button type="submit"
                                                    class="btn btn-light btn-icon btn-sm border shadow-sm" title="Reject">
                                                    <i class="fas fa-times text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="modal fade" id="rejectModal" tabindex="-1"
                                            aria-labelledby="rejectModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="rejectModalLabel">Reject Pembayaran</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Apakah Anda yakin ingin menolak pembayaran ini?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Tolak</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
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
