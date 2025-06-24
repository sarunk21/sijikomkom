@extends('components.templates.master-layout')

@section('title', 'User - Edit')
@section('page-title', 'Edit User')

@section('content')

    <a href="{{ route('admin.user.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali</span>
    </a>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.user.update', $user->id) }}" method="POST">
                @csrf
                @method('POST')

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" id="nama" name="name" class="form-control"
                        placeholder="Isi nama User di sini..." value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                    <input type="text" id="nik" name="nik" class="form-control" maxlength="16"
                        placeholder="Isi NIK di sini..." value="{{ old('nik', $user->nik) }}" required>
                    @error('nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="text" id="email" name="email" class="form-control"
                        placeholder="Isi Email di sini..." value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="telepon" class="form-label">Telepon <span class="text-danger">*</span></label>
                    <input type="text" id="telepon" name="telephone" class="form-control" maxlength="15"
                        placeholder="Isi Telepon di sini..." value="{{ old('telephone', $user->telephone) }}" required>
                    @error('telephone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="user_type" class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="user_type" id="user_type" class="form-control" required>
                        <option value="" disabled>Pilih Role di sini...</option>
                        <option value="asesi" {{ old('user_type', $user->user_type) == 'asesi' ? 'selected' : '' }}>Asesi</option>
                        <option value="asesor" {{ old('user_type', $user->user_type) == 'asesor' ? 'selected' : '' }}>Asesor</option>
                        <option value="kaprodi" {{ old('user_type', $user->user_type) == 'kaprodi' ? 'selected' : '' }}>Kaprodi</option>
                        <option value="pimpinan" {{ old('user_type', $user->user_type) == 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                        <option value="admin" {{ old('user_type', $user->user_type) == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('user_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                    <textarea name="alamat" id="alamat" class="form-control" required>{{ old('alamat', $user->alamat) }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.user.index') }}" class="btn btn-outline-danger mr-2">Batalkan</a>
                    <button type="submit" class="btn btn-orange">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tambahan styling warna btn-orange jika belum ada --}}
    <style>
        .text-orange {
            color: #f25c05;
        }

        .btn-orange {
            background-color: #f25c05;
            color: white;
        }

        .btn-orange:hover {
            background-color: #d94f04;
            color: white;
        }
    </style>

@endsection
