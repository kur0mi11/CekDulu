@extends('layouts.app')

@section('title', 'Notifikasi - CekDulu')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <h4 class="mb-0 fw-bold">Notifikasi</h4>
                <p class="text-muted small mb-0">Pemberitahuan terkait perkembangan laporan Anda</p>
            </div>

            @if($unreadCount > 0)
                <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-check2-all me-1"></i> Tandai Semua Sudah Dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="mb-3 d-flex gap-2">
            <a href="{{ route('notifications.index') }}" 
               class="btn btn-sm {{ empty($filter) ? 'btn-primary' : 'btn-outline-secondary' }}">
                Semua ({{ $totalCount }})
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
               class="btn btn-sm {{ $filter === 'unread' ? 'btn-primary' : 'btn-outline-secondary' }}">
                Belum Dibaca ({{ $unreadCount }})
            </a>
        </div>

        @if($notifications->isEmpty())
            <div class="card shadow-sm border-0 text-center py-5">
                <div class="card-body">
                    <div class="text-muted mb-3">
                        <i class="bi bi-bell-slash fs-1"></i>
                    </div>
                    <h5 class="fw-semibold">Tidak ada notifikasi</h5>
                    <p class="text-muted small mb-0">
                        {{ $filter === 'unread' ? 'Semua notifikasi sudah Anda baca.' : 'Belum ada notifikasi aktivitas untuk akun Anda saat ini.' }}
                    </p>
                </div>
            </div>
        @else
            <div class="list-group shadow-sm border-0 mb-4">
                @foreach($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $isUnread = is_null($notification->read_at);
                        $badgeClass = $data['badge_class'] ?? 'primary';
                        $icon = $data['icon'] ?? 'bell-fill';
                    @endphp
                    <div class="list-group-item list-group-item-action p-3 border-0 border-bottom {{ $isUnread ? 'bg-light border-start border-primary border-4' : '' }}">
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0">
                                <span class="badge rounded-circle p-2 bg-{{ $badgeClass }}-subtle text-{{ $badgeClass }} fs-5">
                                    <i class="bi bi-{{ $icon }}"></i>
                                </span>
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-semibold {{ $isUnread ? 'text-dark' : 'text-secondary' }}">
                                        {{ $data['title'] ?? 'Pemberitahuan' }}
                                        @if($isUnread)
                                            <span class="badge bg-primary ms-1" style="font-size: 0.65rem;">Baru</span>
                                        @endif
                                    </h6>
                                    <small class="text-muted" style="font-size: 0.8rem;">
                                        <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>

                                <p class="mb-2 text-muted small">
                                    {{ $data['message'] ?? '' }}
                                </p>

                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <a href="{{ route('notifications.readAndRedirect', $notification->id) }}" class="btn btn-sm btn-primary py-0 px-2" style="font-size: 0.8rem;">
                                        <i class="bi bi-eye me-1"></i> Lihat Detail
                                    </a>

                                    @if($isUnread)
                                        <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.8rem;">
                                                Tandai Dibaca
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Hapus notifikasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0 ms-2 text-decoration-none" style="font-size: 0.8rem;">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
