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
                                <td>{{ $item->status_text }}</td>
                                <td>
                                    @if ($item->status == 5)
                                        {{-- Status Ujian Berlangsung - bisa mulai ujian --}}
                                        <a href="{{ route('asesi.ujikom.show', $item->id) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-play me-1"></i>Mulai Ujian
                                        </a>
                                    @elseif ($item->status == 4)
                                        {{-- Status Menunggu Ujian - belum bisa mulai --}}
                                        <span class="badge badge-warning">Menunggu Jadwal</span>
                                    @elseif ($item->status == 6)
                                        {{-- Status Selesai - sudah selesai --}}
                                        <span class="badge badge-success">Selesai</span>
                                    @elseif ($item->status == 7)
                                        {{-- Status Asesor Tidak Dapat Hadir --}}
                                        <span class="badge badge-danger">Asesor Tidak Hadir</span>
                                    @else
                                        {{-- Status lainnya --}}
                                        <span class="badge badge-secondary">{{ $item->status_text }}</span>
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
