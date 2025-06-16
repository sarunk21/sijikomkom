@extends('components.templates.master-layout')

@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
    <div class="container-fluid">
        <div class="row">

            {{-- LEFT CARD --}}
            <div class="col-md-4">
                <div class="border p-3 bg-white">
                    <div class="text-center mb-3">
                        @if ($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto" class="img-fluid"
                                style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                        @else
                            <i class="fas fa-user fa-5x text-dark"></i>
                        @endif
                    </div>
                    <table class="w-100">
                        <tr>
                            <td class="text-muted font-weight-bold" style="width: 30%;">Nama</td>
                            <td class="text-muted">: {{ $user->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">NIK</td>
                            <td class="text-muted">: {{ $user->nik }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Email</td>
                            <td class="text-muted">: {{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Telepon</td>
                            <td class="text-muted">: {{ $user->telephone }}</td>
                        </tr>
                    </table>
                </div>

            </div>

            {{-- RIGHT CARD --}}
            <div class="col-md-8">
                <div class="border p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5><strong>Informasi Profil</strong></h5>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Basic info --}}
                        <div class="form-group">
                            <label>Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}"
                                placeholder="Masukkan nama lengkap" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>NIK <span class="text-danger">*</span></label>
                            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                                value="{{ old('nik', $user->nik) }}"
                                placeholder="Masukkan NIK" maxlength="16" required>
                            @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror"
                                value="{{ old('telephone', $user->telephone) }}"
                                placeholder="Masukkan nomor telepon" maxlength="15" required>
                            @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}"
                                placeholder="Masukkan email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tempat / Tanggal / Gender --}}
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror"
                                    value="{{ old('tempat_lahir', $user->tempat_lahir) }}"
                                    placeholder="Masukkan tempat lahir" required>
                                @error('tempat_lahir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label>Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                    value="{{ old('tanggal_lahir', $user->tanggal_lahir) }}" required>
                                @error('tanggal_lahir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div class="form-group">
                            <label>Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                                placeholder="Masukkan alamat lengkap" required rows="3">{{ old('alamat', $user->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kebangsaan / Pekerjaan / Pendidikan --}}
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Kebangsaan <span class="text-danger">*</span></label>
                                <input type="text" name="kebangsaan" class="form-control @error('kebangsaan') is-invalid @enderror"
                                    value="{{ old('kebangsaan', $user->kebangsaan) }}"
                                    placeholder="Masukkan kebangsaan" required>
                                @error('kebangsaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label>Pekerjaan <span class="text-danger">*</span></label>
                                <input type="text" name="pekerjaan" class="form-control @error('pekerjaan') is-invalid @enderror"
                                    value="{{ old('pekerjaan', $user->pekerjaan) }}"
                                    placeholder="Masukkan pekerjaan" required>
                                @error('pekerjaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label>Pendidikan <span class="text-danger">*</span></label>
                                <input type="text" name="pendidikan" class="form-control @error('pendidikan') is-invalid @enderror"
                                    value="{{ old('pendidikan', $user->pendidikan) }}"
                                    placeholder="Masukkan pendidikan terakhir" required>
                                @error('pendidikan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Foto & TTD --}}
                        <div class="form-row text-center mt-4">
                            <div class="col">
                                <label>Foto</label>
                                @if ($user->photo)
                                    <div><img src="{{ asset('storage/' . $user->photo) }}" alt="Foto"
                                            class="img-fluid"></div>
                                @else
                                    <div><i class="fas fa-user fa-5x text-dark"></i></div>
                                @endif
                                <input type="file" name="photo" class="form-control-file mt-2 @error('photo') is-invalid @enderror">
                                <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB</small>
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label>Tanda Tangan</label>
                                @if ($user->tanda_tangan)
                                    <div><img src="{{ asset('storage/' . $user->tanda_tangan) }}" alt="Tanda Tangan"
                                            class="img-fluid"></div>
                                @else
                                    <div><i class="fas fa-user fa-5x text-dark"></i></div>
                                @endif
                                <input type="file" name="tanda_tangan" class="form-control-file mt-2 @error('tanda_tangan') is-invalid @enderror">
                                <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB</small>
                                @error('tanda_tangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="text-right mt-4">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
