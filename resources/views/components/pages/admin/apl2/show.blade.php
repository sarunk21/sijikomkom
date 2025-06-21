@extends('components.templates.master-layout')

@section('title', 'APL02 - List Pertanyaan')
@section('page-title', 'List Pertanyaan APL02')

@section('content')

    <a href="{{ route('admin.apl-2.index') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
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
            <a href="{{ route('admin.apl-2.create.question', $skema_id) }}" class="btn btn-dark mb-3"><i
                    class="fas fa-plus mr-2"></i> Tambah Pertanyaan</a>

            <div class="table-responsive">
                <table id="apl2Table" class="table table-striped table-hover align-middle w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Pertanyaan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questions as $item)
                            <tr>
                                <td class="align-middle">
                                    {{ $item->question_text }}
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center align-items-center" style="gap: 0.5rem;">
                                        <a href="{{ route('admin.apl-2.edit', $item->id) }}"
                                            class="btn btn-light btn-icon btn-sm border shadow-sm" title="Edit">
                                            <i class="fas fa-pen text-primary"></i>
                                        </a>

                                        <form action="{{ route('admin.apl-2.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light btn-icon btn-sm border shadow-sm" title="Hapus">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if ($questions->isEmpty())
                            <tr>
                                <td colspan="2" class="text-center">Tidak ada pertanyaan</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
