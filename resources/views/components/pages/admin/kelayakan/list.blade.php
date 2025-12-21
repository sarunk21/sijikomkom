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

        <!-- Batch Actions -->
        @if($pendaftaranList->count() > 0)
        <div class="mb-3">
            <button type="button" class="btn btn-success" id="batchApproveBtn" disabled>
                <i class="fas fa-check-double mr-2"></i>Approve Terpilih (<span id="selectedCount">0</span>)
            </button>
            <button type="button" class="btn btn-danger" id="batchRejectBtn" data-toggle="modal" data-target="#batchRejectModal" disabled>
                <i class="fas fa-times-circle mr-2"></i>Tolak Terpilih
            </button>
            <button type="button" class="btn btn-secondary btn-sm" id="selectAllBtn">
                <i class="fas fa-check-square mr-1"></i>Pilih Semua
            </button>
            <button type="button" class="btn btn-secondary btn-sm" id="deselectAllBtn">
                <i class="fas fa-square mr-1"></i>Batal Pilih
            </button>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover" id="kelayakanTable">
                <thead class="thead-light">
                    <tr>
                        <th width="3%">
                            <input type="checkbox" id="checkAll" title="Pilih Semua">
                        </th>
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
                            <td>
                                <input type="checkbox" class="pendaftaran-checkbox" value="{{ $pendaftaran->id }}" 
                                       data-name="{{ $pendaftaran->user->name }}">
                            </td>
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
                            <td colspan="9" class="text-center text-muted py-4">
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

<!-- Batch Reject Modal -->
<div class="modal fade" id="batchRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="batchRejectForm" method="POST" action="{{ route('admin.kelayakan.batch-reject') }}">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Tolak Kelayakan Batch</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Yakin ingin menolak <strong id="batchRejectCount">0</strong> pendaftaran yang dipilih?</p>
                    <div id="batchRejectList" class="mb-3 small"></div>
                    <div class="form-group">
                        <label>Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="keterangan" class="form-control" rows="3" required 
                                  placeholder="Masukkan alasan penolakan untuk semua pendaftaran yang dipilih..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Semua Pendaftaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#kelayakanTable').DataTable({
        "pageLength": 25,
        "ordering": false,
        "columnDefs": [
            { "orderable": false, "targets": 0 } // Disable sorting on checkbox column
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Update selected count and enable/disable batch buttons
    function updateBatchButtons() {
        const selectedCount = $('.pendaftaran-checkbox:checked').length;
        $('#selectedCount').text(selectedCount);
        
        if (selectedCount > 0) {
            $('#batchApproveBtn').prop('disabled', false);
            $('#batchRejectBtn').prop('disabled', false);
        } else {
            $('#batchApproveBtn').prop('disabled', true);
            $('#batchRejectBtn').prop('disabled', true);
        }
    }

    // Check/uncheck all
    $('#checkAll').on('change', function() {
        $('.pendaftaran-checkbox').prop('checked', $(this).prop('checked'));
        updateBatchButtons();
    });

    // Individual checkbox change
    $('.pendaftaran-checkbox').on('change', function() {
        const totalCheckboxes = $('.pendaftaran-checkbox').length;
        const checkedCheckboxes = $('.pendaftaran-checkbox:checked').length;
        $('#checkAll').prop('checked', totalCheckboxes === checkedCheckboxes);
        updateBatchButtons();
    });

    // Select all button
    $('#selectAllBtn').on('click', function() {
        $('.pendaftaran-checkbox').prop('checked', true);
        $('#checkAll').prop('checked', true);
        updateBatchButtons();
    });

    // Deselect all button
    $('#deselectAllBtn').on('click', function() {
        $('.pendaftaran-checkbox').prop('checked', false);
        $('#checkAll').prop('checked', false);
        updateBatchButtons();
    });

    // Batch approve
    $('#batchApproveBtn').on('click', function() {
        const selectedIds = [];
        const selectedNames = [];
        
        $('.pendaftaran-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
            selectedNames.push($(this).data('name'));
        });

        if (selectedIds.length === 0) {
            alert('Pilih minimal 1 pendaftaran untuk diapprove');
            return;
        }

        const namesList = selectedNames.map(name => '- ' + name).join('\n');
        const confirmMsg = 'Yakin ingin approve ' + selectedIds.length + ' pendaftaran berikut?\n\n' + namesList;
        
        if (confirm(confirmMsg)) {
            // Create form and submit
            const form = $('<form>', {
                'method': 'POST',
                'action': '{{ route("admin.kelayakan.batch-approve") }}'
            });

            form.append($('<input>', {
                'type': 'hidden',
                'name': '_token',
                'value': '{{ csrf_token() }}'
            }));

            selectedIds.forEach(function(id) {
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'pendaftaran_ids[]',
                    'value': id
                }));
            });

            $('body').append(form);
            form.submit();
        }
    });

    // Show batch reject modal with selected items
    $('#batchRejectBtn').on('click', function() {
        const selectedIds = [];
        const selectedNames = [];
        
        $('.pendaftaran-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
            selectedNames.push($(this).data('name'));
        });

        $('#batchRejectCount').text(selectedIds.length);
        
        // Update list
        const listHtml = '<ul class="list-unstyled">' + 
            selectedNames.map(name => '<li><i class="fas fa-user mr-2"></i>' + name + '</li>').join('') + 
            '</ul>';
        $('#batchRejectList').html(listHtml);

        // Clear previous hidden inputs
        $('#batchRejectForm input[name="pendaftaran_ids[]"]').remove();

        // Add hidden inputs for selected IDs
        selectedIds.forEach(function(id) {
            $('#batchRejectForm').append($('<input>', {
                'type': 'hidden',
                'name': 'pendaftaran_ids[]',
                'value': id
            }));
        });
    });
});
</script>
@endpush
@endsection

