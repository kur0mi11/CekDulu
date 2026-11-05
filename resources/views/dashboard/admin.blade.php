<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        @include('partials.stat-card', ['label' => 'Menunggu Peninjauan', 'value' => $stats['pending_admin_review'] ?? 0, 'color' => 'danger', 'icon' => 'inbox'])
    </div>
    <div class="col-6 col-lg-3">
        @include('partials.stat-card', ['label' => 'Terverifikasi', 'value' => $stats['verified'] ?? 0, 'color' => 'success', 'icon' => 'check-circle'])
    </div>
    <div class="col-6 col-lg-3">
        @include('partials.stat-card', ['label' => 'Ditolak', 'value' => $stats['rejected'] ?? 0, 'color' => 'secondary', 'icon' => 'x-circle'])
    </div>
    <div class="col-6 col-lg-3">
        @include('partials.stat-card', ['label' => 'Total Laporan', 'value' => array_sum($stats), 'color' => 'primary', 'icon' => 'file-text'])
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        @include('partials.stat-card', ['label' => 'Di Tahap Verifier', 'value' => $stats['pending'] ?? 0, 'color' => 'warning', 'icon' => 'clock'])
    </div>
    <div class="col-md-6">
        @include('partials.stat-card', ['label' => 'Menunggu Revisi Pelapor', 'value' => $stats['need_revision'] ?? 0, 'color' => 'info', 'icon' => 'clock'])
    </div>
</div>

<div class="card shadow-sm mb-5">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Antrean Moderasi Terbaru</h5>
        <a href="{{ route('admin.index') }}" class="btn btn-sm btn-outline-primary">Buka Semua Antrean</a>
    </div>

    @if($queue->isEmpty())
        <div class="card-body text-center text-muted py-5">
            Tidak ada laporan yang menunggu keputusan akhir.
        </div>
    @else
        <div class="list-group list-group-flush">
            @foreach($queue as $item)
                <div class="list-group-item d-flex justify-content-between align-items-center gap-3">
                    <div>
                        <div class="fw-bold">{{ $item->reported_value }}</div>
                        <div class="text-muted small">
                            {{ $item->category->name ?? '-' }} | dikirim {{ $item->created_at->format('d M Y, H:i') }} WIB
                        </div>
                    </div>
                    <a href="{{ route('admin.reports.show', $item->id) }}" class="btn btn-sm btn-primary">Tinjau</a>
                </div>
            @endforeach
        </div>
    @endif
</div>