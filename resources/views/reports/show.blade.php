@extends('layouts.app')

@section('title', 'Detail Laporan')

@section('content')
<div class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Detail Laporan</h3>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Laporan</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Tanggal Dikirim:</strong> {{ $report->created_at->format('d M Y, H:i') }} WIB</p>
                    <p class="mb-2"><strong>Nomor Telepon:</strong> <span class="fw-bold">{{ $report->reported_value }}</span></p>
                    <p class="mb-2"><strong>Kategori:</strong> {{ $report->category->name ?? '-' }}</p>
                    <p class="mb-0"><strong>Status:</strong> @include('partials.status-badge', ['report' => $report])</p>

                    @if($report->status === 'need_revision')
                        <div class="alert alert-danger mt-3 mb-0">
                            <strong>Catatan Revisi dari Verifier:</strong>
                            <p class="mb-3">{{ $report->revision_note ?? 'Tidak ada catatan tambahan.' }}</p>
                            <a href="{{ route('reports.edit', $report->id) }}" class="btn btn-danger btn-sm">Perbaiki Laporan</a>
                        </div>
                    @endif

                    @if($report->status === 'rejected' && $report->revision_note)
                        <div class="alert alert-secondary mt-3 mb-0">
                            <strong>Alasan Penolakan:</strong>
                            <p class="mb-0">{{ $report->revision_note }}</p>
                        </div>
                    @endif

                    @if($report->status === 'pending_admin_review')
                        <div class="alert alert-info mt-3 mb-0">
                            Laporan Anda sudah lolos pemeriksaan awal dan menunggu keputusan akhir Admin.
                        </div>
                    @endif

                    @if($report->status === 'verified')
                        <div class="alert alert-success mt-3 mb-0">
                            Laporan Anda sudah terverifikasi dan dapat ditemukan melalui pencarian publik.
                            Identitas Anda tidak ditampilkan kepada publik.
                        </div>
                    @endif

                    <hr>
                    <p class="fw-bold mb-1">Kronologi Kejadian</p>
                    <p class="p-3 bg-light rounded border mb-0">{{ $report->chronology }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Bukti Lampiran</h5>
                </div>
                <div class="card-body text-center">
                    @if($report->evidence_file)
                        @php
                            $ext = strtolower(pathinfo($report->evidence_file, PATHINFO_EXTENSION));
                        @endphp

                        @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                            <img src="{{ route('reports.evidence', $report->id) }}" alt="Bukti laporan" class="img-fluid rounded border mb-3">
                        @else
                            <p class="text-muted small">Bukti berupa file PDF.</p>
                        @endif

                        <a href="{{ route('reports.evidence', $report->id) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">Buka File Bukti</a>
                        <p class="text-muted small mt-2 mb-0">File bukti bersifat privat dan hanya dapat dibuka oleh Anda dan petugas berwenang.</p>
                    @else
                        <p class="text-muted mb-0">Tidak ada file bukti.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection