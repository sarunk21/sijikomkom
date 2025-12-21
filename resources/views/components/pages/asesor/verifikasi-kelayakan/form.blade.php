@extends('components.templates.master-layout')

@section('title', 'Form Verifikasi Kelayakan')
@section('page-title', 'Verifikasi Kelayakan - ' . $pendaftaran->user->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Form Verifikasi -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-check mr-2"></i>Form Verifikasi Kelayakan
                </h5>
            </div>
            <div class="card-body">
                @if($existingVerifikasi)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Sudah Diverifikasi</strong><br>
                        Anda telah memberikan verifikasi untuk pendaftaran ini pada {{ $existingVerifikasi->verified_at->format('d M Y H:i') }}.
                    </div>
                @endif

                <form method="POST" action="{{ route('asesor.verifikasi-kelayakan.store', $pendaftaran->id) }}">
                    @csrf
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Status Kelayakan <span class="text-danger">*</span></label>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="status_layak" name="status" value="1" 
                                   class="custom-control-input" 
                                   {{ old('status', $existingVerifikasi->status ?? '') == 1 ? 'checked' : '' }} 
                                   required>
                            <label class="custom-control-label" for="status_layak">
                                <span class="badge badge-success">✓ LAYAK</span>
                                <small class="d-block text-muted">Asesi memenuhi kriteria dan dokumen lengkap</small>
                            </label>
                        </div>
                        <div class="custom-control custom-radio mt-2">
                            <input type="radio" id="status_tidak_layak" name="status" value="2" 
                                   class="custom-control-input" 
                                   {{ old('status', $existingVerifikasi->status ?? '') == 2 ? 'checked' : '' }} 
                                   required>
                            <label class="custom-control-label" for="status_tidak_layak">
                                <span class="badge badge-danger">✗ TIDAK LAYAK</span>
                                <small class="d-block text-muted">Asesi tidak memenuhi kriteria atau dokumen tidak lengkap</small>
                            </label>
                        </div>
                        @error('status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="catatan" class="font-weight-bold">Catatan / Keterangan</label>
                        <textarea name="catatan" id="catatan" rows="4" class="form-control" 
                                  placeholder="Masukkan catatan atau alasan verifikasi...">{{ old('catatan', $existingVerifikasi->catatan ?? '') }}</textarea>
                        <small class="text-muted">Wajib diisi jika status TIDAK LAYAK</small>
                        @error('catatan')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="border-top pt-3 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save mr-2"></i>Simpan Verifikasi
                        </button>
                        <a href="{{ route('asesor.verifikasi-kelayakan.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Info Asesi -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-user mr-2"></i>Informasi Asesi</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%"><strong>Nama</strong></td>
                        <td>: {{ $pendaftaran->user->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>NIM</strong></td>
                        <td>: {{ $pendaftaran->user->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td>: {{ $pendaftaran->user->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Telepon</strong></td>
                        <td>: {{ $pendaftaran->user->telepon ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jurusan</strong></td>
                        <td>: {{ $pendaftaran->user->jurusan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Info Jadwal -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="fas fa-calendar mr-2"></i>Informasi Jadwal</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%"><strong>Skema</strong></td>
                        <td>: {{ $pendaftaran->skema->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>TUK</strong></td>
                        <td>: {{ $pendaftaran->tuk->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Ujian</strong></td>
                        <td>: {{ $pendaftaran->jadwal->tanggal_ujian ? \Carbon\Carbon::parse($pendaftaran->jadwal->tanggal_ujian)->format('d M Y') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Dokumen -->
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0"><i class="fas fa-file mr-2"></i>Dokumen Persyaratan</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-file-pdf text-danger mr-2"></i>
                        @if($pendaftaran->user->photo_ktp)
                            <a href="{{ asset('storage/' . $pendaftaran->user->photo_ktp) }}" target="_blank">KTP</a>
                        @else
                            <span class="text-muted">KTP (Belum Upload)</span>
                        @endif
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-file-pdf text-danger mr-2"></i>
                        @if($pendaftaran->user->photo_sertifikat)
                            <a href="{{ asset('storage/' . $pendaftaran->user->photo_sertifikat) }}" target="_blank">Sertifikat</a>
                        @else
                            <span class="text-muted">Sertifikat (Belum Upload)</span>
                        @endif
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-file-pdf text-danger mr-2"></i>
                        @if($pendaftaran->user->photo_ktmkhs)
                            <a href="{{ asset('storage/' . $pendaftaran->user->photo_ktmkhs) }}" target="_blank">KTM/KHS</a>
                        @else
                            <span class="text-muted">KTM/KHS (Belum Upload)</span>
                        @endif
                    </li>
                    <li>
                        <i class="fas fa-file-pdf text-danger mr-2"></i>
                        @if($pendaftaran->user->photo_administatif)
                            <a href="{{ asset('storage/' . $pendaftaran->user->photo_administatif) }}" target="_blank">Dokumen Administratif</a>
                        @else
                            <span class="text-muted">Dokumen Administratif (Belum Upload)</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Validate catatan wajib jika status tidak layak
    $('form').on('submit', function(e) {
        var status = $('input[name="status"]:checked').val();
        var catatan = $('#catatan').val().trim();
        
        if (status == '2' && catatan == '') {
            e.preventDefault();
            alert('Catatan wajib diisi jika status TIDAK LAYAK!');
            $('#catatan').focus();
            return false;
        }
    });
});
</script>
@endpush
@endsection

