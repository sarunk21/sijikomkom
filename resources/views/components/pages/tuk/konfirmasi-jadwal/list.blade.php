@extends('components.templates.master-layout')

@section('title', 'Konfirmasi Jadwal')
@section('page-title', 'Konfirmasi Jadwal')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="pendaftaranTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Skema</th>
                            <th>TUK</th>
                            <th>Tanggal Ujian</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Kuota</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($konfirmasiJadwal as $item)
                            <tr>
                                <td>{{ $item->skema->nama }}</td>
                                <td>{{ $item->tuk->nama }}</td>
                                <td>{{ $item->tanggal_ujian }}</td>
                                <td>{{ $item->tanggal_selesai }}</td>
                                <td>
                                    @if ($item->status == 5)
                                        <span class="badge badge-warning">Menunggu Konfirmasi</span>
                                    @endif
                                </td>
                                <td>{{ $item->kuota }}</td>
                                <td class="text-center">
                                    @if ($item->status == 5)
                                        <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                                            <form action="{{ route('tuk.konfirmasi-jadwal.update', $item->id) }}"
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
                                                id="keteranganButton"
                                                class="btn btn-light btn-icon btn-sm border shadow-sm"
                                                data-toggle="modal" data-target="#modalKeterangan"
                                                data-keterangan="{{ $item->keterangan ?? '' }}"
                                                data-id="{{ $item->id }}">
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

    <div class="modal fade" id="modalKeterangan" tabindex="-1" aria-labelledby="modalKeteranganLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalKeteranganLabel">Keterangan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formKeterangan" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="6">
                        <div class="form-group">
                            <label for="keterangan">Keterangan Penolakan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Masukkan keterangan..."></textarea>
                        </div>
                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
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
    </style>

    {{-- Scripts --}}
    @push('scripts')
        <!-- DataTables sudah dimuat global dari layout -->
        <script>
            $(document).ready(function() {
                $('#pendaftaranTable').DataTable({
                    responsive: true,
                    ordering: false,
                    language: {
                        searchPlaceholder: "Cari Konfirmasi Jadwal...",
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

                // Handle modal keterangan
                $('#keteranganButton').on('click', function (event) {
                    var button = $(this);
                    var keterangan = button.data('keterangan');
                    var id = button.data('id');

                    var modal = $('#modalKeterangan');
                    modal.find('#formKeterangan').attr('action', '{{ route("tuk.konfirmasi-jadwal.update", ":id") }}'.replace(':id', id));
                    modal.find('#keterangan').val(keterangan || '');
                });

                // Reset modal when close
                $('#modalKeterangan').on('hidden.bs.modal', function () {
                    $('#formKeterangan').attr('action', '');
                    $(this).find('#keterangan').val('');
                });
            });
        </script>
    @endpush
@endsection
