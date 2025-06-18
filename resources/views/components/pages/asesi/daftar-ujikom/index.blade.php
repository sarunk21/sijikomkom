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
                    <label for="photo_ktp" class="form-label">Foto KTP <span class="text-danger">*</span></label>
                    <input type="file" id="photo_ktp" name="photo_ktp"
                        class="form-control @error('photo_ktp') is-invalid @enderror" required>
                    @error('photo_ktp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="photo_sertifikat" class="form-label">Foto Sertifikat <span
                            class="text-danger">*</span></label>
                    <input type="file" id="photo_sertifikat" name="photo_sertifikat"
                        class="form-control @error('photo_sertifikat') is-invalid @enderror" required>
                    @error('photo_sertifikat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="photo_ktmkhs" class="form-label">Foto KTM/KHS <span class="text-danger">*</span></label>
                    <input type="file" id="photo_ktmkhs" name="photo_ktmkhs"
                        class="form-control @error('photo_ktmkhs') is-invalid @enderror" required>
                    @error('photo_ktmkhs')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="photo_administatif" class="form-label">Foto Administratif <span
                            class="text-danger">*</span></label>
                    <input type="file" id="photo_administatif" name="photo_administatif"
                        class="form-control @error('photo_administatif') is-invalid @enderror" required>
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

    {{-- Scripts --}}
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    @endpush
@endsection
