<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beranda') - Kost Logic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #eef4ff; font-family: 'Poppins', sans-serif; }
        .navbar-biru { background-color: #34568b; }
        .navbar-biru .nav-link { color: #dbe6f5; }
        .navbar-biru .nav-link:hover,
        .navbar-biru .nav-link.active { color: #ffffff; }
        .footer-biru { background-color: #ffffff; border-top: 1px solid #dee2e6; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark navbar-biru">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">Kost Logic</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menuUtama">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    @auth
                        {{-- Penghuni sudah login --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('user/dashboard*') ? 'active' : '' }}" href="{{ url('/user/dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('user/komplain*') ? 'active' : '' }}" href="{{ url('/user/komplain') }}">Komplain</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('user/ubah-password*') ? 'active' : '' }}" href="{{ url('/user/ubah-password') }}">Ubah Password</a>
                        </li>
                        <li class="nav-item text-white small ms-lg-3 me-lg-2">
                            <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm">
                                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                                </button>
                            </form>
                        </li>
                    @else
                        {{-- Tamu --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('login*') ? 'active' : '' }}" href="{{ route('login') }}">Login</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4 flex-grow-1">
        @if (session('sukses'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('sukses') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer-biru text-center py-4">
        <p class="mb-1 text-secondary">Ngekost pakai logic, nyaman tanpa panik</p>
        <p class="mb-0 text-secondary">&copy; {{ date('Y') }} KostLogic.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>