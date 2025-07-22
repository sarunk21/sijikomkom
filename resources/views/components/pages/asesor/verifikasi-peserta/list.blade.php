@extends('components.templates.master-layout')

@section('title', 'Verifikasi Peserta')
@section('page-title', 'Verifikasi Peserta')

@section('content')
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

    <div class="card shadow-sm">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Jadwal Ujian Kompetensi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="jadwalTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Skema</th>
                            <th>TUK</th>
                            <th>Tanggal Ujian</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jadwalList as $jadwal)
                            <tr>
                                <td>{{ $jadwal->jadwal->skema->nama }}</td>
                                <td>{{ $jadwal->jadwal->tuk->nama }}</td>
                                <td>{{ \Carbon\Carbon::parse($jadwal->jadwal->tanggal_ujian)->format('d-m-Y H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($jadwal->jadwal->tanggal_selesai)->format('d-m-Y H:i') }}</td>
                                <td>
                                    <span>
                                        {{ $jadwal->status_text }}
                                    </span>
                                </td>
                                <td>
                                    @if ($jadwal->status == 1)
                                        <a href="{{ route('asesor.verifikasi-peserta.show-asesi', $jadwal->jadwal->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-users"></i> Lihat Asesi
                                        </a>
                                    @elseif ($jadwal->status == 6)
                                        <div class="d-flex gap-2">
                                            <button type="button" id="hadirButton" class="btn btn-success btn-sm"
                                                data-toggle="modal" data-target="#modalHadir"
                                                data-jadwal-id="{{ $jadwal->jadwal->id }}"
                                                data-jadwal-info="{{ $jadwal->jadwal->skema->nama }} - {{ $jadwal->jadwal->tuk->nama }}">
                                                <i class="fas fa-check"></i> Asesor Dapat Hadir
                                            </button>
                                            <button type="button" id="tidakHadirButton" class="btn btn-danger btn-sm"
                                                data-toggle="modal" data-target="#modalTidakHadir"
                                                data-jadwal-id="{{ $jadwal->jadwal->id }}"
                                                data-jadwal-info="{{ $jadwal->jadwal->skema->nama }} - {{ $jadwal->jadwal->tuk->nama }}">
                                                <i class="fas fa-times"></i> Asesor Tidak Dapat Hadir
                                            </button>
                                        </div>
                                    @elseif ($jadwal->status == 2)
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('asesor.verifikasi-peserta.show-asesi', $jadwal->jadwal->id) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-users"></i> Lihat Asesi
                                            </a>
                                        </div>
                                    @elseif ($jadwal->status == 7)
                                        <div class="d-flex gap-2">
                                            @if ($jadwal->keterangan)
                                                <button type="button" id="keteranganButton" class="btn btn-info btn-sm"
                                                    data-toggle="modal" data-target="#modalKeterangan"
                                                    data-keterangan="{{ $jadwal->keterangan }}">
                                                    <i class="fas fa-info-circle"></i> Lihat Alasan
                                                </button>
                                            @endif
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

    <!-- Modal Asesor Dapat Hadir -->
    <div class="modal fade" id="modalHadir" tabindex="-1" role="dialog" aria-labelledby="modalHadirLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalHadirLabel">Konfirmasi Kehadiran Asesor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formHadir" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Jadwal:</strong> <span id="jadwalInfoHadir"></span>
                        </div>
                        <div class="form-group">
                            <label for="keterangan_hadir">Keterangan (Opsional)</label>
                            <textarea class="form-control" id="keterangan_hadir" name="keterangan" rows="3"
                                placeholder="Masukkan keterangan tambahan jika diperlukan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Konfirmasi Dapat Hadir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Asesor Tidak Dapat Hadir -->
    <div class="modal fade" id="modalTidakHadir" tabindex="-1" role="dialog" aria-labelledby="modalTidakHadirLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTidakHadirLabel">Konfirmasi Ketidakhadiran Asesor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formTidakHadir" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>Jadwal:</strong> <span id="jadwalInfoTidakHadir"></span>
                        </div>
                        <div class="form-group">
                            <label for="keterangan_tidak_hadir">Alasan Ketidakhadiran <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="keterangan_tidak_hadir" name="keterangan" rows="3"
                                placeholder="Masukkan alasan ketidakhadiran..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times"></i> Konfirmasi Tidak Dapat Hadir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Keterangan Alasan -->
    <div class="modal fade" id="modalKeterangan" tabindex="-1" role="dialog" aria-labelledby="modalKeteranganLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalKeteranganLabel">Alasan Ketidakhadiran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Alasan:</strong>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" id="keteranganText" rows="5" readonly></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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

        .gap-2 {
            gap: 0.5rem;
        }
    </style>

    {{-- Scripts --}}
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#jadwalTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari Jadwal...",
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

                // Modal Hadir
                $('#hadirButton').on('click', function(event) {
                    var button = $(this);
                    var jadwalId = button.data('jadwal-id');
                    var jadwalInfo = button.data('jadwal-info');
                    var modal = $('#modalHadir');

                    modal.find('#jadwalInfoHadir').text(jadwalInfo);
                    modal.find('#formHadir').attr('action',
                        '{{ route('asesor.verifikasi-peserta.update-status', ':id') }}'.replace(':id', jadwalId));
                });

                // Modal Tidak Hadir
                $('#tidakHadirButton').on('click', function(event) {
                    var button = $(this);
                    var jadwalId = button.data('jadwal-id');
                    var jadwalInfo = button.data('jadwal-info');
                    var modal = $('#modalTidakHadir');

                    modal.find('#jadwalInfoTidakHadir').text(jadwalInfo);
                    modal.find('#formTidakHadir').attr('action',
                        '{{ route('asesor.verifikasi-peserta.update-status', ':id') }}'.replace(':id', jadwalId));
                });

                // Modal Keterangan
                $('#keteranganButton').on('click', function(event) {
                    var button = $(this);
                    var keterangan = button.data('keterangan');
                    var modal = $('#modalKeterangan');

                    modal.find('#keteranganText').val(keterangan);
                });

                // Reset form when modal is hidden
                $('#modalHadir, #modalTidakHadir').on('hidden.bs.modal', function() {
                    $(this).find('form')[0].reset();
                });
            });
        </script>
    @endpush
@endsection
