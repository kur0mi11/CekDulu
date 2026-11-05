<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CekDulu - Platform Anti Penipuan')</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
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
                                <a class="nav-link text-white {{ request()->routeIs('admin.*') ? 'fw-bold' : '' }}" href="{{ route('admin.index') }}">Antrean Moderasi</a>
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
</body>
</html>