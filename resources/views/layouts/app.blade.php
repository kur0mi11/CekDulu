<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CekDulu - Platform Anti Penipuan')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">CekDulu</a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/">Beranda</a>
                    </li>
                    
                    @if(!Auth::check() || Auth::user()->role_id == 1)
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="btn btn-warning rounded-pill px-4 shadow-sm" href="{{ route('reports.create') }}">
                            <i class="bi bi-megaphone-fill me-1"></i> Buat Laporan
                        </a>
                    </li>
                    @endif

                    <div class="vr bg-white opacity-25 mx-3 d-none d-lg-block" style="height: 25px;"></div>
                    
                    @guest
                        <li class="nav-item mt-3 mt-lg-0">
                            <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill px-4">
                                Login
                            </a>
                        </li>
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <a href="{{ route('register') }}" class="btn btn-light text-primary rounded-pill px-4 fw-bold shadow-sm">
                                Register
                            </a>
                        </li>
                    @else
                        <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                            <a class="nav-link text-white" href="{{ route('dashboard') }}">
                                <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
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
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>