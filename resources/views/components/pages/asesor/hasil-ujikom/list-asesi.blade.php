@extends('components.templates.master-layout')

@section('title', 'Asesmen')
@section('page-title', 'Asesmen')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">
                    <b>{{ $jadwal->skema->nama }}</b> - <b>{{ $jadwal->tuk->nama }}</b>
                </h5>
                <p class="card-text">
                    {{ $jadwal->tanggal_ujian }}
                </p>
            </div>

            <div class="mb-3">
                <a href="https://{{ $apl2->link_ujikom_asesor }}" target="_blank"
                    class="btn btn-outline-primary btn-sm shadow-sm">
                    Link Ujikom Asesor
                </a>
            </div>

            <div class="table-responsive">
                <table id="asesiTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama Asesi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($asesi as $item)
                            <tr>
                                <td>{{ $item->asesi->nama }}</td>
                                <td>{{ $item->asesi->status_text }}</td>
                                <td>
                                    @if ($item->status == 1)
                                        <form action="{{ route('asesor.hasil-ujikom.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="2">
                                            <button type="submit" class="btn btn-outline-warning btn-sm shadow-sm">
                                                Kompeten
                                            </button>
                                        </form>
                                    @elseif ($item->status == 2)
                                        <form action="{{ route('asesor.hasil-ujikom.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="3">
                                            <button type="submit" class="btn btn-outline-danger btn-sm shadow-sm">
                                                Tidak Kompeten
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge badge-warning">
                                            Belum Ujikom
                                        </span>
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
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#asesiTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari asesi...",
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
