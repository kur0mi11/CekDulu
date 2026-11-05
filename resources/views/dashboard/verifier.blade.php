<div class="row g-3 mb-4">
    <div class="col-md-4">
        @include('partials.stat-card', ['label' => 'Antrean Baru', 'value' => $stats['pending'] ?? 0, 'color' => 'primary'])
    </div>
    <div class="col-md-4">
        @include('partials.stat-card', ['label' => 'Menunggu Revisi Pelapor', 'value' => $stats['need_revision'] ?? 0, 'color' => 'warning'])
    </div>
    <div class="col-md-4">
        @include('partials.stat-card', ['label' => 'Selesai Saya Periksa', 'value' => $stats['done'] ?? 0, 'color' => 'success'])
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h5 class="fw-bold mb-1">Tugas Pemeriksaan</h5>
            <p class="text-muted small mb-0">Periksa kelengkapan, konsistensi kronologi, dan bukti. Teruskan ke Admin atau minta pelapor merevisi.</p>
        </div>
        <a href="{{ route('verifier.index') }}" class="btn btn-primary text-nowrap">Buka Antrean</a>
    </div>
</div>

<div class="card shadow-sm mb-5">
    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h5 class="fw-bold mb-1">Riwayat Pemeriksaan</h5>
            <p class="text-muted small mb-0">Laporan yang sudah selesai Anda periksa.</p>
        </div>
        <a href="{{ route('verifier.index') }}#riwayat-pane" class="btn btn-outline-secondary text-nowrap">Lihat Riwayat</a>
    </div>
</div>