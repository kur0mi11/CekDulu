@extends('layouts.app')

@section('title', 'Dashboard - CekDulu')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
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
                @elseif(Auth::user()->role->name === 'Verifier')
                    <p class="text-primary fw-bold">Anda masuk sebagai Verifier.</p>
                    <p>Gunakan menu navigasi di bawah ini untuk memeriksa antrean laporan yang masuk dari masyarakat.</p>
                    <a href="{{ route('verifier.index') }}" class="btn btn-primary mt-2">Buka Antrean Laporan</a>
                @else
                    <p class="text-success fw-bold">Anda masuk sebagai User.</p>
                    <p>Di bawah ini adalah riwayat laporan indikasi penipuan yang pernah Anda buat.</p>
                @endif
            </div>
        </div>

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