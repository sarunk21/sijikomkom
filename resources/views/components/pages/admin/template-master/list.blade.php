@extends('components.templates.master-layout')

@section('title', 'Template Master')
@section('page-title', 'Template Master')

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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Master Template</h4>
            <p class="text-muted mb-0">Kelola template dokumen untuk setiap skema dan tipe asesmen</p>
            <div class="alert alert-info mt-2 mb-0" style="font-size: 0.9rem;">
                <i class="fas fa-info-circle"></i>
                <strong>Petunjuk:</strong> Download sample template untuk melihat format variable yang bisa digunakan.
                Gunakan format <code>${variable}</code> di file DOCX untuk variable yang dipilih.
            </div>
        </div>
        <div>
            <a href="{{ asset('storage/templates/sample_apl1_template.docx') }}" class="btn btn-success me-2" download>
                <i class="fas fa-download"></i> Download Sample Template
            </a>
            <a href="{{ route('admin.template-master.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Template
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="templateTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama Template</th>
                            <th>Tipe Template</th>
                            <th>Skema</th>
                            <th>Variables</th>
                            <th>TTD</th>
                            <th>Status</th>
                            <th>Tanggal Dibuat</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($templates as $template)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $template->nama_template }}</strong>
                                        @if($template->deskripsi)
                                            <br><small class="text-muted">{{ Str::limit($template->deskripsi, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $template->tipe_template_label }}</span>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $template->skema->nama }}</strong>
                                        <br><small class="text-muted">{{ $template->skema->kode }}</small>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $variables = is_string($template->variables) ? json_decode($template->variables, true) : $template->variables;
                                        $variables = is_array($variables) ? $variables : [];
                                    @endphp
                                    @if($variables && count($variables) > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(array_slice($variables, 0, 3) as $variable)
                                                <span class="badge badge-secondary badge-sm">{{ $variable }}</span>
                                            @endforeach
                                            @if(count($variables) > 3)
                                                <span class="badge badge-light badge-sm">+{{ count($variables) - 3 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($template->ttd_path)
                                        <i class="fas fa-check-circle text-success" title="TTD tersedia"></i>
                                    @else
                                        <i class="fas fa-times-circle text-muted" title="TTD tidak tersedia"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($template->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>{{ $template->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                                        <a href="{{ route('admin.template-master.show', $template->id) }}"
                                            class="btn btn-light btn-icon btn-sm border shadow-sm" title="Detail">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>
                                        <a href="{{ route('admin.template-master.edit', $template->id) }}"
                                            class="btn btn-light btn-icon btn-sm border shadow-sm" title="Edit">
                                            <i class="fas fa-edit text-warning"></i>
                                        </a>
                                        <a href="{{ route('admin.template-master.download', $template->id) }}"
                                            class="btn btn-light btn-icon btn-sm border shadow-sm" title="Download">
                                            <i class="fas fa-download text-info"></i>
                                        </a>
                                        <form action="{{ route('admin.template-master.toggle-status', $template->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-light btn-icon btn-sm border shadow-sm"
                                                title="{{ $template->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                onclick="return confirm('{{ $template->is_active ? 'Nonaktifkan' : 'Aktifkan' }} template ini?')">
                                                @if($template->is_active)
                                                    <i class="fas fa-toggle-on text-success"></i>
                                                @else
                                                    <i class="fas fa-toggle-off text-muted"></i>
                                                @endif
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.template-master.destroy', $template->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus template ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light btn-icon btn-sm border shadow-sm" title="Hapus">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
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

        .badge-sm {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
    </style>

    {{-- Scripts --}}
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#templateTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari Template...",
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
