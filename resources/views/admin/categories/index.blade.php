@extends('layouts.app')

@section('title', 'Kategori Penipuan - CekDulu')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1">Kategori Penipuan</h3>
        <p class="text-muted small mb-0">Kategori aktif tersedia saat pelapor membuat laporan baru.</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Tambah Kategori</a>
</div>

<div class="card shadow-sm mb-5">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-nowrap">
                    <th scope="col" class="ps-3 ps-md-4">Nama Kategori</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-end">Jumlah Laporan</th>
                    <th scope="col" class="text-end pe-3 pe-md-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <th scope="row" class="ps-3 ps-md-4 fw-semibold text-break">{{ $category->name }}</th>
                        <td>
                            @if($category->is_active)
                                <span class="badge text-bg-success">Aktif</span>
                            @else
                                <span class="badge text-bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end">{{ $category->reports_count }}</td>
                        <td class="pe-3 pe-md-4">
                            <div class="d-flex flex-wrap justify-content-end gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.categories.status', $category) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $category->is_active ? '0' : '1' }}">
                                    <button type="submit" class="btn btn-sm {{ $category->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                        {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">Belum ada kategori penipuan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
