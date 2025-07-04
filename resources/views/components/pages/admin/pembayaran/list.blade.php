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
                            <th>Keterangan</th>
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
                                    @if ($item->bukti_pembayaran)
                                        <a href="{{ asset('storage/' . $item->bukti_pembayaran) }}"
                                            class="btn btn-light btn-icon btn-sm border shadow-sm" target="_blank" title="Lihat Bukti">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>
                                    @else
                                        <span class="text-danger">Pendaftaran Pertama</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status == 1)
                                        <span class="badge badge-warning">{{ $item->status_text }}</span>
                                    @elseif ($item->status == 2)
                                        <span class="badge badge-info">{{ $item->status_text }}</span>
                                    @elseif ($item->status == 3)
                                        <span class="badge badge-danger">{{ $item->status_text }}</span>
                                    @elseif ($item->status == 4)
                                        <span class="badge badge-success">{{ $item->status_text }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $item->status_text }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->keterangan }}</td>
                                <td class="text-center">
                                    @if ($item->status == 2)
                                        <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                                            <form action="{{ route('admin.pembayaran-asesi.update', $item->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="1">
                                                <button type="submit"
                                                    class="btn btn-light btn-icon btn-sm border shadow-sm" title="Approve">
                                                    <i class="fas fa-check text-success"></i>
                                                </button>
                                            </form>
                                            <button
                                                type="button"
                                                id="rejectButton"
                                                class="btn btn-light btn-icon btn-sm border shadow-sm"
                                                title="Reject"
                                                data-toggle="modal"
                                                data-target="#rejectModal"
                                                data-id="{{ $item->id }}"
                                                data-keterangan="{{ $item->keterangan ?? '' }}">
                                                <i class="fas fa-times text-danger"></i>
                                            </button>
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

    <!-- Modal Reject Pembayaran -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Tolak Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formReject" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="2">
                        <div class="form-group">
                            <label for="keterangan">Keterangan Penolakan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="3"
                                placeholder="Masukkan alasan penolakan pembayaran..." required></textarea>
                        </div>
                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Tolak Pembayaran</button>
                        </div>
                    </form>
                </div>
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

        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-info {
            background-color: #17a2b8;
            color: #fff;
        }

        .badge-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .badge-success {
            background-color: #28a745;
            color: #fff;
        }

        .badge-secondary {
            background-color: #6c757d;
            color: #fff;
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
                        searchPlaceholder: "Cari pembayaran...",
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

                // Handle modal reject
                $('#rejectButton').on('click', function (event) {
                    var button = $(this);
                    var id = button.data('id');
                    var keterangan = button.data('keterangan');
                    var modal = $('#rejectModal');

                    modal.find('#formReject').attr('action', '{{ route("admin.pembayaran-asesi.update", ":id") }}'.replace(':id', id));
                    modal.find('#keterangan').val(keterangan || '');
                });

                // Reset modal when close
                $('#rejectModal').on('hidden.bs.modal', function () {
                    $('#formReject').attr('action', '');
                    $('#keterangan').val('');
                });
            });
        </script>
    @endpush
@endsection
