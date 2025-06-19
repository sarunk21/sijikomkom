@extends('components.templates.master-layout')

@section('title', 'Verifikasi Pendaftaran')
@section('page-title', 'Verifikasi Pendaftaran')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">

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
                        @foreach ($verfikasiPendaftaran as $item)
                            <tr>
                                <td>{{ $item->user->name }} - {{ $item->user->nim }}</td>
                                <td>{{ $item->skema->nama }}</td>
                                <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                <td>
                                    @if ($item->status == 1)
                                        <span class="text-success">{{ $item->status_text }}</span>
                                    @elseif ($item->status == 2)
                                        <span class="text-warning">{{ $item->status_text }}</span>
                                    @elseif ($item->status == 3)
                                        <span class="text-danger">{{ $item->status_text }}</span>
                                    @elseif ($item->status == 4)
                                        <span class="text-success">{{ $item->status_text }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->jadwal->tuk->nama }}</td>
                                <td class="text-center">
                                    @if ($item->status == 1)
                                        <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                                            <form action="{{ route('kaprodi.verifikasi-pendaftaran.update', $item->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="3">
                                                <button type="submit"
                                                    class="btn btn-light btn-icon btn-sm border shadow-sm" title="Edit">
                                                    <i class="fas fa-check text-success"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('kaprodi.verifikasi-pendaftaran.update', $item->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="2">
                                                <button type="submit"
                                                    class="btn btn-light btn-icon btn-sm border shadow-sm" title="Hapus">
                                                    <i class="fas fa-times text-danger"></i>
                                                </button>
                                            </form>
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
                $('#pendaftaranTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari nama asesi...",
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
