@extends('layouts.app')

@section('title', 'Antrean Pemeriksaan')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Antrean Pemeriksaan Laporan</h2>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            @if($antrean->isEmpty())
                <p class="text-center text-muted mb-0">Hore! Tidak ada laporan yang mengantre untuk diperiksa.</p>
            @else
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal Masuk</th>
                            <th>Pelapor</th>
                            <th>Nomor Telepon</th>
                            <th>Kategori</th>
                            <th>Status Saat Ini</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($antrean as $laporan)
                            <tr>
                                <td>{{ $laporan->created_at->format('d M Y, H:i') }}</td>
                                <td>{{ $laporan->user->name ?? 'Anonim' }}</td>
                                <td><span class="fw-bold">{{ $laporan->reported_value }}</span></td>
                                <td>{{ $laporan->category->name ?? '-' }}</td>
                                <td>
                                    @if($laporan->status === 'pending')
                                        <span class="badge bg-warning text-dark">Menunggu Pemeriksaan</span>
                                    @elseif($laporan->status === 'need_revision')
                                        <span class="badge bg-danger">Sedang Direvisi User</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- Tombol ini akan mengarah ke halaman detail untuk melihat file bukti -->
                                    <a href="{{ route('verifier.reports.show', $laporan->id) }}" class="btn btn-sm btn-primary">Periksa Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection