@extends('components.templates.master-layout')

@section('title', 'Informasi Pembayaran - Edit')
@section('page-title', 'Edit Informasi Pembayaran')

@section('content')

    {{-- Back Button --}}
    <div class="mb-4">
        <a href="{{ route('asesi.daftar-ujikom.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    {{-- Error Alert --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- Left Column - Payment Info --}}
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-gradient-info text-white py-3">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-university mr-2"></i>Informasi Rekening Pembayaran
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-light border-info mb-4">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-info-circle text-info mr-3 mt-1" style="font-size: 1.5rem;"></i>
                            <div>
                                <p class="mb-2 font-weight-500">Silakan transfer biaya ujian ke rekening berikut:</p>
                            </div>
                        </div>
                    </div>

                    <div class="payment-info-box p-4 mb-3 rounded border border-info bg-light">
                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">Nama Bank</label>
                            <h5 class="mb-0 font-weight-bold text-primary">
                                <i class="fas fa-building mr-2"></i>Bank BCA
                            </h5>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">Nomor Rekening</label>
                            <h4 class="mb-0 font-weight-bold text-dark">1234567890</h4>
                        </div>

                        <div>
                            <label class="text-muted small d-block mb-1">Atas Nama</label>
                            <h6 class="mb-0 font-weight-bold text-dark">UPN "Veteran" Jakarta</h6>
                        </div>
                    </div>

                    <div class="alert alert-warning border-warning mb-0">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
                            <small>
                                <strong>Penting!</strong> Pastikan transfer sesuai dengan nominal yang tertera dan simpan bukti transfer untuk diupload.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Upload Form --}}
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-upload text-warning mr-2"></i>
                        <h6 class="mb-0 font-weight-bold">Upload Bukti Pembayaran</h6>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('asesi.informasi-pembayaran.update', $pembayaran->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Jadwal Ujian (Readonly) --}}
                        <div class="form-group">
                            <label class="font-weight-500">
                                <i class="fas fa-calendar-alt text-warning mr-1"></i>
                                Jadwal Ujian <span class="text-danger">*</span>
                            </label>
                            <select name="jadwal_id" id="jadwal_id"
                                class="form-control bg-light @error('jadwal_id') is-invalid @enderror" disabled>
                                <option value="" disabled>Pilih Jadwal Ujian</option>
                                @foreach ($jadwal as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('jadwal_id', $pembayaran->jadwal_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->skema->nama }} - {{ $item->tanggal_ujian }} - {{ $item->tuk->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-lock mr-1"></i>Jadwal ujian tidak dapat diubah
                            </small>
                            @error('jadwal_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- Upload Bukti Pembayaran --}}
                        <div class="form-group">
                            <label class="font-weight-500">
                                <i class="fas fa-file-upload text-warning mr-1"></i>
                                Bukti Pembayaran <span class="text-danger">*</span>
                            </label>

                            {{-- File Preview Area --}}
                            @if($pembayaran->bukti_pembayaran)
                            <div class="mb-3 p-3 bg-light rounded border">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-image text-success fa-2x mr-3"></i>
                                        <div>
                                            <small class="text-muted d-block">File Saat Ini:</small>
                                            <span class="font-weight-500">{{ basename($pembayaran->bukti_pembayaran) }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}"
                                       target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye mr-1"></i>Lihat
                                    </a>
                                </div>
                            </div>
                            @endif

                            <div class="custom-file">
                                <input type="file"
                                    class="custom-file-input @error('bukti_pembayaran') is-invalid @enderror"
                                    id="bukti_pembayaran"
                                    name="bukti_pembayaran"
                                    accept="image/jpeg,image/png,image/jpg,.pdf"
                                    required>
                                <label class="custom-file-label" for="bukti_pembayaran">
                                    {{ $pembayaran->bukti_pembayaran ? 'Upload file baru untuk mengganti' : 'Pilih file...' }}
                                </label>
                                @error('bukti_pembayaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Format: JPG, PNG, PDF | Maksimal 2MB
                            </small>
                        </div>

                        <hr class="my-4">

                        {{-- Action Buttons --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <span class="text-danger">*</span> Wajib diisi
                            </small>
                            <div>
                                <a href="{{ route('asesi.daftar-ujikom.index') }}"
                                   class="btn btn-outline-danger mr-2">
                                    <i class="fas fa-times mr-1"></i>Batalkan
                                </a>
                                <button type="submit" class="btn btn-warning px-4">
                                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    .bg-gradient-info {
        background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
    }

    .payment-info-box {
        transition: all 0.3s ease;
    }

    .payment-info-box:hover {
        box-shadow: 0 5px 15px rgba(54, 185, 204, 0.2);
        transform: translateY(-2px);
    }

    .custom-file-label::after {
        content: "Browse";
    }

    .font-weight-500 {
        font-weight: 500;
    }
</style>
@endpush

@push('scripts')
<script>
    // Update custom file input label
    document.querySelector('#bukti_pembayaran')?.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Pilih file...';
        const label = e.target.nextElementSibling;
        label.textContent = fileName;
    });
</script>
@endpush
