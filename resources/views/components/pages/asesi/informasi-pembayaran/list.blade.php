@extends('components.templates.master-layout')

@section('title', 'Informasi Pembayaran')
@section('page-title', 'Informasi Pembayaran')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table id="pendaftaranTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Skema</th>
                            <th>Tanggal Asesmen</th>
                            <th>TUK</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Bukti Pembayaran</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembayaran as $item)
                            <tr>
                                <td>{{ $item->jadwal->skema->nama }}</td>
                                <td>{{ $item->jadwal->tanggal_ujian }}</td>
                                <td>{{ $item->jadwal->tuk->nama }}</td>
                                <td>
                                    {{ $item->status_text }}
                                </td>
                                <td>{{ $item->keterangan }}</td>
                                <td>
                                    @if ($item->bukti_pembayaran)
                                        <a href="{{ asset('storage/' . $item->bukti_pembayaran) }}" target="_blank">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-eye text-orange"></i>
                                            </div>
                                        </a>
                                    @else
                                        <span class="text-muted">Belum ada</span>
                                    @endif
                                </td>
                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if ($item->status == 1)
                                        <a href="{{ route('asesi.informasi-pembayaran.edit', $item->id) }}"
                                            class="btn btn-outline-warning btn-sm shadow-sm">
                                            <i class="fas fa-upload"></i> Upload Bukti
                                        </a>
                                    @elseif ($item->status == 2)
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i> Menunggu Verifikasi
                                        </span>
                                    @elseif ($item->status == 3)
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times"></i> Ditolak
                                        </span>
                                    @elseif ($item->status == 4)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Dikonfirmasi
                                        </span>
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

    {{-- Include Payment Confirmation Modal --}}
    @include('components.modals.payment-confirmation-modal')

    {{-- Scripts --}}
    @push('scripts')
        <!-- DataTables sudah dimuat global dari layout -->
        <script>
            $(document).ready(function() {
                $('#pendaftaranTable').DataTable({
                    responsive: true,
                    ordering: false,
                    language: {
                        searchPlaceholder: "Cari Informasi Pembayaran...",
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
