<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CekDulu - Platform Anti Penipuan')</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; }

        .password-toggle-btn {
            border-color: #dee2e6;
            color: #6c757d;
            background-color: #fff;
            cursor: pointer;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
        }
        .password-toggle-btn:hover {
            color: #0d6efd;
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        .form-control:focus + .password-toggle-btn,
        .password-toggle-btn:focus {
            border-color: #86b7fe;
            box-shadow: none;
        }
        .form-control.is-invalid + .password-toggle-btn {
            border-color: #dc3545;
        }
        .input-group:has(.form-control.is-invalid) .password-toggle-btn {
            border-color: #dc3545;
        }
        .input-group .form-control.is-invalid {
            background-image: none;
            padding-right: 0.75rem;
        }
    </style>
</head>
<body class="bg-light">

    @php
        $role = Auth::check() ? Auth::user()->role?->name : null;
    @endphp

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">CekDulu</a>

            <button class="btn btn-outline-light btn-sm d-lg-none" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Buka menu">
                Menu
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">

                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('/') ? 'fw-bold' : '' }}" href="{{ url('/') }}">Beranda</a>
                    </li>

                    @auth
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->routeIs('dashboard') ? 'fw-bold' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>

                        @if($role === 'Verifier')
                            <li class="nav-item">
                                <a class="nav-link text-white {{ request()->routeIs('verifier.*') ? 'fw-bold' : '' }}" href="{{ route('verifier.index') }}">Antrean Pemeriksaan</a>
                            </li>
                        @elseif($role === 'Admin')
                            <li class="nav-item">
                                <a class="nav-link text-white {{ request()->routeIs('admin.index', 'admin.reports.*') ? 'fw-bold' : '' }}" href="{{ route('admin.index') }}">Antrean Moderasi</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white {{ request()->routeIs('admin.categories.*') ? 'fw-bold' : '' }}" href="{{ route('admin.categories.index') }}">Kategori Penipuan</a>
                            </li>
                        @endif
                    @endauth

                    @if(!$role || $role === 'User')
                        <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                            <a class="btn btn-warning rounded-pill px-4 shadow-sm" href="{{ route('reports.create') }}">Buat Laporan</a>
                        </li>
                    @endif

                    <li class="nav-item d-none d-lg-block mx-3">
                        <div class="vr bg-white opacity-25" style="height: 25px;"></div>
                    </li>

                    @guest
                        <li class="nav-item mt-3 mt-lg-0">
                            <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill px-4">Login</a>
                        </li>
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <a href="{{ route('register') }}" class="btn btn-light text-primary rounded-pill px-4 fw-bold shadow-sm">Register</a>
                        </li>
                    @else
                        <!-- Notifikasi Dropdown -->
                        <li class="nav-item dropdown me-lg-2 my-2 my-lg-0">
                            @php
                                $unreadCount = Auth::user()->unreadNotifications()->count();
                                $recentNotifications = Auth::user()->notifications()->latest()->take(5)->get();
                            @endphp
                            <a class="nav-link text-white position-relative py-1 px-2" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
                                <i class="bi bi-bell-fill fs-5"></i>
                                @if($unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                        <span class="visually-hidden">notifikasi belum dibaca</span>
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" aria-labelledby="notifDropdown" style="width: 320px; max-width: 90vw;">
                                <li class="d-flex justify-content-between align-items-center px-2 py-1 border-bottom mb-2">
                                    <span class="fw-bold small">Notifikasi ({{ $unreadCount }} baru)</span>
                                    @if($unreadCount > 0)
                                        <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="m-0 p-0">
                                            @csrf
                                            <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 0.75rem;">Tandai dibaca</button>
                                        </form>
                                    @endif
                                </li>
                                @forelse($recentNotifications as $notif)
                                    @php
                                        $nData = $notif->data;
                                        $badgeBg = $nData['badge_class'] ?? 'primary';
                                        $nIcon = $nData['icon'] ?? 'bell-fill';
                                    @endphp
                                    <li>
                                        <a class="dropdown-item rounded p-2 mb-1 {{ $notif->read_at ? 'text-muted' : 'bg-light fw-medium' }}" href="{{ route('notifications.readAndRedirect', $notif->id) }}">
                                            <div class="d-flex align-items-start gap-2">
                                                <span class="badge rounded-circle p-2 bg-{{ $badgeBg }}-subtle text-{{ $badgeBg }} mt-1">
                                                    <i class="bi bi-{{ $nIcon }}"></i>
                                                </span>
                                                <div class="w-100 overflow-hidden">
                                                    <div class="fw-semibold text-dark text-truncate" style="font-size: 0.85rem;">{{ $nData['title'] ?? 'Pemberitahuan' }}</div>
                                                    <div class="text-muted small text-truncate" style="font-size: 0.75rem;">{{ $nData['message'] ?? '' }}</div>
                                                    <small class="text-secondary" style="font-size: 0.7rem;">{{ $notif->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="text-center py-3 text-muted small">
                                        Tidak ada notifikasi
                                    </li>
                                @endforelse
                                <li class="border-top pt-2 text-center mt-1">
                                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary btn-sm w-100 py-1" style="font-size: 0.8rem;">
                                        Lihat Semua Notifikasi
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item mt-2 mt-lg-0">
                            <span class="navbar-text text-white">
                                {{ Auth::user()->name }}
                                <span class="badge bg-light text-primary ms-1">{{ $role }}</span>
                            </span>
                        </li>
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0 d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger rounded-pill px-4 shadow-sm">Logout</button>
                            </form>
                        </li>
                    @endguest

                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @include('partials.password-toggle-script')
    @stack('scripts')
    
</body>
</html>