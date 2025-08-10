@extends('components.templates.master-layout')

@section('title', 'Informasi User')
@section('page-title', 'Informasi User')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.user.create') }}" class="btn btn-dark"><i class="fas fa-plus mr-2"></i> Tambah User</a>
        <button type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#importModal">
            <i class="fas fa-file-upload mr-2"></i> Import Data (CSV/Excel)
        </button>
        <a href="{{ route('admin.user.import.template') }}" class="btn btn-outline-secondary ml-2">
            <i class="fas fa-download mr-2"></i> Download Template CSV
        </a>
        <a href="{{ route('admin.user.import.template.excel') }}" class="btn btn-outline-success ml-2">
            <i class="fas fa-file-excel mr-2"></i> Download Template Excel
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="userTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-center" style="width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $user->user_type)) }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                                        @if ($user->user_type === 'asesor_nonaktif')
                                            <form action="{{ route('admin.user.aktifkan', $user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-light btn-icon btn-sm border shadow-sm"
                                                    title="Aktifkan">
                                                    <i class="fas fa-check text-success"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if ($user->user_type === 'asesor')
                                            <form action="{{ route('admin.user.nonaktifkan', $user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-light btn-icon btn-sm border shadow-sm"
                                                    title="Nonaktifkan">
                                                    <i class="fas fa-times text-danger"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.user.edit', $user->id) }}"
                                            class="btn btn-light btn-icon btn-sm border shadow-sm" title="Edit">
                                            <i class="fas fa-pen text-primary"></i>
                                        </a>
                                        <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light btn-icon btn-sm border shadow-sm"
                                                title="Hapus">
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

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import User (CSV/Excel)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.user.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <div><strong>Format file</strong>: .csv, .xlsx, .xls (disarankan gunakan Template Excel).</div>
                            <div><strong>Header yang didukung</strong>:
                                <ul class="mb-1">
                                    <li>Readable: <em>Name, NIK, NIM, Telephone, Email, Tempat Lahir, Tanggal Lahir, Jenis Kelamin, Alamat, Kebangsaan, Pekerjaan, Pendidikan, Jurusan, User Type</em></li>
                                    <li>Snake case: <code>name, nik, nim, telephone, email, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, kebangsaan, pekerjaan, pendidikan, jurusan, user_type</code></li>
                                </ul>
                            </div>
                            <div><strong>Catatan</strong>:
                                <ul class="mb-0">
                                    <li>Password otomatis sama dengan email user.</li>
                                    <li>Template Excel sudah aman untuk angka (nol di depan tidak hilang).</li>
                                    <li>Ukuran maks. 10 MB.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="file">Pilih File CSV</label>
                            <input type="file" name="file" id="file" class="form-control" accept=".csv,.xlsx,.xls" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
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
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#userTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari User...",
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
