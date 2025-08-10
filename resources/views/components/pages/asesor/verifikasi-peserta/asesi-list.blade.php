@extends('components.templates.master-layout')

@section('title', 'List Asesi - Verifikasi Peserta')
@section('page-title', 'List Asesi')

@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Detail Jadwal</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="150"><strong>Skema:</strong></td>
                            <td>{{ $jadwal->jadwal->skema->nama }}</td>
                        </tr>
                        <tr>
                            <td><strong>TUK:</strong></td>
                            <td>{{ $jadwal->jadwal->tuk->nama }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Ujian:</strong></td>
                            <td>{{ \Carbon\Carbon::parse($jadwal->jadwal->tanggal_ujian)->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Selesai:</strong></td>
                            <td>{{ \Carbon\Carbon::parse($jadwal->jadwal->tanggal_selesai)->format('d-m-Y') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="150"><strong>Status Jadwal:</strong></td>
                            <td>
                                <span class="badge badge-{{ $jadwal->jadwal->status == 5 ? 'warning' : ($jadwal->jadwal->status == 6 ? 'info' : 'success') }}">
                                    {{ $jadwal->jadwal->status_text }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Kuota:</strong></td>
                            <td>{{ $jadwal->jadwal->kuota }} peserta</td>
                        </tr>
                        <tr>
                            <td><strong>Jumlah Asesi:</strong></td>
                            <td>{{ $asesiList->count() }} peserta</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Asesi</h6>
            <a href="{{ route('asesor.verifikasi-peserta.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="asesiTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama Asesi</th>
                            <th>NIM</th>
                            <th>Telepon</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($asesiList as $asesi)
                            <tr>
                                <td>{{ $asesi->asesi->name }}</td>
                                <td>{{ $asesi->asesi->nim }}</td>
                                <td>{{ $asesi->asesi->telephone }}</td>
                                <td>
                                    <span class="badge badge-{{ $asesi->pendaftaran->status == 4 ? 'info' : ($asesi->pendaftaran->status == 7 ? 'danger' : 'success') }}">
                                        {{ $asesi->pendaftaran->status_text }}
                                    </span>
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
                $('#asesiTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari Asesi...",
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
            });
        </script>
    @endpush
@endsection
