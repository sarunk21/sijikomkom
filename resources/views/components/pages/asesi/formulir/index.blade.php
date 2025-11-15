@extends('components.templates.master-layout')

@section('title', 'Daftar Formulir')
@section('page-title', 'Daftar Formulir - ' . ($jadwal->skema->nama ?? 'Skema'))

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list mr-2"></i>Daftar Formulir
                </h5>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                @if ($bankSoals->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>Belum ada formulir yang tersedia untuk skema ini.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Formulir</th>
                                    <th width="15%">Tipe</th>
                                    <th width="15%">Status</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bankSoals as $index => $bankSoal)
                                    @php
                                        $response = $responses->get($bankSoal->id);
                                        $status = $response ? $response->status : 'not_started';
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $bankSoal->nama }}</strong>
                                            @if ($bankSoal->keterangan)
                                                <br><small class="text-muted">{{ $bankSoal->keterangan }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ strtoupper($bankSoal->tipe) }}</span>
                                        </td>
                                        <td>
                                            @if ($status === 'submitted')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle mr-1"></i>Sudah Submit
                                                </span>
                                            @elseif ($status === 'draft')
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-save mr-1"></i>Draft
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-circle mr-1"></i>Belum Dimulai
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($status === 'submitted')
                                                <a href="{{ route('asesi.formulir.view', [$jadwal->id, $bankSoal->id]) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye mr-1"></i>Lihat
                                                </a>
                                            @else
                                                <a href="{{ route('asesi.formulir.fill', [$jadwal->id, $bankSoal->id]) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    {{ $status === 'draft' ? 'Lanjutkan' : 'Mulai Isi' }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <div class="alert alert-light border">
                            <h6 class="font-weight-bold mb-2">
                                <i class="fas fa-info-circle text-primary mr-2"></i>Keterangan Status:
                            </h6>
                            <ul class="mb-0 pl-3">
                                <li><span class="badge badge-secondary">Belum Dimulai</span> - Formulir belum diisi</li>
                                <li><span class="badge badge-warning">Draft</span> - Formulir sudah diisi sebagian (tersimpan otomatis)</li>
                                <li><span class="badge badge-success">Sudah Submit</span> - Formulir sudah disubmit dan tidak bisa diubah</li>
                            </ul>
                        </div>
                    </div>
                @endif
        </div>
    </div>
@endsection
