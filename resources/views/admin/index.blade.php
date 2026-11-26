@extends('layouts.app')

@section('title', 'Antrean Moderasi - CekDulu')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-1">Moderasi Laporan</h3>
    <p class="text-muted small mb-0">Tetapkan keputusan akhir untuk laporan yang diteruskan Verifier.</p>
</div>

<div class="card shadow-sm mb-5">
    <div class="card-header bg-white pt-3 pb-0">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="antrean-tab" data-bs-toggle="tab" data-bs-target="#antrean-pane"
                        type="button" role="tab" aria-controls="antrean-pane" aria-selected="true">
                    Menunggu Peninjauan <span class="badge bg-primary ms-1">{{ $queue->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat-pane"
                        type="button" role="tab" aria-controls="riwayat-pane" aria-selected="false">
                    Riwayat Keputusan <span class="badge bg-secondary ms-1">{{ $history->count() }}</span>
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="antrean-pane" role="tabpanel" aria-labelledby="antrean-tab" tabindex="0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <caption class="visually-hidden">Laporan yang menunggu peninjauan</caption>
                    <thead class="table-light">
                        <tr class="small text-nowrap">
                            <th scope="col" class="ps-3 ps-md-4 py-3">Nomor Telepon / Dikirim</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Verifier</th>
                            <th scope="col">Kronologi</th>
                            <th scope="col" class="pe-3 pe-md-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($queue as $report)
                            <tr>
                                <th scope="row" class="ps-3 ps-md-4 py-3 text-nowrap">
                                    <div>{{ $report->reported_value }}</div>
                                    <time class="small fw-normal text-muted" datetime="{{ $report->created_at->toIso8601String() }}">
                                        {{ $report->created_at->format('d M Y, H:i') }} WIB
                                    </time>
                                </th>
                                <td class="text-break">{{ $report->category->name ?? '—' }}</td>
                                <td>{{ $report->verifier->name ?? '—' }}</td>
                                <td class="small text-muted text-break">{{ Str::limit($report->chronology, 60) }}</td>
                                <td class="pe-3 pe-md-4 text-end">
                                    <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-sm btn-primary">Tinjau</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">Tidak ada laporan yang menunggu moderasi akhir.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="riwayat-pane" role="tabpanel" aria-labelledby="riwayat-tab" tabindex="0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <caption class="visually-hidden">Riwayat keputusan laporan</caption>
                    <thead class="table-light">
                        <tr class="small text-nowrap">
                            <th scope="col" class="ps-3 ps-md-4 py-3">Nomor Telepon / Waktu Keputusan</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Keputusan</th>
                            <th scope="col">Admin</th>
                            <th scope="col" class="pe-3 pe-md-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $report)
                            <tr>
                                <th scope="row" class="ps-3 ps-md-4 py-3 text-nowrap">
                                    <div>{{ $report->reported_value }}</div>
                                    <time class="small fw-normal text-muted" datetime="{{ $report->updated_at->toIso8601String() }}">
                                        {{ $report->updated_at->format('d M Y, H:i') }} WIB
                                    </time>
                                </th>
                                <td class="text-break">{{ $report->category->name ?? '—' }}</td>
                                <td>@include('partials.status-badge', ['report' => $report])</td>
                                <td>{{ $report->admin->name ?? '—' }}</td>
                                <td class="pe-3 pe-md-4 text-end text-nowrap">
                                    <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-sm btn-outline-secondary">Lihat Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">Belum ada keputusan yang ditetapkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const moderationTabs = {
        '#antrean-pane': 'antrean-tab',
        '#riwayat-pane': 'riwayat-tab',
    };
    const moderationTab = document.getElementById(moderationTabs[window.location.hash]);
    if (moderationTab && window.bootstrap) {
        bootstrap.Tab.getOrCreateInstance(moderationTab).show();
    }
</script>
@endpush
