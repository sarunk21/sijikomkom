@extends('components.templates.master-layout')

@section('title', 'APL2 - Daftar Portofolio')
@section('page-title', 'APL2 - Daftar Portofolio')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0">APL2 - Daftar Portofolio</h4>
                <p class="text-muted mb-0">Review dan nilai portofolio asesi</p>
            </div>
        </div>

        <!-- Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list"></i> Daftar Portofolio APL2
                </h6>
            </div>
            <div class="card-body">
                @if($pendaftaran->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Asesi</th>
                                    <th>Email</th>
                                    <th>Skema</th>
                                    <th>Status Pengisian</th>
                                    <th>Status Penilaian</th>
                                    <th>Tanggal Submit</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendaftaran as $index => $p)
                                    @php
                                        $hasCustomVariables = !empty($p->custom_variables);
                                        $hasAsesorAssessment = !empty($p->asesor_assessment);
                                        $hasSignature = !empty($p->ttd_asesi_path);
                                        $hasAsesorSignature = !empty($p->ttd_asesor_path);
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $p->user->name ?? 'N/A' }}</td>
                                        <td>{{ $p->user->email ?? 'N/A' }}</td>
                                        <td>{{ $p->skema->nama ?? 'N/A' }}</td>
                                        <td>
                                            @if($hasCustomVariables)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Sudah Mengisi
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> Belum Mengisi
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($hasAsesorAssessment)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Sudah Dinilai
                                                </span>
                                            @elseif($hasCustomVariables)
                                                <span class="badge badge-info">
                                                    <i class="fas fa-eye"></i> Menunggu Penilaian
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-minus"></i> Belum Ada
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($hasCustomVariables)
                                                {{ $p->updated_at->format('d/m/Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('asesor.apl2.show', $p->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i> Review
                                                </a>
                                                @if($hasCustomVariables)
                                                    <a href="{{ route('asesor.apl2.export-docx', $p->id) }}" class="btn btn-success btn-sm">
                                                        <i class="fas fa-download"></i> Export
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada portofolio APL2</h5>
                        <p class="text-muted">Portofolio yang sudah diisi oleh asesi akan muncul di sini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "ordering": false,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                "pageLength": 25,
                "order": [[ 6, "desc" ]]
            });
        });
    </script>
    @endpush
@endsection
