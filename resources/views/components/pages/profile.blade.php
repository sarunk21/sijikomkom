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
                        <i class="fas fa-user fa-5x text-dark"></i>
                    </div>
                    <table class="w-100">
                        <tr>
                            <td class="text-muted font-weight-bold" style="width: 30%;">Nama</td>
                            <td class="text-muted">: Asesi 1</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">NIK</td>
                            <td class="text-muted">: 321010220033</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Email</td>
                            <td class="text-muted">: asesi1@mail.com</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Telepon</td>
                            <td class="text-muted">: 09482133</td>
                        </tr>
                    </table>
                </div>

            </div>

            {{-- RIGHT CARD --}}
            <div class="col-md-8">
                <div class="border p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5><strong>Informasi Profil</strong></h5>
                        <a href="#" class="btn btn-sm btn-warning">
                            <i class="fas fa-plus"></i> Edit Profile
                        </a>
                    </div>

                    <form>
                        {{-- Basic info --}}
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" class="form-control" value="Asesi 1" disabled>
                        </div>
                        <div class="form-group">
                            <label>NIK</label>
                            <input type="text" class="form-control" value="321010220033" disabled>
                        </div>
                        <div class="form-group">
                            <label>Telepon</label>
                            <input type="text" class="form-control" value="09482133" disabled>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" value="asesi1@mail.com" disabled>
                        </div>

                        {{-- Tempat / Tanggal / Gender --}}
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Tempat Lahir</label>
                                <input type="text" class="form-control" value="jakarta" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Tanggal Lahir</label>
                                <input type="text" class="form-control" value="10/11/2003" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Jenis Kelamin</label>
                                <input type="text" class="form-control" value="Laki Laki" disabled>
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div class="form-group">
                            <label>Alamat</label>
                            <input type="text" class="form-control" value="Jl. Fatmawati" disabled>
                        </div>

                        {{-- Kebangsaan / Pekerjaan / Pendidikan --}}
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Kebangsaan</label>
                                <input type="text" class="form-control" value="Indonesia" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Pekerjaan</label>
                                <input type="text" class="form-control" value="Pelajar/Mahasiswa" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Pendidikan</label>
                                <input type="text" class="form-control" value="S1" disabled>
                            </div>
                        </div>

                        {{-- Foto & TTD --}}
                        <div class="form-row text-center mt-4">
                            <div class="col">
                                <label>Foto</label>
                                <div><i class="fas fa-user fa-4x text-dark"></i></div>
                            </div>
                            <div class="col">
                                <label>Tanda Tangan</label>
                                <div><i class="fas fa-user fa-4x text-dark"></i></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
