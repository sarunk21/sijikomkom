@extends('components.templates.master-layout')

@section('title', 'Verifikasi Pendaftaran')
@section('page-title', 'Verifikasi Pendaftaran')

@section('content')
    {{-- Filter Card --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="fas fa-filter mr-2"></i>Filter Pendaftaran</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('kaprodi.verifikasi-pendaftaran.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date">Tanggal Dari</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}" placeholder="mm/dd/yyyy">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date">Tanggal Sampai</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}" placeholder="mm/dd/yyyy">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="skema_id">Skema Sertifikasi</label>
                            <select class="form-control" id="skema_id" name="skema_id">
                                <option value="">-- Semua Skema --</option>
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
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>
                        <a href="{{ route('kaprodi.verifikasi-pendaftaran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-sync-alt mr-1"></i> Reset
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
                                <td data-order="{{ $item->created_at->timestamp }}">{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalDetailsLabel">
                        <i class="fas fa-clipboard-check mr-2"></i>Detail Pendaftaran Ujikom
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <!-- Informasi Asesi & Pendaftaran in Cards -->
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-gradient-primary text-white">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-user-graduate mr-2"></i>Informasi Asesi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="info-item mb-3">
                                        <small class="text-muted d-block">Nama Lengkap</small>
                                        <strong id="detail-nama" class="text-dark">-</strong>
                                    </div>
                                    <div class="info-item mb-3">
                                        <small class="text-muted d-block">NIM</small>
                                        <strong id="detail-nim" class="text-dark">-</strong>
                                    </div>
                                    <div class="info-item mb-3">
                                        <small class="text-muted d-block">Email</small>
                                        <strong id="detail-email" class="text-dark">-</strong>
                                    </div>
                                    <div class="info-item mb-3">
                                        <small class="text-muted d-block">No. Telepon</small>
                                        <strong id="detail-phone" class="text-dark">-</strong>
                                    </div>
                                    <div class="info-item mb-0">
                                        <small class="text-muted d-block">Program Studi</small>
                                        <strong id="detail-jurusan" class="text-dark">-</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-gradient-success text-white">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-clipboard-list mr-2"></i>Informasi Pendaftaran
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="info-item mb-3">
                                        <small class="text-muted d-block">Skema Sertifikasi</small>
                                        <strong id="detail-skema" class="text-dark">-</strong>
                                    </div>
                                    <div class="info-item mb-3">
                                        <small class="text-muted d-block">Tempat Uji Kompetensi (TUK)</small>
                                        <strong id="detail-tuk" class="text-dark">-</strong>
                                    </div>
                                    <div class="info-item mb-3">
                                        <small class="text-muted d-block">Tanggal Ujian</small>
                                        <strong id="detail-tanggal" class="text-dark">-</strong>
                                    </div>
                                    <div class="info-item mb-3">
                                        <small class="text-muted d-block">Status Pendaftaran</small>
                                        <span id="detail-status" class="badge badge-info badge-pill">-</span>
                                    </div>
                                    <div class="info-item mb-0">
                                        <small class="text-muted d-block">Tanggal Pendaftaran</small>
                                        <strong id="detail-created" class="text-dark">-</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan Card -->
                    <div class="card border-0 shadow-sm mb-3" id="detail-keterangan-card">
                        <div class="card-header bg-gradient-warning text-white">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fas fa-comment-dots mr-2"></i>Keterangan
                            </h6>
                        </div>
                        <div class="card-body">
                            <p id="detail-keterangan" class="mb-0 text-dark">-</p>
                        </div>
                    </div>

                    <!-- File Upload Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-info text-white">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fas fa-paperclip mr-2"></i>Dokumen Persyaratan
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="detail-files" class="row"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Tutup
                    </button>
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
            border-radius: 0.5rem;
            transition: transform 0.2s;
        }

        #modalDetails .card:hover {
            transform: translateY(-2px);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
        }

        .info-item {
            padding: 0.75rem;
            background: #f8f9fc;
            border-radius: 0.35rem;
        }

        .info-item small {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .info-item strong {
            font-size: 0.95rem;
        }

        #detail-files .card {
            transition: all 0.3s ease;
            border: 1px solid #e3e6f0;
        }

        #detail-files .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-color: #36b9cc;
        }

        #detail-files .btn {
            transition: all 0.2s ease;
        }

        #detail-files .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.25rem 0.5rem rgba(54, 185, 204, 0.3);
        }

        .modal-xl {
            max-width: 1140px;
        }
    </style>

    {{-- Scripts --}}
    @push('scripts')
        <!-- DataTables sudah dimuat global dari layout -->
        <script>
            $(document).ready(function() {
                $('#pendaftaranTable').DataTable({
                    responsive: true,
                    ordering: false, // Disable all sorting to preserve server-side order
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
                    }
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
                    $('#detail-phone').text(item.user?.telephone || '-');
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

                                // Determine icon based on file type
                                var icon = 'fa-file';
                                if (key.includes('ktp')) icon = 'fa-id-card';
                                else if (key.includes('sertifikat') || key.includes('rekomendasi')) icon = 'fa-file-certificate';
                                else if (key.includes('ktm') || key.includes('khs')) icon = 'fa-id-badge';
                                else if (key.includes('administratif')) icon = 'fa-file-alt';

                                filesHtml += '<div class="col-md-6 col-lg-3 mb-3">';
                                filesHtml += '  <div class="card h-100 border">';
                                filesHtml += '    <div class="card-body text-center p-3">';
                                filesHtml += '      <i class="fas ' + icon + ' fa-3x text-info mb-3"></i>';
                                filesHtml += '      <h6 class="font-weight-bold mb-3" style="font-size: 0.85rem;">' + label + '</h6>';
                                filesHtml += '      <a href="' + fileUrl + '" target="_blank" class="btn btn-info btn-sm btn-block">';
                                filesHtml += '        <i class="fas fa-eye mr-1"></i> Lihat Dokumen';
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
                        $('#detail-files').html(filesHtml);
                    } else {
                        $('#detail-files').html('<div class="col-12"><p class="text-muted mb-0">Belum ada file yang diupload</p></div>');
                    }
                });

                // Reset modal details when close
                $('#modalDetails').on('hidden.bs.modal', function () {
                    $('#detail-nama, #detail-nim, #detail-email, #detail-phone, #detail-jurusan, ' +
                      '#detail-skema, #detail-tuk, #detail-tanggal, #detail-status, #detail-created').text('-');
                    $('#detail-keterangan').text('-');
                    $('#detail-files').html('');
                });
            });
        </script>
    @endpush
@endsection
