@extends('components.templates.master-layout')

@section('title', 'Review & Verifikasi')
@section('page-title', 'Review & Verifikasi')

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

    <!-- Pending Confirmations Section -->
    @if(isset($pendingConfirmations) && $pendingConfirmations->count() > 0)
    <div class="card shadow-sm mb-4 border-left-info">
        <div class="card-header bg-gradient-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-calendar-check mr-2"></i>Jadwal Asesmen Mendatang
            </h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Catatan:</strong> Anda ditugaskan untuk <strong>{{ $pendingConfirmations->count() }} jadwal ujikom</strong> mendatang.
                <br><br>
                Anda dianggap <strong>HADIR</strong> secara default untuk semua jadwal di bawah.
                <br>
                Jika Anda <strong>tidak dapat hadir</strong>, silakan klik tombol "Tidak Dapat Hadir" sebelum ujian dimulai.
                Sistem akan otomatis mencari asesor pengganti.
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="pendingConfirmationTable">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 15%;">Tanggal Ujian</th>
                            <th style="width: 25%;">Skema</th>
                            <th style="width: 20%;">TUK</th>
                            <th style="width: 10%;" class="text-center">Jumlah Asesi</th>
                            <th style="width: 15%;">Ditugaskan Sejak</th>
                            <th style="width: 15%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingConfirmations as $item)
                        <tr id="confirmation-row-{{ $item['jadwal_id'] }}">
                            <td>
                                <strong class="text-primary">{{ \Carbon\Carbon::parse($item['jadwal']->tanggal_ujian)->format('d M Y') }}</strong><br>
                                <small class="text-muted">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ \Carbon\Carbon::parse($item['jadwal']->waktu_mulai)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($item['jadwal']->waktu_selesai)->format('H:i') }}
                                </small>
                            </td>
                            <td>
                                <strong>{{ $item['jadwal']->skema->nama ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $item['jadwal']->skema->kode ?? '-' }}</small>
                            </td>
                            <td>{{ $item['jadwal']->tuk->nama ?? 'N/A' }}</td>
                            <td class="text-center">
                                <span class="badge badge-info" style="font-size: 0.9rem; padding: 0.5rem 0.75rem;">
                                    {{ $item['jumlah_asesi'] }} Asesi
                                </span>
                            </td>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($item['ditugaskan_sejak'])->format('d M Y H:i') }}</small>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-danger reject-btn"
                                        data-jadwal-id="{{ $item['jadwal_id'] }}"
                                        data-tanggal="{{ \Carbon\Carbon::parse($item['jadwal']->tanggal_ujian)->format('d M Y') }}"
                                        data-skema="{{ $item['jadwal']->skema->nama ?? 'N/A' }}"
                                        data-jumlah="{{ $item['jumlah_asesi'] }}"
                                        title="Tidak Dapat Hadir">
                                    <i class="fas fa-times mr-1"></i>Tidak Dapat Hadir
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-filter mr-2"></i> Filter Jadwal
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('asesor.review.index') }}" method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-semibold">Tanggal Dari</label>
                        <input type="date"
                               name="tanggal_dari"
                               class="form-control"
                               value="{{ request('tanggal_dari') }}"
                               id="tanggal_dari">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-semibold">Tanggal Sampai</label>
                        <input type="date"
                               name="tanggal_sampai"
                               class="form-control"
                               value="{{ request('tanggal_sampai') }}"
                               id="tanggal_sampai">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-semibold">Skema Sertifikasi</label>
                        <select name="skema_id" class="form-control" id="skema_id">
                            <option value="">-- Semua Skema --</option>
                            @foreach($skemas as $skema)
                                <option value="{{ $skema->id }}" {{ request('skema_id') == $skema->id ? 'selected' : '' }}>
                                    {{ $skema->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-2"></i> Filter
                        </button>
                        <a href="{{ route('asesor.review.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo mr-2"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Jadwal List Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar-check mr-2"></i> Daftar Jadwal Ujian Kompetensi
            </h6>
        </div>
        <div class="card-body">
            @if($jadwalList->count() > 0)
                <div class="table-responsive">
                    <table id="jadwalTable" class="table table-striped table-hover align-middle w-100">
                        <thead class="thead-light">
                            <tr>
                                <th>Skema</th>
                                <th>TUK</th>
                                <th>Tanggal Ujian</th>
                                <th>Waktu</th>
                                <th>Jumlah Asesi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jadwalList as $item)
                                @php
                                    $jadwal = $item->jadwal;
                                    // Count total asesi for this jadwal
                                    $totalAsesi = \App\Models\PendaftaranUjikom::where('jadwal_id', $jadwal->id)
                                        ->where('asesor_id', Auth::id())
                                        ->count();
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $jadwal->skema->nama }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $jadwal->skema->kode }}</small>
                                    </td>
                                    <td>{{ $jadwal->tuk->nama }}</td>
                                    <td>{{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('d M Y') }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}
                                    </td>
                                    <td>
                                        <span class="badge badge-info badge-pill">
                                            {{ $totalAsesi }} Asesi
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('asesor.review.show-asesi', $jadwal->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-users mr-1"></i> Lihat Asesi
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle mr-2"></i>
                    @if(request()->has('tanggal_dari') || request()->has('tanggal_sampai') || request()->has('skema_id'))
                        Tidak ada jadwal yang sesuai dengan filter yang Anda pilih.
                    @else
                        Belum ada jadwal ujian yang ditugaskan kepada Anda.
                    @endif
                </div>
            @endif
        </div>
    </div>

    <style>
        .badge-pill {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }

        .table td {
            vertical-align: middle;
        }

        .card-header.bg-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }
    </style>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize DataTable only if there's data
                @if($jadwalList->count() > 0)
                    $('#jadwalTable').DataTable({
                        responsive: true,
                        ordering: false,
                        language: {
                            searchPlaceholder: "Cari jadwal...",
                            search: "",
                            lengthMenu: "_MENU_ jadwal per halaman",
                            zeroRecords: "Jadwal tidak ditemukan",
                            info: "Menampilkan _START_ - _END_ dari _TOTAL_ jadwal",
                            infoEmpty: "Tidak ada data",
                            infoFiltered: "(difilter dari _MAX_ total jadwal)",
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
                @endif

                // Auto-submit on select change (optional - user experience enhancement)
                $('#skema_id').on('change', function() {
                    // Uncomment to enable auto-submit
                    // $('#filterForm').submit();
                });

                // Reject Handler (Tidak Dapat Hadir)
                $('.reject-btn').on('click', function() {
                    const jadwalId = $(this).data('jadwal-id');
                    const tanggal = $(this).data('tanggal');
                    const skema = $(this).data('skema');
                    const jumlah = $(this).data('jumlah');

                    const confirmMessage = `Anda akan menolak untuk menjadi asesor pada:\n\n` +
                        `Tanggal: ${tanggal}\n` +
                        `Skema: ${skema}\n` +
                        `Jumlah Asesi: ${jumlah} orang\n\n` +
                        `Apakah Anda yakin TIDAK DAPAT HADIR?\n` +
                        `Sistem akan mencari asesor pengganti secara otomatis.`;

                    if (!confirm(confirmMessage)) {
                        return;
                    }

                    const notes = prompt('Mohon berikan alasan mengapa Anda tidak dapat hadir (opsional):');

                    if (notes === null) {
                        return; // User canceled the prompt
                    }

                    // Send rejection request
                    $.ajax({
                        url: '{{ route("asesor.dashboard.confirm-jadwal") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            jadwal_id: jadwalId,
                            status: 'rejected',
                            notes: notes || 'Tidak dapat hadir'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert(response.message || 'Penolakan berhasil diproses. Sistem akan mencari asesor pengganti.');
                                // Remove row from table
                                $('#confirmation-row-' + jadwalId).fadeOut(300, function() {
                                    $(this).remove();
                                    // Reload if no more pending confirmations
                                    if ($('#pendingConfirmationTable tbody tr:visible').length === 0) {
                                        location.reload();
                                    }
                                });
                            } else {
                                alert('Terjadi kesalahan: ' + (response.message || 'Unknown error'));
                            }
                        },
                        error: function(xhr) {
                            let errorMsg = 'Terjadi kesalahan saat memproses penolakan.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            alert(errorMsg);
                            console.error(xhr);
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
