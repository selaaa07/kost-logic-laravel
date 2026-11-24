<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - Kost Logic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #212529; }
        .sidebar .nav-link { color: #adb5bd; padding: 10px 16px; border-radius: 6px; margin-bottom: 4px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #ffffff; background-color: #0d6efd; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar p-3">
                <div class="text-white mb-4 px-2">
                    <h5 class="fw-bold mb-0"><i class="bi bi-building me-2"></i>Kost Logic</h5>
                    <small class="text-secondary">Panel Admin</small>
                </div>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ url('/admin/dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/kamar*') ? 'active' : '' }}" href="{{ url('/admin/kamar') }}">
                            <i class="bi bi-door-open me-2"></i> Kamar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/penghuni*') ? 'active' : '' }}" href="{{ url('/admin/penghuni') }}">
                            <i class="bi bi-people me-2"></i> Penghuni
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/pembayaran*') ? 'active' : '' }}" href="{{ url('/admin/pembayaran') }}">
                            <i class="bi bi-cash-stack me-2"></i> Pembayaran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/komplain*') ? 'active' : '' }}" href="{{ url('/admin/komplain') }}">
                            <i class="bi bi-exclamation-circle me-2"></i> Komplain
                        </a>
                    </li>
                </ul>

                <hr class="text-secondary my-4">

                <form action="{{ url('/logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </nav>

            <!-- Konten Utama -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                    <h4 class="fw-bold mb-0">@yield('title', 'Dashboard Admin')</h4>
                    <span class="text-muted small">
                        <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name ?? 'Admin' }}
                    </span>
                </div>

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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>