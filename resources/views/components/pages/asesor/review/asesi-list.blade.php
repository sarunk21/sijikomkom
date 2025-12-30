@extends('components.templates.master-layout')

@section('title', 'Daftar Asesi - Review')
@section('page-title', 'Daftar Asesi - Review & Verifikasi')

@section('content')
    <a href="{{ route('asesor.review.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-primary mr-2"></i>
        <span class="text-primary">Kembali ke Daftar Jadwal</span>
    </a>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Jadwal Info Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-info-circle mr-2"></i> Informasi Jadwal Ujian
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="font-weight-bold" style="width: 150px;">Skema</td>
                            <td>: {{ $jadwal->skema->nama }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Kode Skema</td>
                            <td>: {{ $jadwal->skema->kode }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">TUK</td>
                            <td>: {{ $jadwal->tuk->nama }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="font-weight-bold" style="width: 150px;">Tanggal Ujian</td>
                            <td>: {{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Waktu</td>
                            <td>: {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Jumlah Asesi</td>
                            <td>: {{ $asesiList->count() }} peserta</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Approve Kelayakan -->
    <div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="approveForm" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-check-circle mr-2"></i>Setujui Kelayakan
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Anda akan menyetujui kelayakan asesi: <strong id="approveNama"></strong></p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Setelah disetujui, asesi dapat melakukan pembayaran untuk mengikuti ujian.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check mr-1"></i>Ya, Setujui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Reject Kelayakan -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-times-circle mr-2"></i>Tolak Kelayakan
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Anda akan menolak kelayakan asesi: <strong id="rejectNama"></strong></p>
                        <div class="form-group">
                            <label for="catatan">Catatan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="4" 
                                placeholder="Masukkan alasan penolakan..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-check mr-1"></i>Tolak Kelayakan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Asesi List Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users mr-2"></i> Daftar Asesi
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="asesiTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>NIM/NIK</th>
                            <th>Nama Asesi</th>
                            <th>Email</th>
                            <th>Status APL1</th>
                            <th>Status APL2</th>
                            <th>Kelayakan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($asesiList as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->asesi->nim ?? $item->asesi->nik ?? '-' }}</td>
                                <td>
                                    <strong>{{ $item->asesi->name }}</strong>
                                </td>
                                <td>{{ $item->asesi->email }}</td>
                                <td class="text-center">
                                    @if($item->has_apl1)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle mr-1"></i> Terisi
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-clock mr-1"></i> Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->has_apl2)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle mr-1"></i> Terisi
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-clock mr-1"></i> Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->kelayakan_status == 0)
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-clock mr-1"></i> Belum Diperiksa
                                        </span>
                                    @elseif($item->kelayakan_status == 1)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle mr-1"></i> Layak
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times-circle mr-1"></i> Tidak Layak
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group-vertical btn-group-sm" role="group" style="gap: 4px; display: flex;">
                                        @if($item->pendaftaran_id)
                                            <!-- APL Buttons -->
                                            <div class="btn-group btn-group-sm" role="group">
                                                @if($item->has_apl1)
                                                    <a href="{{ route('asesor.review.apl1', $item->pendaftaran_id) }}"
                                                        class="btn btn-info"
                                                        title="Review APL1">
                                                        <i class="fas fa-file-alt mr-1"></i> APL1
                                                    </a>
                                                @else
                                                    <button class="btn btn-secondary" disabled title="APL1 belum diisi asesi">
                                                        <i class="fas fa-file-alt mr-1"></i> APL1
                                                    </button>
                                                @endif

                                                @if($item->has_apl2)
                                                    <a href="{{ route('asesor.review.apl2', $item->pendaftaran_id) }}"
                                                        class="btn btn-warning"
                                                        title="Review APL2">
                                                        <i class="fas fa-file-contract mr-1"></i> APL2
                                                    </a>
                                                @else
                                                    <button class="btn btn-secondary" disabled title="APL2 belum diisi asesi">
                                                        <i class="fas fa-file-contract mr-1"></i> APL2
                                                    </button>
                                                @endif
                                            </div>

                                            <!-- Kelayakan Buttons (hanya muncul jika status 6 dan belum ada kelayakan status) -->
                                            @if($item->pendaftaran_status == 6 && $item->kelayakan_status == 0)
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-success approveBtn"
                                                        data-toggle="modal" 
                                                        data-target="#approveModal"
                                                        data-id="{{ $item->pendaftaran_id }}"
                                                        data-nama="{{ $item->asesi->name }}"
                                                        title="Layak">
                                                        <i class="fas fa-check-circle mr-1"></i> Layak
                                                    </button>
                                                    <button type="button" class="btn btn-danger rejectBtn"
                                                        data-toggle="modal" 
                                                        data-target="#rejectModal"
                                                        data-id="{{ $item->pendaftaran_id }}"
                                                        data-nama="{{ $item->asesi->name }}"
                                                        title="Tidak Layak">
                                                        <i class="fas fa-times-circle mr-1"></i> Tidak Layak
                                                    </button>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted small">Data tidak tersedia</span>
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

    <style>
        .badge {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .btn-group .btn {
            margin: 0 2px;
        }

        .table td {
            vertical-align: middle;
        }

        .table-borderless td {
            padding: 0.5rem 0;
        }
    </style>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#asesiTable').DataTable({
                    responsive: true,
                    ordering: false,
                    language: {
                        searchPlaceholder: "Cari asesi...",
                        search: "",
                        lengthMenu: "_MENU_ asesi per halaman",
                        zeroRecords: "Asesi tidak ditemukan",
                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ asesi",
                        infoEmpty: "Tidak ada data",
                        paginate: {
                            previous: "Sebelumnya",
                            next: "Berikutnya"
                        }
                    },
                    columnDefs: [{
                        targets: -1, // Last column (actions)
                        orderable: false
                    }]
                });

                // Handle approve button click - use event delegation for DataTable
                $(document).on('click', '.approveBtn', function() {
                    var pendaftaranId = $(this).data('id');
                    var nama = $(this).data('nama');
                    
                    $('#approveNama').text(nama);
                    $('#approveForm').attr('action', '{{ route("asesor.review.kelayakan.approve", ":id") }}'.replace(':id', pendaftaranId));
                });

                // Handle reject button click - use event delegation for DataTable
                $(document).on('click', '.rejectBtn', function() {
                    var pendaftaranId = $(this).data('id');
                    var nama = $(this).data('nama');
                    
                    $('#rejectNama').text(nama);
                    $('#rejectForm').attr('action', '{{ route("asesor.review.kelayakan.reject", ":id") }}'.replace(':id', pendaftaranId));
                    $('#catatan').val('');
                });

                // Reset modals on close
                $('#approveModal').on('hidden.bs.modal', function() {
                    $('#approveForm').attr('action', '');
                    $('#approveNama').text('');
                });

                $('#rejectModal').on('hidden.bs.modal', function() {
                    $('#rejectForm').attr('action', '');
                    $('#catatan').val('');
                    $('#rejectNama').text('');
                });
            });
        </script>
    @endpush
@endsection
