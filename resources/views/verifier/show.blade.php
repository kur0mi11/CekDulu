@extends('layouts.app')

@section('title', 'Periksa Detail Laporan - Verifier')

@section('content')
<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Pemeriksaan Laporan</h2>
        <a href="{{ route('verifier.index') }}" class="btn btn-secondary">Kembali ke Antrean</a>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Pengaduan</h5>
                </div>
                <div class="card-body">
                    <p><strong>Pelapor:</strong> {{ $report->user->name ?? 'Anonim' }}</p>
                    <p><strong>Tanggal Masuk:</strong> {{ $report->created_at->format('d M Y, H:i') }}</p>
                    <p><strong>Nomor Telepon Dilaporkan:</strong> <span class="text-danger fs-5 fw-bold">{{ $report->reported_value }}</span></p>
                    <p><strong>Kategori:</strong> {{ $report->category->name ?? '-' }}</p>
                    <hr>
                    <p class="fw-bold mb-1">Kronologi Kejadian:</p>
                    <p class="p-3 bg-light rounded border">{{ $report->chronology }}</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Keputusan Verifikasi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('verifier.reports.update', $report->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">Ubah Status Laporan <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="" selected disabled>-- Pilih Keputusan --</option>
                                <option value="pending_admin_review">Setujui (Teruskan ke Peninjauan Admin)</option>
                                <option value="need_revision">Minta Revisi dari Pelapor</option>
                                <option value="rejected">Tolak Laporan</option>
                            </select>
                        </div>

                        <div class="mb-3" id="note-container">
                            <label for="revision_note" class="form-label fw-bold">Catatan Revisi (Wajib diisi jika minta revisi)</label>
                            <textarea class="form-control" id="revision_note" name="revision_note" rows="3" placeholder="Jelaskan bagian mana yang perlu diperbaiki oleh pelapor..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Simpan Keputusan Verifikasi</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Bukti Lampiran (Privat)</h5>
                </div>
                <div class="card-body text-center">
                    @if($report->evidence_file)
                        @php
                            $extension = pathinfo($report->evidence_file, PATHINFO_EXTENSION);
                        @endphp

                        @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                            <img src="{{ route('verifier.reports.evidence', $report->id) }}" alt="Bukti Laporan" class="img-fluid rounded border mb-3" style="max-height: 400px;">
                        @else
                            <p class="text-muted">File berupa dokumen PDF.</p>
                        @endif

                        <a href="{{ route('verifier.reports.evidence', $report->id) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">Buka File Ukuran Penuh</a>
                    @else
                        <p class="text-muted mb-0">Tidak ada file bukti yang dilampirkan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection