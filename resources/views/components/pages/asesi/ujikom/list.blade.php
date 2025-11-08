@extends('components.templates.master-layout')

@section('title', 'Ujikom')
@section('page-title', 'Ujian Kompetensi')

@section('content')
    <div class="card shadow-sm">
        @if (session('success'))
            <div class="alert alert-success mb-3">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif
        <div class="card-body">
            <div class="table-responsive">
                <table id="ujikomTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Skema</th>
                            <th>Tanggal Assesmen</th>
                            <th>TUK</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendaftaran as $item)
                            <tr>
                                <td>{{ $item->skema->nama }}</td>
                                <td>{{ $item->jadwal->tanggal_ujian }}</td>
                                <td>{{ $item->jadwal->tuk->nama }}</td>
                                <td>
                                    @if ($item->pendaftaranUjikom && $item->pendaftaranUjikom->status)
                                        {{-- Tampilkan status dari PendaftaranUjikom --}}
                                        <span class="badge badge-{{
                                            $item->pendaftaranUjikom->status == 1 ? 'primary' :
                                            ($item->pendaftaranUjikom->status == 2 ? 'info' :
                                            ($item->pendaftaranUjikom->status == 3 ? 'success' :
                                            ($item->pendaftaranUjikom->status == 4 ? 'danger' :
                                            ($item->pendaftaranUjikom->status == 5 ? 'success' :
                                            ($item->pendaftaranUjikom->status == 6 ? 'warning' :
                                            ($item->pendaftaranUjikom->status == 7 ? 'danger' : 'secondary'))))))
                                        }}">
                                            {{ $item->pendaftaranUjikom->status_text }}
                                        </span>
                                    @else
                                        <span class="badge badge-{{
                                            $item->status == 1 ? 'info' :
                                            ($item->status == 2 ? 'danger' :
                                            ($item->status == 3 ? 'warning' :
                                            ($item->status == 4 ? 'warning' :
                                            ($item->status == 5 ? 'info' :
                                            ($item->status == 6 ? 'success' :
                                            ($item->status == 7 ? 'danger' : 'secondary'))))))
                                        }}">
                                            {{ $item->status_text }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        // Cek status dari PendaftaranUjikom jika ada, jika tidak gunakan status Pendaftaran
                                        $ujikomStatus = $item->pendaftaranUjikom ? $item->pendaftaranUjikom->status : null;
                                        $pendaftaranStatus = $item->status;
                                    @endphp

                                    @if ($ujikomStatus == 1 || ($pendaftaranStatus == 4 && $item->jadwal->status == 3))
                                        {{-- Status Belum Ujikom atau Menunggu Ujian dengan Jadwal Ujian Berlangsung - bisa mulai ujian --}}
                                        <a href="{{ route('asesi.ujikom.show', $item->id) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-play me-1"></i>Mulai Ujian
                                        </a>
                                    @elseif ($ujikomStatus == 2)
                                        {{-- Status Ujikom Berlangsung - sedang mengerjakan --}}
                                        <a href="{{ route('asesi.ujikom.show', $item->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit me-1"></i>Lanjutkan Ujian
                                        </a>
                                    @elseif ($ujikomStatus == 3)
                                        {{-- Status Ujikom Selesai - menunggu penilaian asesor --}}
                                        <span class="badge badge-info">Menunggu Penilaian</span>
                                    @elseif ($ujikomStatus == 4)
                                        {{-- Status Tidak Kompeten --}}
                                        <span class="badge badge-danger">Tidak Kompeten</span>
                                    @elseif ($ujikomStatus == 5)
                                        {{-- Status Kompeten --}}
                                        <span class="badge badge-success">Kompeten</span>
                                    @elseif ($ujikomStatus == 6)
                                        {{-- Status Menunggu Konfirmasi Asesor --}}
                                        <span class="badge badge-warning">Menunggu Konfirmasi Asesor</span>
                                    @elseif ($ujikomStatus == 7 || $pendaftaranStatus == 7)
                                        {{-- Status Asesor Tidak Dapat Hadir --}}
                                        <span class="badge badge-danger">Asesor Tidak Hadir</span>
                                    @elseif ($pendaftaranStatus == 4)
                                        {{-- Status Menunggu Ujian - belum waktunya --}}
                                        <span class="badge badge-warning">Menunggu Jadwal</span>
                                    @elseif ($pendaftaranStatus == 1)
                                        {{-- Status Menunggu Verifikasi Kaprodi --}}
                                        <span class="badge badge-info">Menunggu Verifikasi</span>
                                    @elseif ($pendaftaranStatus == 2)
                                        {{-- Status Tidak Lolos Verifikasi --}}
                                        <span class="badge badge-danger">Tidak Lolos Verifikasi</span>
                                    @elseif ($pendaftaranStatus == 3)
                                        {{-- Status Menunggu Verifikasi Admin --}}
                                        <span class="badge badge-warning">Menunggu Verifikasi Admin</span>
                                    @else
                                        {{-- Status lainnya --}}
                                        <span class="badge badge-secondary">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
                $('#ujikomTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari Ujian Kompetensi...",
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
