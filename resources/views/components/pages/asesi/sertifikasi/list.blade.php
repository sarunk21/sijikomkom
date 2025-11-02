@extends('components.templates.master-layout')

@section('title', 'Sertifikasi')
@section('page-title', 'Sertifikasi')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="sertifikasiTable" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Skema</th>
                            <th>Tanggal Assesmen</th>
                            <th>TUK</th>
                            <th>Status</th>
                            <th>Status APL2</th>
                            <th>Keterangan</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendaftaran as $item)
                            <tr>
                                <td>{{ $item->skema->nama }}</td>
                                <td>{{ $item->jadwal->tanggal_ujian }}</td>
                                <td>{{ $item->jadwal->tuk->nama }}</td>
                                <td>
                                    @if($item->status == 4)
                                        <span class="badge badge-warning">{{ $item->status_text }}</span>
                                    @elseif($item->status == 6)
                                        <span class="badge badge-success">{{ $item->status_text }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $item->status_text }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $isReviewedByAsesor = !empty($item->asesor_assessment);
                                    @endphp

                                    @if($isReviewedByAsesor)
                                        <span class="badge badge-danger">Direview Asesor</span>
                                    @else
                                        <span class="badge badge-secondary">Belum Diisi</span>
                                    @endif
                                </td>
                                <td>{{ $item->pendaftaranUjikom ? $item->pendaftaranUjikom->keterangan : '-' }}</td>
                                <td class="text-center">
                                    @if(in_array($item->status, [3, 4, 5]))
                                        {{-- Status yang bisa generate APL 1 dan APL 2 --}}
                                        <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 0.5rem;">
                                            @php
                                                $isReviewedByAsesor = !empty($item->asesor_assessment);
                                            @endphp

                                            @if($isReviewedByAsesor)
                                                <button type="button"
                                                    class="btn btn-secondary btn-sm"
                                                    title="APL2 sudah direview asesor"
                                                    disabled>
                                                    <i class="fas fa-lock"></i> APL 2 (Locked)
                                                </button>
                                            @else
                                                <a href="{{ route('asesi.sertifikasi.apl2', $item->id) }}"
                                                    class="btn btn-warning btn-sm"
                                                    title="Isi Form APL 2">
                                                    <i class="fas fa-file-alt"></i> APL 2
                                                </a>
                                            @endif
                                            <a href="{{ route('asesi.template.generate-apl1', $item->id) }}"
                                                class="btn btn-primary btn-sm"
                                                title="Generate APL 1">
                                                <i class="fas fa-file-word"></i> APL 1
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted">Status: {{ $item->status_text }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    @push('scripts')
        <!-- DataTables sudah dimuat global dari layout -->
        <script>
            $(document).ready(function() {
                $('#sertifikasiTable').DataTable({
                    responsive: true,
                    language: {
                        searchPlaceholder: "Cari Sertifikasi...",
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
