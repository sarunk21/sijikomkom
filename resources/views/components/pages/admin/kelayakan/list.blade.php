@extends('components.templates.master-layout')

@section('title', 'Approval Kelayakan')
@section('page-title', 'Approval Kelayakan Pendaftaran')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-check-double mr-2"></i>Daftar Pendaftaran Menunggu Approval Kelayakan
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
                <div class="col-md-3">
                    <label>Dari Tanggal:</label>
                    <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
                </div>
                <div class="col-md-3">
                    <label>Sampai Tanggal:</label>
                    <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
                </div>
                <div class="col-md-3">
                    <label>Skema:</label>
                    <select name="skema_id" class="form-control">
                        <option value="">Semua Skema</option>
                        @foreach($skemas as $skema)
                            <option value="{{ $skema->id }}" {{ request('skema_id') == $skema->id ? 'selected' : '' }}>
                                {{ $skema->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover" id="kelayakanTable">
                <thead class="thead-light">
                    <tr>
                        <th>Tanggal Pendaftaran</th>
                        <th>Nama Asesi</th>
                        <th>Skema</th>
                        <th>Jadwal Ujian</th>
                        <th>TUK</th>
                        <th>Diverifikasi oleh</th>
                        <th>Catatan Asesor</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftaranList as $pendaftaran)
                        @php
                            $verifikasi = $pendaftaran->kelayankanVerifikasi->first();
                        @endphp
                        <tr>
                            <td>{{ $pendaftaran->created_at->format('d-m-Y H:i') }}</td>
                            <td>
                                <strong>{{ $pendaftaran->user->name }}</strong><br>
                                <small class="text-muted">{{ $pendaftaran->user->email }}</small>
                            </td>
                            <td>{{ $pendaftaran->skema->nama ?? '-' }}</td>
                            <td>{{ $pendaftaran->jadwal->tanggal_ujian ? \Carbon\Carbon::parse($pendaftaran->jadwal->tanggal_ujian)->format('d M Y') : '-' }}</td>
                            <td>{{ $pendaftaran->tuk->nama ?? '-' }}</td>
                            <td>
                                @if($verifikasi)
                                    {{ $verifikasi->asesor->name }}<br>
                                    <small class="text-muted">{{ $verifikasi->verified_at->format('d M Y H:i') }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($verifikasi && $verifikasi->catatan)
                                    <small>{{ Str::limit($verifikasi->catatan, 50) }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <form action="{{ route('admin.kelayakan.approve', $pendaftaran->id) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Setujui kelayakan pendaftaran ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            data-toggle="modal" 
                                            data-target="#rejectModal{{ $pendaftaran->id }}"
                                            title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $pendaftaran->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.kelayakan.reject', $pendaftaran->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Tolak Kelayakan</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Yakin ingin menolak pendaftaran <strong>{{ $pendaftaran->user->name }}</strong>?</p>
                                                    <div class="form-group">
                                                        <label>Alasan Penolakan <span class="text-danger">*</span></label>
                                                        <textarea name="keterangan" class="form-control" rows="3" required 
                                                                  placeholder="Masukkan alasan penolakan..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Tolak Pendaftaran</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Tidak ada pendaftaran yang menunggu approval kelayakan.
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
    $('#kelayakanTable').DataTable({
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

