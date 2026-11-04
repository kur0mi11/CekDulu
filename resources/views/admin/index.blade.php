@extends('layouts.app')

@section('title', 'Kelola Laporan Masuk')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Moderasi Akhir Laporan (Admin)</h2>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            @if($reports->isEmpty())
                <p class="text-center text-muted mb-0">Tidak ada laporan yang menunggu moderasi akhir.</p>
            @else
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal Masuk</th>
                            <th>Nomor Telepon</th>
                            <th>Kategori</th>
                            <th>Kronologi</th>
                            <th>Aksi Moderasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td>{{ $report->created_at->format('d M Y, H:i') }}</td>
                                <td><span class="fw-bold text-danger">{{ $report->reported_value }}</span></td>
                                <td>{{ $report->category->name ?? '-' }}</td>
                                <td>{{ Str::limit($report->chronology, 50) }}</td>
                                <td>
                                    <form action="{{ route('admin.reports.update', $report->id) }}" method="POST" class="d-flex gap-2">
                                        @csrf
                                        @method('PATCH')
                                        
                                        <input type="hidden" name="status" value="verified">
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setujui laporan ini agar tampil publik?')">Setujui</button>
                                    </form>
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