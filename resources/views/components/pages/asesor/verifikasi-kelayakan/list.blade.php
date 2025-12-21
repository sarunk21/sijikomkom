@extends('components.templates.master-layout')

@section('title', 'Verifikasi Kelayakan')
@section('page-title', 'Verifikasi Kelayakan Asesi')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-clipboard-check mr-2"></i>Daftar Pendaftaran untuk Verifikasi Kelayakan
        </h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <!-- Filter -->
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <label>Filter Jadwal:</label>
                    <select name="jadwal_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua Jadwal</option>
                        @foreach($jadwalList as $jadwal)
                            <option value="{{ $jadwal->id }}" {{ request('jadwal_id') == $jadwal->id ? 'selected' : '' }}>
                                {{ $jadwal->skema->nama ?? '-' }} - {{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('d M Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover" id="verifikasiTable">
                <thead class="thead-light">
                    <tr>
                        <th>Tanggal Pendaftaran</th>
                        <th>Nama Asesi</th>
                        <th>Skema</th>
                        <th>Jadwal Ujian</th>
                        <th>TUK</th>
                        <th>Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftaranList as $item)
                        <tr>
                            <td>{{ $item->pendaftaran->created_at->format('d-m-Y H:i') }}</td>
                            <td>
                                <strong>{{ $item->pendaftaran->user->name }}</strong><br>
                                <small class="text-muted">{{ $item->pendaftaran->user->email }}</small>
                            </td>
                            <td>{{ $item->pendaftaran->skema->nama ?? '-' }}</td>
                            <td>{{ $item->pendaftaran->jadwal->tanggal_ujian ? \Carbon\Carbon::parse($item->pendaftaran->jadwal->tanggal_ujian)->format('d M Y') : '-' }}</td>
                            <td>{{ $item->pendaftaran->tuk->nama ?? '-' }}</td>
                            <td>
                                <span class="badge badge-warning">
                                    {{ $item->pendaftaran->status_text }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('asesor.verifikasi-kelayakan.show', $item->pendaftaran->id) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye mr-1"></i>Verifikasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Tidak ada pendaftaran yang menunggu verifikasi kelayakan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#verifikasiTable').DataTable({
        "pageLength": 25,
        "ordering": false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });
});
</script>
@endpush
@endsection

