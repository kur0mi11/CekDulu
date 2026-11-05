@extends('layouts.app')

@section('title', 'Antrean & Riwayat Pemeriksaan')

@section('content')
<div class="container mt-2">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">
                <i class="bi bi-inboxes text-primary me-2"></i>Pemeriksaan Laporan
            </h3>
            <p class="text-muted small mb-0 mt-1">Kelola antrean dan tinjau riwayat verifikasi Anda.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Card Utama -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
        
        <!-- Area Pencarian & Navigasi Tab -->
        <div class="card-header bg-white border-bottom pt-4 pb-0 px-4">
            <div class="row justify-content-between align-items-center mb-4">
                <div class="col-md-6 col-lg-5">
                    <form action="{{ route('verifier.index') }}" method="GET" class="d-flex align-items-center bg-light p-1 rounded-pill border">
                        <i class="bi bi-search text-muted ms-3 me-2"></i>
                        <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none" placeholder="Cari nama pelapor atau nomor/rekening..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Cari</button>
                    </form>
                </div>
                
                @if(request('search'))
                <div class="col-md-auto mt-3 mt-md-0 text-md-end">
                    <a href="{{ route('verifier.index') }}" class="btn btn-light rounded-pill text-danger btn-sm px-3">
                        <i class="bi bi-x-circle me-1"></i>Hapus Filter
                    </a>
                </div>
                @endif
            </div>

            <!-- Tab Buttons -->
            <ul class="nav nav-tabs border-0" id="laporanTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold text-dark border-0 pb-3" id="antrean-tab" data-bs-toggle="tab" data-bs-target="#antrean-pane" type="button" role="tab" style="background: transparent;">
                        Antrean Saat Ini <span class="badge bg-primary ms-1">{{ $antrean->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-muted border-0 pb-3" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat-pane" type="button" role="tab" style="background: transparent;">
                        Riwayat Selesai <span class="badge bg-secondary ms-1">{{ $riwayat->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="laporanTabContent">
                
                <div class="tab-pane fade show active" id="antrean-pane" role="tabpanel" tabindex="0">
                    @if($antrean->isEmpty())
                        <div class="text-center py-5">
                            <div class="display-1 text-muted opacity-25 mb-3">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <h5 class="fw-bold text-muted">Antrean Kosong</h5>
                            <p class="text-muted">Tidak ada laporan yang menunggu diperiksa.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="ps-4 py-3 border-0">Waktu Masuk</th>
                                        <th class="py-3 border-0">Pelapor</th>
                                        <th class="py-3 border-0">Target Laporan</th>
                                        <th class="py-3 border-0">Status</th>
                                        <th class="pe-4 py-3 border-0 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    @foreach($antrean as $laporan)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-semibold text-dark">{{ $laporan->created_at->format('d M Y') }}</div>
                                                <div class="text-muted small">{{ $laporan->created_at->format('H:i') }} WIB</div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                                        <i class="bi bi-person"></i>
                                                    </div>
                                                    <span class="fw-medium text-dark">{{ $laporan->user->name ?? 'Anonim' }}</span>
                                                </div>
                                            </td>
                                            <td><span class="fw-bold fs-6 text-dark" style="letter-spacing: 1px;">{{ $laporan->reported_value }}</span></td>
                                            <td>
                                                @if($laporan->status === 'pending')
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-50 rounded-pill px-3 py-2">
                                                        <i class="bi bi-hourglass-split me-1"></i> Menunggu
                                                    </span>
                                                @elseif($laporan->status === 'need_revision')
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-50 rounded-pill px-3 py-2">
                                                        <i class="bi bi-pencil-square me-1"></i> Revisi User
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="pe-4 text-center">
                                                <a href="{{ route('verifier.reports.show', $laporan->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm d-inline-flex align-items-center">
                                                    Periksa <i class="bi bi-arrow-right-short fs-5 ms-1"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="tab-pane fade" id="riwayat-pane" role="tabpanel" tabindex="0">
                    @if($riwayat->isEmpty())
                        <div class="text-center py-5">
                            <div class="display-1 text-muted opacity-25 mb-3">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <h5 class="fw-bold text-muted">Belum Ada Riwayat</h5>
                            <p class="text-muted">Anda belum menyelesaikan verifikasi laporan apa pun.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="ps-4 py-3 border-0">Selesai Dicek</th>
                                        <th class="py-3 border-0">Pelapor</th>
                                        <th class="py-3 border-0">Target Laporan</th>
                                        <th class="py-3 border-0">Keputusan Akhir</th>
                                        <th class="pe-4 py-3 border-0 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    @foreach($riwayat as $laporan)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-semibold text-dark">{{ $laporan->updated_at->format('d M Y') }}</div>
                                                <div class="text-muted small">{{ $laporan->updated_at->format('H:i') }} WIB</div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="fw-medium text-dark">{{ $laporan->user->name ?? 'Anonim' }}</span>
                                                </div>
                                            </td>
                                            <td><span class="fw-bold fs-6 text-dark" style="letter-spacing: 1px;">{{ $laporan->reported_value }}</span></td>
                                            <td>
                                                @if($laporan->status === 'pending_admin_review')
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-50 rounded-pill px-3 py-2">
                                                        <i class="bi bi-shield-check me-1"></i> Diteruskan ke Admin
                                                    </span>
                                                @elseif($laporan->status === 'rejected')
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-50 rounded-pill px-3 py-2">
                                                        <i class="bi bi-x-circle me-1"></i> Ditolak
                                                    </span>
                                                @else
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-50 rounded-pill px-3 py-2">
                                                        <i class="bi bi-check-all me-1"></i> {{ ucfirst($laporan->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="pe-4 text-center">
                                                <a href="{{ route('verifier.reports.show', $laporan->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm d-inline-flex align-items-center">
                                                    Lihat Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let hash = window.location.hash;
        if (hash) {
            let tabButton = document.querySelector('button[data-bs-target="' + hash + '"]');
            if (tabButton) {
                let tab = new bootstrap.Tab(tabButton);
                tab.show();
            }
        }
    });
</script>

<style>
    .nav-tabs .nav-link { border-bottom: 3px solid transparent !important; }
    .nav-tabs .nav-link.active { border-bottom: 3px solid #0d6efd !important; color: #0f2027 !important; }
    .nav-tabs .nav-link:hover { border-bottom: 3px solid #e9ecef !important; }
</style>
@endsection