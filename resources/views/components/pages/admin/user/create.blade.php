@extends('components.templates.master-layout')

@section('title', 'User - Create')
@section('page-title', 'Create User')

@section('content')

    <a href="{{ route('admin.user.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali</span>
    </a>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.user.store') }}" method="POST">
                @csrf
                @method('POST')

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" id="nama" name="name" class="form-control"
                        placeholder="Isi nama User di sini..." required>
                </div>

                <div class="mb-3">
                    <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                    <input type="text" id="nik" name="nik" class="form-control" maxlength="16"
                        placeholder="Isi NIK di sini..." required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="text" id="email" name="email" class="form-control"
                        placeholder="Isi Email di sini..." required>
                </div>

                <div class="mb-3">
                    <label for="telepon" class="form-label">Telepon <span class="text-danger">*</span></label>
                    <input type="text" id="telepon" name="telephone" class="form-control" maxlength="15"
                        placeholder="Isi Telepon di sini..." required>
                </div>

                <div class="mb-3">
                    <label for="user_type" class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="user_type" id="user_type" class="form-control" required>
                        <option value="" disabled selected>Pilih Role di sini...</option>
                        <option value="Admin">Admin</option>
                        <option value="User">User</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                    <textarea name="alamat" id="alamat" class="form-control" required></textarea>
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
