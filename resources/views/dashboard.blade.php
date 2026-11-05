@extends('layouts.app')

@section('title', 'Dashboard - CekDulu')

@section('content')
<div class="row mt-4 mb-5">
    <div class="col-md-12">
        
        @if(Auth::user()->role->name === 'Verifier')
            <div class="d-flex flex-column mb-4 mt-2">
                <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">
                    <i class="bi bi-person-badge text-primary me-2"></i>Selamat datang, {{ Auth::user()->name }}!
                </h3>
                <p class="text-muted mt-1 mb-0">Tim Verifikasi CekDulu</p>
            </div>

            <div class="mb-4">
                <div class="row g-4 mb-4">
                    <!-- Metrik 1 -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                            <div class="card-body p-4 d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-inbox fs-3"></i>
                                </div>
                                <div>
                                    <p class="text-muted small fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px;">Antrean Baru</p>
                                    <h2 class="fw-bold text-dark mb-0 mt-1">{{ \App\Models\Report::where('status', 'pending')->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Metrik 2 -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                            <div class="card-body p-4 d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-pencil-square fs-3"></i>
                                </div>
                                <div>
                                    <p class="text-muted small fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px;">Sedang Direvisi</p>
                                    <h2 class="fw-bold text-dark mb-0 mt-1">{{ \App\Models\Report::where('status', 'need_revision')->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metrik 3 -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                            <div class="card-body p-4 d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-check2-circle fs-3"></i>
                                </div>
                                <div>
                                    <p class="text-muted small fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px;">Selesai Dicek</p>
                                    <h2 class="fw-bold text-dark mb-0 mt-1">{{ \App\Models\Report::where('verifier_id', Auth::id())->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between text-center text-md-start">
                            <div class="d-flex flex-column flex-md-row align-items-center mb-3 mb-md-0">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 mb-3 mb-md-0 me-md-4" style="width: 60px; height: 60px;">
                                    <i class="bi bi-shield-check fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Tugas Pemeriksaan</h5>
                                    <p class="text-muted small mb-0 mx-auto mx-md-0" style="max-width: 600px;">
                                        Gunakan menu antrean utama untuk meninjau dan memverifikasi laporan yang masuk. Pastikan validitas bukti sebelum meneruskan ke Admin atau meminta revisi pelapor.
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('verifier.index') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-medium shadow-sm text-nowrap ms-md-3">
                                Buka Antrean <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Dashboard {{ Auth::user()->role->name ?? 'Pengguna' }}</h5>
                </div>
                <div class="card-body">
                    <h4>Selamat datang, {{ Auth::user()->name }}!</h4>
                    
                    @if(Auth::user()->role->name === 'Admin')
                        <p class="text-danger fw-bold">Anda masuk sebagai Administrator.</p>
                        <p>Gunakan menu navigasi di bawah ini untuk meninjau laporan akhir dan mengelola sistem.</p>
                        <a href="{{ route('admin.index') }}" class="btn btn-danger mt-2">Tinjau Laporan Akhir</a>
                    @else
                        <p class="text-success fw-bold">Anda masuk sebagai User.</p>
                        <p>Di bawah ini adalah riwayat laporan indikasi penipuan yang pernah Anda buat.</p>
                    @endif
                </div>
            </div>
        @endif

        @if(Auth::user()->role->name === 'User')
        <div class="d-flex justify-content-between align-items-center mb-3 mt-5">
            <h4 class="mb-0">Riwayat Laporan Saya</h4>
            <a href="{{ route('reports.create') }}" class="btn btn-primary">Buat Laporan Baru</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                @if(isset($reports) && $reports->isEmpty())
                    <p class="text-center text-muted mb-0">Belum ada laporan yang Anda buat.</p>
                @elseif(isset($reports))
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nomor Telepon</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Catatan Revisi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td>{{ $report->created_at->format('d M Y') }}</td>
                                    <td>{{ $report->reported_value }}</td>
                                    <td>{{ $report->category->name ?? '-' }}</td>
                                    <td>
                                        @if($report->status === 'pending')
                                            <span class="badge bg-warning text-dark">Menunggu Pemeriksaan</span>
                                        @elseif($report->status === 'need_revision')
                                            <span class="badge bg-danger">Perlu Revisi</span>
                                        @elseif($report->status === 'pending_admin_review')
                                            <span class="badge bg-info text-dark">Menunggu Peninjauan</span>
                                        @elseif($report->status === 'verified')
                                            <span class="badge bg-success">Terverifikasi</span>
                                        @elseif($report->status === 'rejected')
                                            <span class="badge bg-secondary">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>{{ $report->revision_note ?? '-' }}</td>
                                    <td>
                                        @if($report->status === 'need_revision')
                                            <a href="{{ route('reports.edit', $report->id) }}" class="btn btn-sm btn-outline-danger">Revisi</a>
                                        @else
                                            <a href="{{ route('reports.show', $report->id) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>
@endsection