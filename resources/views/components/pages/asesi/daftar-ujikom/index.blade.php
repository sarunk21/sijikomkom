@extends('components.templates.master-layout')

@section('title', 'Daftar Ujikom')
@section('page-title', 'Daftar Ujikom')

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

    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    <!-- Registration Info Card -->
    @if($registrationInfo['has_previous_registration'])
        <div class="card border-left-warning shadow mb-4">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            <i class="fas fa-info-circle"></i> Informasi Pendaftaran Kedua
                        </div>
                        <div class="h6 mb-0 text-gray-800">
                            @if($registrationInfo['last_payment'])
                                Status Pembayaran Terakhir: <strong>{{ $registrationInfo['last_payment']->status_text }}</strong>
                                @if($registrationInfo['last_payment']->keterangan)
                                    <br><small class="text-muted">{{ $registrationInfo['last_payment']->keterangan }}</small>
                                @endif
                            @else
                                Anda sudah pernah mendaftar sebelumnya
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('asesi.daftar-ujikom.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="jadwal_id" class="form-label">Jadwal Ujian <span class="text-danger">*</span></label>
                    <select name="jadwal_id" id="jadwal_id" class="form-control @error('jadwal_id') is-invalid @enderror"
                        required>
                        <option value="" disabled selected>Pilih Jadwal Ujian di sini...</option>
                        @foreach ($jadwal as $item)
                            <option value="{{ $item->id }}" {{ old('jadwal_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->skema->nama }} - {{ $item->tanggal_ujian }} - {{ $item->tuk->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('jadwal_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="photo_ktp" class="form-label">
                        Foto KTP
                        @if(!auth()->user()->photo_ktp)
                            <span class="text-danger">*</span>
                        @else
                            <small class="text-muted">(Opsional - sudah ada file sebelumnya)</small>
                        @endif
                    </label>
                    @if(auth()->user()->photo_ktp)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . auth()->user()->photo_ktp) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat File Sebelumnya
                            </a>
                        </div>
                    @endif
                    <input type="file" id="photo_ktp" name="photo_ktp"
                        class="form-control @error('photo_ktp') is-invalid @enderror"
                        accept="image/jpeg,image/png,image/jpg,.pdf"
                        {{ auth()->user()->photo_ktp ? '' : 'required' }}>
                    <small class="form-text text-muted">Format: JPG, PNG, PDF | Maksimal 2MB</small>
                    @error('photo_ktp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="photo_sertifikat" class="form-label">
                        Surat Rekomendasi
                        @if(!auth()->user()->photo_sertifikat)
                            <span class="text-danger">*</span>
                        @else
                            <small class="text-muted">(Opsional - sudah ada file sebelumnya)</small>
                        @endif
                    </label>
                    @if(auth()->user()->photo_sertifikat)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . auth()->user()->photo_sertifikat) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat File Sebelumnya
                            </a>
                        </div>
                    @endif
                    <input type="file" id="photo_sertifikat" name="photo_sertifikat"
                        class="form-control @error('photo_sertifikat') is-invalid @enderror"
                        accept="image/jpeg,image/png,image/jpg,.pdf"
                        {{ auth()->user()->photo_sertifikat ? '' : 'required' }}>
                    <small class="form-text text-muted">Format: JPG, PNG, PDF | Maksimal 2MB</small>
                    @error('photo_sertifikat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="photo_ktmkhs" class="form-label">
                        Foto KTM/KHS
                        @if(!auth()->user()->photo_ktmkhs)
                            <span class="text-danger">*</span>
                        @else
                            <small class="text-muted">(Opsional - sudah ada file sebelumnya)</small>
                        @endif
                    </label>
                    @if(auth()->user()->photo_ktmkhs)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . auth()->user()->photo_ktmkhs) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat File Sebelumnya
                            </a>
                        </div>
                    @endif
                    <input type="file" id="photo_ktmkhs" name="photo_ktmkhs"
                        class="form-control @error('photo_ktmkhs') is-invalid @enderror"
                        accept="image/jpeg,image/png,image/jpg,.pdf"
                        {{ auth()->user()->photo_ktmkhs ? '' : 'required' }}>
                    <small class="form-text text-muted">Format: JPG, PNG, PDF | Maksimal 2MB</small>
                    @error('photo_ktmkhs')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="photo_administatif" class="form-label">
                        Foto Administratif
                        @if(!auth()->user()->photo_administatif)
                            <span class="text-danger">*</span>
                        @else
                            <small class="text-muted">(Opsional - sudah ada file sebelumnya)</small>
                        @endif
                    </label>
                    @if(auth()->user()->photo_administatif)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . auth()->user()->photo_administatif) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat File Sebelumnya
                            </a>
                        </div>
                    @endif
                    <input type="file" id="photo_administatif" name="photo_administatif"
                        class="form-control @error('photo_administatif') is-invalid @enderror"
                        accept="image/jpeg,image/png,image/jpg,.pdf"
                        {{ auth()->user()->photo_administatif ? '' : 'required' }}>
                    <small class="form-text text-muted">Format: JPG, PNG, PDF | Maksimal 2MB</small>
                    @error('photo_administatif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('asesi.daftar-ujikom.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
                    <button type="submit" class="btn btn-orange">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Include Payment Confirmation Modal --}}
    @include('components.modals.payment-confirmation-modal')

    {{-- Scripts --}}
    @push('scripts')
        <!-- DataTables sudah dimuat global dari layout -->
        <script>
            $(document).ready(function() {
                // Tampilkan modal jika ini pendaftaran kedua
                @if($isSecondRegistration)
                    $('#paymentConfirmationModal').modal('show');
                @endif
            });
        </script>
    @endpush
@endsection
