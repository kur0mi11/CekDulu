@extends('layouts.app')

@section('title', 'Buat Laporan Baru - CekDulu')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Laporkan Indikasi Penipuan Nomor Telepon</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="reported_value" class="form-label fw-bold">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('reported_value') is-invalid @enderror" id="reported_value" name="reported_value" value="{{ old('reported_value') }}" placeholder="Contoh: 081234567890" required>
                            @error('reported_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-bold">Kategori Penipuan <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="" selected disabled>-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="chronology" class="form-label fw-bold">Kronologi Kejadian <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('chronology') is-invalid @enderror" id="chronology" name="chronology" rows="4" placeholder="Ceritakan secara singkat bagaimana penipuan terjadi..." required>{{ old('chronology') }}</textarea>
                            @error('chronology')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="evidence_file" class="form-label fw-bold">Bukti Pendukung (Tangkapan Layar/PDF) <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('evidence_file') is-invalid @enderror" id="evidence_file" name="evidence_file" accept=".jpg,.jpeg,.png,.pdf" required>
                            <div class="form-text">Maksimal ukuran file: 2MB. Disimpan secara privat.</div>
                            @error('evidence_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Kirim Laporan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection