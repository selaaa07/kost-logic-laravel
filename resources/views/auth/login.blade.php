<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kost Logic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f6fb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 420px;
            width: 100%;
            border: none;
            border-top: 4px solid #5b9bd5;
            border-radius: 12px;
        }
        .btn-soft-blue {
            background-color: #5b9bd5;
            border: none;
            color: #fff;
        }
        .btn-soft-blue:hover {
            background-color: #4a89c7;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="card shadow-sm p-4 login-card">
        <div class="card-body">
            <h3 class="fw-bold text-center mb-1" style="color: #5b9bd5;">Kost Logic</h3>
            <p class="text-center text-secondary mb-4">Masuk ke Portal Admin / Penghuni</p>

            @if (session('sukses'))
                <div class="alert alert-success">{{ session('sukses') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login.proses') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="contoh: admin@kostlogic.com" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn btn-soft-blue w-100 py-2 fw-semibold">Masuk</button>
            </form>
        </div>
    </div>
</body>
</html>