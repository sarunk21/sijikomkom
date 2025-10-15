@extends('components.templates.master-layout')

@section('title', 'Custom Data - APL 1')
@section('page-title', 'Input Data Custom untuk APL 1')

@section('content')

    <a href="{{ route('asesi.sertifikasi.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali ke Sertifikasi</span>
    </a>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Input Data Custom untuk APL 1
                        </h6>
                        <small class="text-muted">
                            Skema: <strong>{{ $pendaftaran->skema->nama }}</strong>
                        </small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('asesi.custom-data.store', $pendaftaran->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Custom Variables -->
                            @if(count($customVariables) > 0)
                            <div class="mb-4">
                                <h6 class="text-primary">Custom Variables</h6>
                                <p class="small text-muted">Isi data custom yang diperlukan untuk template APL 1 Anda</p>

                                @foreach($customVariables as $variable)
                                <div class="mb-3">
                                    <label for="custom_{{ $variable }}" class="form-label">
                                        {{ ucwords(str_replace(['_', '.'], ' ', $variable)) }}
                                    </label>
                                    <input type="text"
                                        class="form-control @error('custom_variables.' . $variable) is-invalid @enderror"
                                        id="custom_{{ $variable }}"
                                        name="custom_variables[{{ $variable }}]"
                                        value="{{ old('custom_variables.' . $variable, $pendaftaran->custom_variables[$variable] ?? '') }}"
                                        placeholder="Masukkan {{ strtolower(str_replace(['_', '.'], ' ', $variable)) }}">
                                    @error('custom_variables.' . $variable)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Format di template: <code>${ {{ $variable }} }</code>
                                    </small>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <!-- TTD Digital Asesi -->
                            <div class="mb-4">
                                <h6 class="text-success">Tanda Tangan Digital</h6>
                                <p class="small text-muted">Upload tanda tangan digital Anda (opsional)</p>

                                @if($pendaftaran->ttd_asesi_path)
                                <div class="alert alert-info">
                                    <i class="fas fa-image"></i>
                                    TTD saat ini:
                                    <a href="{{ asset('storage/' . $pendaftaran->ttd_asesi_path) }}" target="_blank">
                                        Lihat TTD
                                    </a>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <label for="ttd_asesi" class="form-label">Upload TTD Digital Baru</label>
                                    <input type="file"
                                        class="form-control @error('ttd_asesi') is-invalid @enderror"
                                        id="ttd_asesi"
                                        name="ttd_asesi"
                                        accept="image/*">
                                    <small class="form-text text-muted">
                                        Format: PNG, JPG, JPEG. Maksimal 2MB.
                                        TTD ini akan menggantikan TTD template di dokumen APL 1.
                                    </small>
                                    @error('ttd_asesi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Info Template -->
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Informasi</h6>
                                <ul class="mb-0">
                                    <li>Data custom akan digunakan untuk menggantikan variable di template APL 1</li>
                                    <li>TTD digital Anda akan menggantikan TTD template (jika ada)</li>
                                    <li>Anda bisa mengubah data ini kapan saja sebelum generate APL 1</li>
                                    <li>Jika ada custom variable yang tidak diisi, akan menggunakan data default dari template</li>
                                </ul>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('asesi.sertifikasi.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Data Custom
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Template Info</h6>
                    </div>
                    <div class="card-body">
                        <h6>Template: {{ $template->nama_template }}</h6>
                        <p class="small text-muted">{{ $template->deskripsi }}</p>

                        @if(count($customVariables) > 0)
                        <h6 class="mt-3">Custom Variables:</h6>
                        <ul class="list-unstyled">
                            @foreach($customVariables as $variable)
                            <li class="small">
                                <code>${ {{ $variable }} }</code>
                                @if($pendaftaran->custom_variables && isset($pendaftaran->custom_variables[$variable]))
                                    <span class="badge badge-success">Sudah diisi</span>
                                @else
                                    <span class="badge badge-warning">Belum diisi</span>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <div class="alert alert-info">
                            <small>Tidak ada custom variables yang perlu diisi untuk template ini.</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
