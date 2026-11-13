<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        @include('partials.stat-card', ['label' => 'Total Laporan', 'value' => $reports->count(), 'color' => 'primary'])
    </div>
    <div class="col-6 col-lg-3">
        @include('partials.stat-card', ['label' => 'Menunggu Pemeriksaan', 'value' => $stats['pending'] ?? 0, 'color' => 'warning'])
    </div>
    <div class="col-6 col-lg-3">
        @include('partials.stat-card', ['label' => 'Perlu Revisi', 'value' => $stats['need_revision'] ?? 0, 'color' => 'danger'])
    </div>
    <div class="col-6 col-lg-3">
        @include('partials.stat-card', ['label' => 'Terverifikasi', 'value' => $stats['verified'] ?? 0, 'color' => 'success'])
    </div>
</div>

@if(($stats['need_revision'] ?? 0) > 0)
    <div class="alert alert-warning">
        Ada {{ $stats['need_revision'] }} laporan yang perlu Anda perbaiki.
        Buka detail laporan untuk membaca catatan dari Verifier.
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Riwayat Laporan Saya</h4>
    <a href="{{ route('reports.create') }}" class="btn btn-primary">Buat Laporan Baru</a>
</div>

<div class="card shadow-sm mb-5">
    <div class="card-body">
        @if($reports->isEmpty())
            <div class="text-center py-5">
                <h5 class="fw-bold">Belum ada laporan</h5>
                <p class="text-muted">Mulai dengan mengirim laporan nomor telepon yang Anda curigai sebagai penipuan.</p>
                <a href="{{ route('reports.create') }}" class="btn btn-primary">Buat Laporan Pertama</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nomor Telepon</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td>{{ $report->created_at->format('d M Y') }}</td>
                                <td class="fw-semibold">{{ $report->reported_value }}</td>
                                <td>{{ $report->category->name ?? '-' }}</td>
                                <td>@include('partials.status-badge', ['report' => $report])</td>
                                <td>
                                    @if(in_array($report->status, ['need_revision', 'rejected']) && $report->revision_note)
                                        {{ Str::limit($report->revision_note, 60) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('reports.show', $report->id) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                    @if($report->status === 'need_revision')
                                        <a href="{{ route('reports.edit', $report->id) }}" class="btn btn-sm btn-danger">Revisi</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>