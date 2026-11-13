@extends('layouts.app')

@section('title', 'Perbaiki Laporan - CekDulu')

@section('content')
<div class="row justify-content-center mb-5">
    <div class="col-lg-8">

        <div class="mb-4">
            <h3 class="fw-bold mb-0">Perbaiki Laporan</h3>
            <p class="text-muted small mb-0">
                Nomor {{ $report->reported_value }}, dikirim {{ $report->created_at->format('d M Y, H:i') }} WIB
            </p>
        </div>

        <div class="alert alert-danger shadow-sm">
            <strong>Catatan dari Verifier</strong>
            <p class="mb-0">{{ $report->revision_note ?? 'Tidak ada catatan tambahan.' }}</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Formulir Perbaikan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.update', $report->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="reported_value" class="form-label fw-bold">Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="text" id="reported_value" name="reported_value"
                               class="form-control @error('reported_value') is-invalid @enderror"
                               value="{{ old('reported_value', $report->reported_value) }}"
                               placeholder="Contoh: 081234567890" required>
                        @error('reported_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-bold">Kategori Penipuan <span class="text-danger">*</span></label>
                        <select id="category_id" name="category_id"
                                class="form-select @error('category_id') is-invalid @enderror" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $report->category_id) == $category->id ? 'selected' : '' }}>
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
                        <textarea id="chronology" name="chronology" rows="6"
                                  class="form-control @error('chronology') is-invalid @enderror"
                                  required>{{ old('chronology', $report->chronology) }}</textarea>
                        @error('chronology')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="evidence_file" class="form-label fw-bold">Ganti Bukti Pendukung (opsional)</label>
                        <input type="file" id="evidence_file" name="evidence_file"
                               class="form-control @error('evidence_file') is-invalid @enderror"
                               accept=".jpg,.jpeg,.png,.pdf">
                        <div class="form-text">
                            Kosongkan jika bukti saat ini sudah sesuai.
                            <a href="{{ route('reports.evidence', $report->id) }}" target="_blank">Lihat bukti saat ini</a>.
                            Format JPG, PNG, atau PDF, maksimal 2 MB.
                        </div>
                        @error('evidence_file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('reports.show', $report->id) }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Kirim Perbaikan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection