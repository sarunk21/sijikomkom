@extends('components.templates.master-layout')

@section('title', 'Template Master - Detail')
@section('page-title', 'Detail Template Master')

@section('content')

    <a href="{{ route('admin.template-master.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali</span>
    </a>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Template</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%" class="font-weight-bold">Nama Template</td>
                            <td>{{ $template->nama_template }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Tipe Template</td>
                            <td><span class="badge badge-info">{{ $template->tipe_template_label }}</span></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Skema</td>
                            <td>
                                <strong>{{ $template->skema->nama }}</strong><br>
                                <small class="text-muted">{{ $template->skema->kode }}</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Deskripsi</td>
                            <td>{{ $template->deskripsi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Status</td>
                            <td>
                                @if($template->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Dibuat Pada</td>
                            <td>{{ $template->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Terakhir Diubah</td>
                            <td>{{ $template->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-code"></i> Variables Template</h5>
                </div>
                <div class="card-body">
                    @php
                        $customVariables = is_string($template->custom_variables) ? json_decode($template->custom_variables, true) : $template->custom_variables;
                        $customVariables = is_array($customVariables) ? $customVariables : [];
                    @endphp

                    @if(count($customVariables) > 0)
                        <div class="row">
                            @foreach($customVariables as $variable)
                                <div class="col-md-6 mb-2">
                                    <div class="border rounded p-2 bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $variable['label'] ?? $variable['name'] ?? $variable }}</strong>
                                                @if(isset($variable['type']))
                                                    <small class="text-muted d-block">Tipe: {{ ucfirst($variable['type']) }}</small>
                                                @endif
                                                @if(isset($variable['options']))
                                                    <small class="text-muted d-block">Opsi: {{ $variable['options'] }}</small>
                                                @endif
                                            </div>
                                            <div>
                                                <code>${ {{ $variable['name'] ?? $variable }} }</code>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Tidak ada custom variables yang didefinisikan.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-file-word"></i> File Template</h5>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-file-word fa-5x text-primary mb-3"></i>
                    <p class="mb-2"><strong>File Template DOCX</strong></p>
                    <a href="{{ route('admin.template-master.download', $template->id) }}"
                        class="btn btn-primary btn-block">
                        <i class="fas fa-download"></i> Download Template
                    </a>
                </div>
            </div>

            @if($template->ttd_path)
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-signature"></i> TTD Digital</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ $template->ttd_url }}"
                            alt="TTD Digital"
                            class="img-fluid border rounded mb-3"
                            style="max-height: 150px;">
                        <p class="mb-0"><small class="text-muted">TTD Digital tersedia</small></p>
                    </div>
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-signature"></i> TTD Digital</h5>
                    </div>
                    <div class="card-body text-center">
                        <p class="text-muted mb-0">TTD Digital belum diupload</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-between">
        <a href="{{ route('admin.template-master.edit', $template->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Template
        </a>
        <form action="{{ route('admin.template-master.destroy', $template->id) }}" method="POST"
            onsubmit="return confirm('Apakah Anda yakin ingin menghapus template ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus Template
            </button>
        </form>
    </div>

    <style>
        .text-orange {
            color: #f25c05;
        }
    </style>

@endsection
