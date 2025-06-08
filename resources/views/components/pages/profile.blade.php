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
                        <a href="{{ route('admin.profile.update', $user->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-plus"></i> Simpan Perubahan
                        </a>
                    </div>

                    <form>
                        {{-- Basic info --}}
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" class="form-control" value="{{ $user->name }}" required>
                        </div>
                        <div class="form-group">
                            <label>NIK</label>
                            <input type="text" class="form-control" value="{{ $user->nik }}" required>
                        </div>
                        <div class="form-group">
                            <label>Telepon</label>
                            <input type="text" class="form-control" value="{{ $user->telephone }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" required>
                        </div>

                        {{-- Tempat / Tanggal / Gender --}}
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Tempat Lahir</label>
                                <input type="text" class="form-control" value="{{ $user->tempat_lahir }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Tanggal Lahir</label>
                                <input type="text" class="form-control" value="{{ $user->tanggal_lahir }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Jenis Kelamin</label>
                                <input type="text" class="form-control" value="{{ $user->jenis_kelamin }}" required>
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div class="form-group">
                            <label>Alamat</label>
                            <input type="text" class="form-control" value="{{ $user->alamat }}" required>
                        </div>

                        {{-- Kebangsaan / Pekerjaan / Pendidikan --}}
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Kebangsaan</label>
                                <input type="text" class="form-control" value="{{ $user->kebangsaan }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Pekerjaan</label>
                                <input type="text" class="form-control" value="{{ $user->pekerjaan }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Pendidikan</label>
                                <input type="text" class="form-control" value="{{ $user->pendidikan }}" required>
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
                            </div>
                            <div class="col">
                                <label>Tanda Tangan</label>
                                @if ($user->tanda_tangan)
                                    <div><img src="{{ asset('storage/' . $user->tanda_tangan) }}" alt="Tanda Tangan"
                                            class="img-fluid"></div>
                                @else
                                    <div><i class="fas fa-user fa-5x text-dark"></i></div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
