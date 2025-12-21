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
        <div class="card border-left-info shadow mb-4">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            <i class="fas fa-info-circle"></i> Informasi Pendaftaran
                        </div>
                        <div class="h6 mb-0 text-gray-800">
                            Anda sudah pernah mendaftar sebelumnya. Pastikan untuk melengkapi formulir APL setelah mendaftar.
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-info-circle fa-2x text-info"></i>
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
                    <a href="{{ route('dashboard.asesi') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-2"></i>Daftar Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="alert alert-info mt-3">
        <h5><i class="fas fa-info-circle mr-2"></i>Langkah Selanjutnya</h5>
        <ol class="mb-0">
            <li>Setelah mendaftar, Anda akan diarahkan untuk <strong>melengkapi formulir APL</strong></li>
            <li>Pendaftaran akan diverifikasi oleh <strong>Kaprodi → Admin → Asesor</strong></li>
            <li>Setelah kelayakan disetujui, Anda akan mendapat notifikasi untuk <strong>melakukan pembayaran</strong></li>
            <li>Upload bukti pembayaran dan tunggu konfirmasi untuk mulai ujikom</li>
        </ol>
    </div>
@endsection
