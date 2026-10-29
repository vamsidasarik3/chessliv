<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - ChessLiv Ingestion Console</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chessliv.css') }}">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 50% 30%, #1e293b 0%, #090d16 100%);
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 12px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.6);
        }
    </style>
</head>
<body>
<div class="container p-3">
    <div class="login-card mx-auto p-4 p-md-5">
        <div class="text-center mb-4">
            <span class="fs-1 text-warning"><i class="bi bi-shield-lock-fill"></i></span>
            <h1 class="font-heading fs-3 fw-bold text-white mt-2 mb-1">CHESS<span class="text-warning">LIV</span></h1>
            <p class="text-muted small">News Ingestion & Administration Console</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger py-2 small mb-3">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success py-2 small mb-3">
                <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger py-2 small mb-3">
                @foreach($errors->all() as $err)
                    <div>{{ $err }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label text-light small fw-bold">Admin Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark text-secondary border-secondary"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control bg-dark text-white border-secondary" value="{{ old('email', 'admin@chessliv.com') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label text-light small fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark text-secondary border-secondary"><i class="bi bi-key"></i></span>
                    <input type="password" name="password" id="password" class="form-control bg-dark text-white border-secondary" placeholder="Enter password (default: admin123)" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label text-muted small" for="remember">Remember session</label>
                </div>
                <small class="text-muted"><kbd class="bg-secondary text-white">admin123</kbd></small>
            </div>

            <button type="submit" class="btn btn-warning w-100 fw-bold py-2 mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i>Authenticate & Enter
            </button>

            <div class="text-center">
                <a href="{{ route('home') }}" class="text-muted small text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i>Return to Public Website
                </a>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
