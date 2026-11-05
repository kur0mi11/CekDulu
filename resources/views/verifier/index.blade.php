@extends('layouts.app')

@section('title', 'Antrean Pemeriksaan - Verifikator')

@section('content')
<div class="container mt-2">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-0" style="color: #0f2027;">
                <i class="bi bi-inboxes text-primary me-2"></i>Antrean Pemeriksaan
            </h3>
            <p class="text-muted small mb-0 mt-1">Kelola dan verifikasi laporan indikasi penipuan dari masyarakat.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Card Utama -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
        
        <!-- Area Pencarian Terintegrasi -->
        <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
            <div class="row justify-content-between align-items-center">
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
        </div>

        <!-- Tabel Antrean -->
        <div class="card-body p-0">
            @if($antrean->isEmpty())
                <!-- State Kosong Modern -->
                <div class="text-center py-5">
                    <div class="display-1 text-muted opacity-25 mb-3">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <h5 class="fw-bold text-muted">Antrean Kosong</h5>
                    <p class="text-muted">
                        @if(request('search'))
                            Tidak ada laporan yang cocok dengan kata kunci "<b>{{ request('search') }}</b>".
                        @else
                            Kerja bagus! Semua laporan telah selesai diperiksa saat ini.
                        @endif
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <tr>
                                <th class="ps-4 py-3 border-0">Waktu Masuk</th>
                                <th class="py-3 border-0">Pelapor</th>
                                <th class="py-3 border-0">Target Laporan</th>
                                <th class="py-3 border-0">Kategori</th>
                                <th class="py-3 border-0">Status</th>
                                <th class="pe-4 py-3 border-0 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @foreach($antrean as $laporan)
                                <tr>
                                    <!-- Kolom Tanggal -->
                                    <td class="ps-4">
                                        <div class="fw-semibold text-dark">{{ $laporan->created_at->format('d M Y') }}</div>
                                        <div class="text-muted small"><i class="bi bi-clock me-1"></i>{{ $laporan->created_at->format('H:i') }} WIB</div>
                                    </td>
                                    
                                    <!-- Kolom Pelapor -->
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <span class="fw-medium text-dark">{{ $laporan->user->name ?? 'Anonim' }}</span>
                                        </div>
                                    </td>
                                    
                                    <!-- Kolom Target (Nomor/Rekening) -->
                                    <td>
                                        <span class="fw-bold fs-6 text-dark" style="letter-spacing: 1px;">{{ $laporan->reported_value }}</span>
                                    </td>
                                    
                                    <!-- Kolom Kategori -->
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2 fw-normal">
                                            {{ $laporan->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    
                                    <!-- Kolom Status -->
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
                                    
                                    <!-- Kolom Aksi -->
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
    </div>
</div>
@endsection