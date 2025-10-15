@extends('components.templates.master-layout')

@section('title', 'Sertifikasi')
@section('page-title', 'Sertifikasi')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="sertifikasiTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Skema</th>
                            <th>Tanggal Assesmen</th>
                            <th>TUK</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendaftaran as $item)
                            <tr>
                                <td>{{ $item->skema->nama }}</td>
                                <td>{{ $item->jadwal->tanggal_ujian }}</td>
                                <td>{{ $item->jadwal->tuk->nama }}</td>
                                <td>
                                    @if($item->status == 4)
                                        <span class="badge badge-warning">{{ $item->status_text }}</span>
                                    @elseif($item->status == 6)
                                        <span class="badge badge-success">{{ $item->status_text }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $item->status_text }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->pendaftaranUjikom ? $item->pendaftaranUjikom->keterangan : '-' }}</td>
                                <td class="text-center">
                                    @if($item->status == 4)
                                        {{-- Status Menunggu Ujian - bisa generate APL 1 --}}
                                        <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                                            <a href="{{ route('asesi.custom-data.show', $item->id) }}"
                                                class="btn btn-success btn-sm"
                                                title="Input Data Custom">
                                                <i class="fas fa-edit"></i> Custom
                                            </a>
                                            <a href="{{ route('asesi.template.generate-apl1', $item->id) }}"
                                                class="btn btn-primary btn-sm"
                                                title="Generate APL 1">
                                                <i class="fas fa-file-word"></i> APL 1
                                            </a>
                                            <button type="button"
                                                class="btn btn-info btn-sm preview-apl1-btn"
                                                data-pendaftaran-id="{{ $item->id }}"
                                                title="Preview Data APL 1">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Preview Data APL 1 -->
    <div class="modal fade" id="previewApl1Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-word"></i> Preview Data APL 1
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="previewApl1Content">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <a href="#" id="generateApl1Link" class="btn btn-primary">
                        <i class="fas fa-download"></i> Generate & Download APL 1
                    </a>
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

        .data-preview-table {
            width: 100%;
        }

        .data-preview-table tr {
            border-bottom: 1px solid #e3e6f0;
        }

        .data-preview-table td {
            padding: 0.75rem;
        }

        .data-preview-table td:first-child {
            font-weight: 600;
            width: 40%;
            background-color: #f8f9fc;
        }
    </style>

    {{-- Scripts --}}
    @push('scripts')
        <!-- DataTables sudah dimuat global dari layout -->
        <script>
            $(document).ready(function() {
                $('#sertifikasiTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari Sertifikasi...",
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

                // Preview APL 1 Data
                $('.preview-apl1-btn').on('click', function() {
                    const pendaftaranId = $(this).data('pendaftaran-id');

                    // Show modal
                    $('#previewApl1Modal').modal('show');

                    // Reset content
                    $('#previewApl1Content').html(`
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    `);

                    // Update generate link
                    $('#generateApl1Link').attr('href', `/asesi/template/generate-apl1/${pendaftaranId}`);

                    // Fetch preview data
                    $.ajax({
                        url: `/asesi/template/preview-apl1-data/${pendaftaranId}`,
                        method: 'GET',
                        success: function(response) {
                            console.log('Preview response:', response); // Debug log

                            if (response.success) {
                                let html = '<table class="data-preview-table">';

                                // Data Asesi
                                html += '<tr><td colspan="2" class="bg-primary text-white"><strong>Data Asesi</strong></td></tr>';
                                html += `<tr><td>Nama</td><td>${response.data['user.name'] || '-'}</td></tr>`;
                                html += `<tr><td>Email</td><td>${response.data['user.email'] || '-'}</td></tr>`;
                                html += `<tr><td>Telepon</td><td>${response.data['user.telephone'] || '-'}</td></tr>`;
                                html += `<tr><td>NIK</td><td>${response.data['user.nik'] || '-'}</td></tr>`;
                                html += `<tr><td>Alamat</td><td>${response.data['user.alamat'] || '-'}</td></tr>`;

                                // Data Skema
                                html += '<tr><td colspan="2" class="bg-info text-white"><strong>Data Skema</strong></td></tr>';
                                html += `<tr><td>Nama Skema</td><td>${response.data['skema.nama'] || '-'}</td></tr>`;
                                html += `<tr><td>Kode Skema</td><td>${response.data['skema.kode'] || '-'}</td></tr>`;
                                html += `<tr><td>Kategori</td><td>${response.data['skema.kategori'] || '-'}</td></tr>`;
                                html += `<tr><td>Bidang</td><td>${response.data['skema.bidang'] || '-'}</td></tr>`;

                                // Data Jadwal
                                html += '<tr><td colspan="2" class="bg-success text-white"><strong>Data Jadwal</strong></td></tr>';
                                html += `<tr><td>Tanggal Ujian</td><td>${response.data['jadwal.tanggal_ujian'] || '-'}</td></tr>`;
                                html += `<tr><td>Waktu</td><td>${response.data['jadwal.waktu_mulai'] || '-'} - ${response.data['jadwal.waktu_selesai'] || '-'}</td></tr>`;
                                html += `<tr><td>Lokasi (TUK)</td><td>${response.data['jadwal.tuk.nama'] || '-'}</td></tr>`;

                                // Data Sistem
                                html += '<tr><td colspan="2" class="bg-secondary text-white"><strong>Data Sistem</strong></td></tr>';
                                html += `<tr><td>Nomor Pendaftaran</td><td>${response.data['system.nomor_pendaftaran'] || '-'}</td></tr>`;
                                html += `<tr><td>Tanggal Generate</td><td>${response.data['system.tanggal_generate'] || '-'}</td></tr>`;

                                html += '</table>';

                                $('#previewApl1Content').html(html);
                            } else {
                                $('#previewApl1Content').html(`
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        ${response.error || 'Gagal memuat data preview'}
                                    </div>
                                `);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', xhr.responseText); // Debug log
                            $('#previewApl1Content').html(`
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Terjadi kesalahan saat memuat data: ${error}
                                    <br><small>Status: ${xhr.status} - ${xhr.statusText}</small>
                                </div>
                            `);
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
