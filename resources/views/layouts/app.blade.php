<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CekDulu - Platform Anti Penipuan')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">CekDulu</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.index') }}">Daftar Laporan</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-warning ms-2" href="{{ route('reports.create') }}">Buat Laporan</a>
                    </li>
                    
                    @guest
                        <li class="nav-item ms-3">
                            <a class="btn btn-outline-light" href="{{ route('login') }}">Login</a>
                        </li>
                    @endguest

                    @auth
                        <li class="nav-item ms-3">
                            <a class="nav-link text-white fw-bold" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline m-0 p-0">
                                @csrf
                                <button type="submit" class="btn btn-danger ms-2">Logout</button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @yield('content')
    </div>

</body>
</html>