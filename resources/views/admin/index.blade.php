@extends('layouts.app')

@section('title', 'Antrean Moderasi - CekDulu')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-0">Moderasi Laporan</h3>
    <p class="text-muted small mb-0">Tetapkan keputusan akhir untuk laporan yang sudah diteruskan Verifier.</p>
</div>

<div class="card shadow-sm mb-5">
    <div class="card-header bg-white pt-3 pb-0">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="antrean-tab" data-bs-toggle="tab" data-bs-target="#antrean-pane" type="button" role="tab">
                    Menunggu Peninjauan <span class="badge bg-primary ms-1">{{ $queue->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat-pane" type="button" role="tab">
                    Riwayat Keputusan <span class="badge bg-secondary ms-1">{{ $history->count() }}</span>
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content">

            <div class="tab-pane fade show active" id="antrean-pane" role="tabpanel">
                @if($queue->isEmpty())
                    <div class="text-center text-muted py-5">Tidak ada laporan yang menunggu moderasi akhir.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Dikirim</th>
                                    <th>Nomor Telepon</th>
                                    <th>Kategori</th>
                                    <th>Diperiksa Oleh</th>
                                    <th>Kronologi</th>
                                    <th class="pe-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($queue as $report)
                                    <tr>
                                        <td class="ps-4">{{ $report->created_at->format('d M Y, H:i') }} WIB</td>
                                        <td class="fw-bold">{{ $report->reported_value }}</td>
                                        <td>{{ $report->category->name ?? '-' }}</td>
                                        <td>{{ $report->verifier->name ?? '-' }}</td>
                                        <td>{{ Str::limit($report->chronology, 60) }}</td>
                                        <td class="pe-4 text-center">
                                            <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-sm btn-primary">Tinjau</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="tab-pane fade" id="riwayat-pane" role="tabpanel">
                @if($history->isEmpty())
                    <div class="text-center text-muted py-5">Belum ada keputusan yang ditetapkan.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Tanggal Keputusan</th>
                                    <th>Nomor Telepon</th>
                                    <th>Kategori</th>
                                    <th>Keputusan</th>
                                    <th>Admin</th>
                                    <th class="pe-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $report)
                                    <tr>
                                        <td class="ps-4">{{ $report->updated_at->format('d M Y, H:i') }} WIB</td>
                                        <td class="fw-bold">{{ $report->reported_value }}</td>
                                        <td>{{ $report->category->name ?? '-' }}</td>
                                        <td>@include('partials.status-badge', ['report' => $report])</td>
                                        <td>{{ $report->admin->name ?? '-' }}</td>
                                        <td class="pe-4 text-center">
                                            <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-sm btn-outline-secondary">Lihat Detail</a>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var hash = window.location.hash;
        if (hash) {
            var btn = document.querySelector('button[data-bs-target="' + hash + '"]');
            if (btn) { new bootstrap.Tab(btn).show(); }
        }
    });
</script>
@endsection