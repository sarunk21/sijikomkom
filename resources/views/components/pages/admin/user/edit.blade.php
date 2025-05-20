@extends('components.templates.master-layout')

@section('title', 'User - Edit')
@section('page-title', 'Edit User')

@section('content')

    <a href="{{ route('admin.user.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
        <i class="fas fa-arrow-left text-orange mr-2"></i>
        <span class="text-orange">Kembali</span>
    </a>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" id="nama" name="nama" class="form-control"
                        placeholder="Isi nama User di sini..." value="Admin" disabled>
                </div>

                <div class="mb-3">
                    <label for="nik" class="form-label">NIK</label>
                    <input type="text" id="nik" name="nik" class="form-control"
                        placeholder="Isi NIK di sini..." value="1234567890" disabled>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" id="email" name="email" class="form-control"
                        placeholder="Isi Email di sini..." value="admin@gmail.com" disabled>
                </div>

                <div class="mb-3">
                    <label for="telepon" class="form-label">Telepon</label>
                    <input type="text" id="telepon" name="telepon" class="form-control"
                        placeholder="Isi Telepon di sini..." value="081234567890" disabled>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="" disabled>Pilih Role di sini...</option>
                        <option value="Admin" selected>Admin</option>
                        <option value="User">User</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea name="alamat" id="alamat" class="form-control" disabled>Jl. Raya No. 123, Jakarta</textarea>
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
