@extends('components.templates.master-layout')

@section('title', 'Bank Soal')
@section('page-title', 'Bank Soal')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center shadow-sm mb-4" style="border-left: 4px solid #dc3545; background-color: #f8d7da; border-color: #f5c6cb;">
            <i class="fas fa-exclamation-circle me-3" style="font-size: 1.5rem; color: #dc3545;"></i>
            <div style="color: #721c24;">{{ session('error') }}</div>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center shadow-sm mb-4" style="border-left: 4px solid #28a745; background-color: #d4edda; border-color: #c3e6cb;">
            <i class="fas fa-check-circle me-3" style="font-size: 1.5rem; color: #28a745;"></i>
            <div style="color: #155724;">{{ session('success') }}</div>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Bank Soal / Formulir</h4>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Kelola bank soal dan formulir asesmen per skema</p>
        </div>
        <div>
            <a href="{{ route('admin.bank-soal.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Bank Soal
            </a>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form action="{{ route('admin.bank-soal.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <label for="skema_id" class="form-label">Filter Skema</label>
                        <select name="skema_id" id="skema_id" class="form-control">
                            <option value="">Semua Skema</option>
                            @foreach($skemas as $skema)
                                <option value="{{ $skema->id }}" {{ request('skema_id') == $skema->id ? 'selected' : '' }}>
                                    {{ $skema->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="tipe" class="form-label">Filter Tipe</label>
                        <select name="tipe" id="tipe" class="form-control">
                            <option value="">Semua Tipe</option>
                            <option value="FR IA 03" {{ request('tipe') == 'FR IA 03' ? 'selected' : '' }}>FR IA 03</option>
                            <option value="FR IA 06" {{ request('tipe') == 'FR IA 06' ? 'selected' : '' }}>FR IA 06</option>
                            <option value="FR IA 07" {{ request('tipe') == 'FR IA 07' ? 'selected' : '' }}>FR IA 07</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="is_active" class="form-label">Filter Status</label>
                        <select name="is_active" id="is_active" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('admin.bank-soal.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="bankSoalTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama Bank Soal</th>
                            <th>Tipe</th>
                            <th>Target</th>
                            <th>Skema</th>
                            <th>Status</th>
                            <th>Tanggal Dibuat</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bankSoals as $bankSoal)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $bankSoal->nama }}</strong>
                                        @if($bankSoal->keterangan)
                                            <br><small class="text-muted">{{ Str::limit($bankSoal->keterangan, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($bankSoal->tipe == 'FR IA 03')
                                        <span class="badge badge-primary">{{ $bankSoal->tipe }}</span>
                                    @elseif($bankSoal->tipe == 'FR IA 06')
                                        <span class="badge badge-info">{{ $bankSoal->tipe }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ $bankSoal->tipe }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($bankSoal->target == 'asesi')
                                        <span class="badge badge-success">Asesi</span>
                                    @else
                                        <span class="badge badge-secondary">Asesor</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $bankSoal->skema->nama }}</strong>
                                        <br><small class="text-muted">{{ $bankSoal->skema->kode }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($bankSoal->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>{{ $bankSoal->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                                        <a href="{{ route('admin.bank-soal.edit', $bankSoal->id) }}"
                                            class="btn btn-light btn-icon btn-sm border shadow-sm" title="Edit">
                                            <i class="fas fa-edit text-warning"></i>
                                        </a>
                                        <form action="{{ route('admin.bank-soal.destroy', $bankSoal->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus bank soal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light btn-icon btn-sm border shadow-sm" title="Hapus">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Tidak ada data bank soal</td>
                            </tr>
                        @endforelse
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
            width: 32px;
            height: 32px;
            padding: 0;
        }
    </style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#bankSoalTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[6, 'desc']], // Sort by tanggal dibuat
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endpush
