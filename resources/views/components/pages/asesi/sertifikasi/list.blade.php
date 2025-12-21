@extends('components.templates.master-layout')

@section('title', 'Sertifikasi')
@section('page-title', 'Sertifikasi')

@section('content')
    {{-- Info Alert - Only show for status 1, 5, or 6 (before kelayakan verified) --}}
    @if($pendaftaran->whereIn('status', [1, 5, 6])->count() > 0)
        <div class="alert alert-info alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h5 class="alert-heading"><i class="fas fa-info-circle mr-2"></i>Lengkapi Formulir APL</h5>
            <p class="mb-0">
                Pendaftaran Anda sedang diproses. Silakan <strong>lengkapi formulir APL 1 dan APL 2</strong> untuk mempercepat proses verifikasi.
                Klik tombol <span class="badge badge-primary">APL 1</span> dan <span class="badge badge-warning">APL 2</span> pada tabel di bawah.
            </p>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

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
                                    @if($item->status == 1)
                                        <span class="badge badge-info">{{ $item->status_text }}</span>
                                    @elseif($item->status == 2)
                                        <span class="badge badge-danger">{{ $item->status_text }}</span>
                                    @elseif($item->status == 3)
                                        <span class="badge badge-primary">{{ $item->status_text }}</span>
                                    @elseif($item->status == 4)
                                        <span class="badge badge-warning">{{ $item->status_text }}</span>
                                    @elseif($item->status == 5)
                                        <span class="badge badge-info">{{ $item->status_text }}</span>
                                    @elseif($item->status == 6)
                                        <span class="badge badge-success">{{ $item->status_text }}</span>
                                    @elseif($item->status == 7)
                                        <span class="badge badge-dark">{{ $item->status_text }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $item->status_text }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->pendaftaranUjikom ? $item->pendaftaranUjikom->keterangan : '-' }}</td>
                                <td class="text-center">
                                    @if($item->status == 2)
                                        {{-- Ditolak Kaprodi --}}
                                        <span class="badge badge-danger">Ditolak</span>
                                    @elseif($item->status == 7)
                                        {{-- Tidak Lolos Kelayakan --}}
                                        <span class="badge badge-danger">Tidak Layak</span>
                                    @elseif($item->status == 12)
                                        {{-- Asesor Tidak Hadir --}}
                                        <span class="badge badge-warning">Asesor Tidak Hadir - Menunggu Redistribusi</span>
                                    @elseif(in_array($item->status, [1, 3, 4, 5, 6]))
                                        {{-- Status yang bisa isi APL 1 dan APL 2: Sejak daftar sampai kelayakan --}}
                                        <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 0.5rem;">
                                            @php
                                                $isReviewedByAsesor = !empty($item->asesor_assessment);
                                            @endphp

                                            @if($isReviewedByAsesor)
                                                <button type="button"
                                                    class="btn btn-secondary btn-sm"
                                                    title="APL2 sudah direview asesor"
                                                    disabled>
                                                    <i class="fas fa-lock"></i> APL 2 (Locked)
                                                </button>
                                            @else
                                                <a href="{{ route('asesi.sertifikasi.apl2', $item->id) }}"
                                                    class="btn btn-warning btn-sm"
                                                    title="Isi Form APL 2 - Self Assessment">
                                                    <i class="fas fa-file-alt"></i> APL 2
                                                </a>
                                            @endif
                                            <a href="{{ route('asesi.template.apl1-form', $item->id) }}"
                                                class="btn btn-primary btn-sm"
                                                title="Isi Form APL 1 - Data Asesi">
                                                <i class="fas fa-file-word"></i> APL 1
                                            </a>
                                        </div>
                                    @elseif($item->status == 8)
                                        {{-- Menunggu Pembayaran --}}
                                        <a href="{{ route('asesi.informasi-pembayaran.index') }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-money-bill-wave mr-1"></i>Bayar Sekarang
                                        </a>
                                    @elseif(in_array($item->status, [9, 10]))
                                        {{-- Menunggu Ujian / Ujian Berlangsung --}}
                                        <span class="badge badge-success">{{ $item->status_text }}</span>
                                    @elseif($item->status == 11)
                                        {{-- Selesai --}}
                                        <span class="badge badge-dark">{{ $item->status_text }}</span>
                                    @else
                                        <span class="text-muted">{{ $item->status_text }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    @push('scripts')
        <!-- DataTables sudah dimuat global dari layout -->
        <script>
            $(document).ready(function() {
                $('#sertifikasiTable').DataTable({
                    responsive: true,
                    ordering: false,
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
            });
        </script>
    @endpush
@endsection
