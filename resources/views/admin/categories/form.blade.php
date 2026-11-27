@extends('layouts.app')

@php
    $editing = $category->exists;
    $title = $editing ? 'Edit Kategori' : 'Tambah Kategori';
@endphp

@section('title', $title . ' - CekDulu')

@section('content')
<div class="row justify-content-center mb-5">
    <div class="col-lg-7">
        <h3 class="fw-bold mb-3">{{ $title }}</h3>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST">
                    @csrf
                    @if($editing)
                        @method('PATCH')
                    @endif

                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}"
                               class="form-control @error('name') is-invalid @enderror" maxlength="255" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
