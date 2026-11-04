@extends('layouts.app')

@section('title', 'Detail Laporan Saya - CekDulu')

@section('content')
<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Laporan Saya</h2>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Pengaduan</h5>
                </div>
                <div class="card-body">
                    <p><strong>Tanggal Pembuatan:</strong> {{ $report->created_at->format('d M Y, H:i') }}</p>
                    <p><strong>Nomor Telepon Dilaporkan:</strong> <span class="text-danger fw-bold">{{ $report->reported_value }}</span></p>
                    <p><strong>Kategori:</strong> {{ $report->category->name ?? '-' }}</p>
                    <p><strong>Status Saat Ini:</strong> 
                        @if($report->status === 'pending')
                            <span class="badge bg-warning text-dark">Menunggu Pemeriksaan</span>
                        @elseif($report->status === 'need_revision')
                            <span class="badge bg-danger">Perlu Revisi</span>
                        @elseif($report->status === 'pending_admin_review')
                            <span class="badge bg-info text-dark">Meninjauan Admin</span>
                        @elseif($report->status === 'verified')
                            <span class="badge bg-success">Terverifikasi (Publik)</span>
                        @elseif($report->status === 'rejected')
                            <span class="badge bg-secondary">Ditolak</span>
                        @endif
                    </p>

                    @if($report->status === 'need_revision' && $report->revision_note)
                        <div class="alert alert-danger mt-3">
                            <strong>Catatan Revisi dari Verifikator:</strong>
                            <p class="mb-0">{{ $report->revision_note }}</p>
                        </div>
                    @endif

                    <hr>
                    <p class="fw-bold mb-1">Kronologi Kejadian:</p>
                    <p class="p-3 bg-light rounded border">{{ $report->chronology }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Bukti Lampiran</h5>
                </div>
                <div class="card-body text-center">
                    @if($report->evidence_file)
                        <p class="text-muted small">File bukti tersimpan secara aman di sistem.</p>
                        <a href="{{ asset('storage/' . $report->evidence_file) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">Lihat File Bukti</a>
                    @else
                        <p class="text-muted mb-0">Tidak ada file bukti.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection