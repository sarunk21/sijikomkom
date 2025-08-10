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
                                    @elseif ($item->status == 5)
                                        <span class="text-success">{{ $item->status_text }}</span>
                                    @elseif ($item->status == 6)
                                        <span class="text-success">{{ $item->status_text }}</span>
                                    @else
                                        <span class="text-danger">{{ $item->status_text }}</span>
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
                        <input type="hidden" name="status" value="2">
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
                    language: {
                        searchPlaceholder: "Cari Pendaftaran...",
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
                    modal.find('#formKeterangan').attr('action', '{{ route("kaprodi.verifikasi-pendaftaran.update", ":id") }}'.replace(':id', id));
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
