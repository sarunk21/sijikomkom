@extends('components.templates.master-layout')

@section('title', 'Verifikasi Pendaftaran')
@section('page-title', 'Verifikasi Pendaftaran')

@section('content')
    {{-- Filter Card --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">
            <h6 class="mb-0">Filter Data</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('kaprodi.verifikasi-pendaftaran.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="skema_id">Skema</label>
                            <select class="form-control" id="skema_id" name="skema_id">
                                <option value="">Semua Skema</option>
                                @foreach($skemas as $skema)
                                    <option value="{{ $skema->id }}" {{ request('skema_id') == $skema->id ? 'selected' : '' }}>
                                        {{ $skema->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Ditolak</option>
                                <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Diverifikasi</option>
                                <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Sudah Bayar</option>
                                <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>Pembayaran Diverifikasi</option>
                                <option value="6" {{ request('status') == '6' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter mr-1"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('kaprodi.verifikasi-pendaftaran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo mr-1"></i> Reset Filter
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table Card --}}
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
                                    <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                                        <button
                                            type="button"
                                            class="btn btn-light btn-icon btn-sm border shadow-sm viewDetailsButton"
                                            data-toggle="modal"
                                            data-target="#modalDetails"
                                            data-id="{{ $item->id }}"
                                            data-item="{{ json_encode($item) }}"
                                            title="View Details">
                                            <i class="fas fa-eye text-info"></i>
                                        </button>
                                        @if ($item->status == 1)
                                            <form action="{{ route('kaprodi.verifikasi-pendaftaran.update', $item->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="3">
                                                <button type="submit"
                                                    class="btn btn-light btn-icon btn-sm border shadow-sm" title="Approve">
                                                    <i class="fas fa-check text-success"></i>
                                                </button>
                                            </form>
                                            <button
                                                type="button"
                                                class="btn btn-light btn-icon btn-sm border shadow-sm keteranganButton"
                                                data-toggle="modal" data-target="#modalKeterangan"
                                                data-keterangan="{{ $item->keterangan ?? '' }}"
                                                data-id="{{ $item->id }}"
                                                title="Reject">
                                                <i class="fas fa-times text-danger"></i>
                                            </button>
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

    {{-- Modal Keterangan --}}
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

    {{-- Modal Details --}}
    <div class="modal fade" id="modalDetails" tabindex="-1" aria-labelledby="modalDetailsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailsLabel">Detail Pendaftaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Informasi Asesi & Pendaftaran in Cards -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-left-primary shadow-sm h-100 mb-3">
                                <div class="card-body">
                                    <h6 class="font-weight-bold text-primary mb-3">
                                        <i class="fas fa-user mr-2"></i>Informasi Asesi
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="40%" class="text-muted"><strong>Nama</strong></td>
                                            <td id="detail-nama">-</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><strong>NIM</strong></td>
                                            <td id="detail-nim">-</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><strong>Email</strong></td>
                                            <td id="detail-email">-</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><strong>No. Telepon</strong></td>
                                            <td id="detail-phone">-</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><strong>Jurusan</strong></td>
                                            <td id="detail-jurusan">-</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-left-success shadow-sm h-100 mb-3">
                                <div class="card-body">
                                    <h6 class="font-weight-bold text-success mb-3">
                                        <i class="fas fa-clipboard-list mr-2"></i>Informasi Pendaftaran
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="40%" class="text-muted"><strong>Skema</strong></td>
                                            <td id="detail-skema">-</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><strong>TUK</strong></td>
                                            <td id="detail-tuk">-</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><strong>Tanggal Ujian</strong></td>
                                            <td id="detail-tanggal">-</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><strong>Status</strong></td>
                                            <td id="detail-status">-</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><strong>Tanggal Daftar</strong></td>
                                            <td id="detail-created">-</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan Card -->
                    <div class="card border-left-warning shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-warning mb-2">
                                <i class="fas fa-comment mr-2"></i>Keterangan
                            </h6>
                            <p id="detail-keterangan" class="mb-0 text-muted">-</p>
                        </div>
                    </div>

                    <!-- File Upload Card -->
                    <div class="card border-left-info shadow-sm" id="detail-files-card">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-info mb-3">
                                <i class="fas fa-file-upload mr-2"></i>File yang Diupload
                            </h6>
                            <div id="detail-files" class="row"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Optional CSS for btn-icon and modal cards --}}
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

        .card {
            border-radius: 0.35rem;
        }

        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        #detail-files-card {
            display: none;
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
                $('.keteranganButton').on('click', function (event) {
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

                // Handle modal details
                $('.viewDetailsButton').on('click', function (event) {
                    var button = $(this);
                    var item = button.data('item');

                    console.log('=== MODAL DEBUG ===');
                    console.log('Full item data:', item);
                    console.log('User data:', item.user);
                    console.log('User files:', {
                        photo_ktp: item.user?.photo_ktp,
                        photo_sertifikat: item.user?.photo_sertifikat,
                        photo_ktmkhs: item.user?.photo_ktmkhs,
                        photo_administatif: item.user?.photo_administatif
                    });

                    // Populate user information
                    $('#detail-nama').text(item.user?.name || '-');
                    $('#detail-nim').text(item.user?.nim || '-');
                    $('#detail-email').text(item.user?.email || '-');
                    $('#detail-phone').text(item.user?.no_hp || '-');
                    $('#detail-jurusan').text(item.user?.jurusan || '-');

                    // Populate registration information
                    $('#detail-skema').text(item.skema?.nama || '-');
                    $('#detail-tuk').text(item.jadwal?.tuk?.nama || '-');
                    $('#detail-tanggal').text(item.jadwal?.tanggal_ujian || '-');
                    $('#detail-status').text(item.status_text || '-');
                    $('#detail-created').text(item.created_at || '-');
                    $('#detail-keterangan').text(item.keterangan || 'Tidak ada keterangan');

                    // Handle files if any
                    var filesHtml = '';
                    var hasFiles = false;

                    // First, check user's uploaded files (stored in users table)
                    if (item.user) {
                        var userFileLabels = {
                            'photo_ktp': 'Foto KTP',
                            'photo_sertifikat': 'Surat Rekomendasi',
                            'photo_ktmkhs': 'Foto KTM/KHS',
                            'photo_administatif': 'Foto Administratif'
                        };

                        Object.keys(userFileLabels).forEach(function(key) {
                            var value = item.user[key];
                            if (value) {
                                hasFiles = true;
                                var fileUrl = value.startsWith('http') ? value : '/storage/' + value;
                                var label = userFileLabels[key];

                                filesHtml += '<div class="col-md-6 mb-3">';
                                filesHtml += '  <div class="card">';
                                filesHtml += '    <div class="card-body p-3">';
                                filesHtml += '      <h6 class="font-weight-bold mb-2">' + label + '</h6>';
                                filesHtml += '      <a href="' + fileUrl + '" target="_blank" class="btn btn-info btn-sm btn-block">';
                                filesHtml += '        <i class="fas fa-external-link-alt mr-1"></i> Lihat File';
                                filesHtml += '      </a>';
                                filesHtml += '    </div>';
                                filesHtml += '  </div>';
                                filesHtml += '</div>';
                            }
                        });
                    }

                    // Also check pendaftaranUjikom for uploaded files
                    if (!hasFiles && item.pendaftaran_ujikom && item.pendaftaran_ujikom.custom_variables) {
                        try {
                            var customVars = typeof item.pendaftaran_ujikom.custom_variables === 'string'
                                ? JSON.parse(item.pendaftaran_ujikom.custom_variables)
                                : item.pendaftaran_ujikom.custom_variables;

                            if (customVars && typeof customVars === 'object') {
                                // Define friendly names for file fields
                                var fileLabels = {
                                    'foto_ktp': 'Foto KTP',
                                    'surat_rekomendasi': 'Surat Rekomendasi',
                                    'foto_ktm_khs': 'Foto KTM/KHS',
                                    'foto_administratif': 'Foto Administratif'
                                };

                                Object.keys(customVars).forEach(function(key) {
                                    var value = customVars[key];
                                    if (value && (value.toString().includes('/storage/') || value.toString().includes('http'))) {
                                        hasFiles = true;
                                        var label = fileLabels[key] || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

                                        filesHtml += '<div class="col-md-6 mb-3">';
                                        filesHtml += '  <div class="card">';
                                        filesHtml += '    <div class="card-body p-3">';
                                        filesHtml += '      <h6 class="font-weight-bold mb-2">' + label + '</h6>';
                                        filesHtml += '      <a href="' + value + '" target="_blank" class="btn btn-info btn-sm btn-block">';
                                        filesHtml += '        <i class="fas fa-external-link-alt mr-1"></i> Lihat File';
                                        filesHtml += '      </a>';
                                        filesHtml += '    </div>';
                                        filesHtml += '  </div>';
                                        filesHtml += '</div>';
                                    }
                                });
                            }
                        } catch (e) {
                            console.error('Error parsing custom variables:', e);
                        }
                    }

                    // Also check direct custom_variables on item
                    if (!hasFiles && item.custom_variables) {
                        try {
                            var customVars2 = typeof item.custom_variables === 'string'
                                ? JSON.parse(item.custom_variables)
                                : item.custom_variables;

                            if (customVars2 && typeof customVars2 === 'object') {
                                var fileLabels = {
                                    'foto_ktp': 'Foto KTP',
                                    'surat_rekomendasi': 'Surat Rekomendasi',
                                    'foto_ktm_khs': 'Foto KTM/KHS',
                                    'foto_administratif': 'Foto Administratif'
                                };

                                Object.keys(customVars2).forEach(function(key) {
                                    var value = customVars2[key];
                                    if (value && (value.toString().includes('/storage/') || value.toString().includes('http'))) {
                                        hasFiles = true;
                                        var label = fileLabels[key] || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

                                        filesHtml += '<div class="col-md-6 mb-3">';
                                        filesHtml += '  <div class="card">';
                                        filesHtml += '    <div class="card-body p-3">';
                                        filesHtml += '      <h6 class="font-weight-bold mb-2">' + label + '</h6>';
                                        filesHtml += '      <a href="' + value + '" target="_blank" class="btn btn-info btn-sm btn-block">';
                                        filesHtml += '        <i class="fas fa-external-link-alt mr-1"></i> Lihat File';
                                        filesHtml += '      </a>';
                                        filesHtml += '    </div>';
                                        filesHtml += '  </div>';
                                        filesHtml += '</div>';
                                    }
                                });
                            }
                        } catch (e) {
                            console.error('Error parsing item custom variables:', e);
                        }
                    }

                    if (hasFiles) {
                        $('#detail-files-card').show();
                        $('#detail-files').html(filesHtml);
                    } else {
                        $('#detail-files-card').hide();
                    }
                });

                // Reset modal details when close
                $('#modalDetails').on('hidden.bs.modal', function () {
                    $('#detail-nama, #detail-nim, #detail-email, #detail-phone, #detail-jurusan, ' +
                      '#detail-skema, #detail-tuk, #detail-tanggal, #detail-status, #detail-created').text('-');
                    $('#detail-keterangan').text('-');
                    $('#detail-files').html('');
                    $('#detail-files-card').hide();
                });
            });
        </script>
    @endpush
@endsection
