<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Console - ChessLiv')</title>

    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>♞</text></svg>">

    <!-- Bootstrap 5.3 CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom ChessLiv CSS -->
    <link rel="stylesheet" href="{{ asset('css/chessliv.css') }}">

    <style>
        .admin-nav .nav-link {
            color: rgba(255, 255, 255, 0.75);
            font-weight: 500;
            padding: 0.6rem 1rem;
            border-radius: 6px;
            transition: all 0.15s ease;
        }
        .admin-nav .nav-link:hover {
            color: #ffc107;
            background-color: rgba(255, 255, 255, 0.05);
        }
        .admin-nav .nav-link.active {
            color: #000 !important;
            background-color: #ffc107 !important;
            font-weight: 700;
        }
        .admin-card {
            background-color: #16181c;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }
        .stat-card {
            background: linear-gradient(145deg, #181b20, #131519);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 1.25rem;
        }
        .stat-num {
            font-size: 1.85rem;
            font-weight: 700;
            line-height: 1.1;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-black text-light">

    {{-- Top Admin Navbar --}}
    <nav class="navbar navbar-expand-xl navbar-dark bg-dark border-bottom border-secondary border-opacity-50 py-2 sticky-top shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('admin.ingestion.index') }}">
                <span class="fs-4 text-warning"><i class="bi bi-shield-lock-fill"></i></span>
                <span class="font-heading">CHESS<span class="text-warning">LIV</span></span>
                <span class="badge bg-warning text-dark text-uppercase small ms-1" style="font-size: 0.65rem;">Admin</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-xl-0 admin-nav gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.ingestion.*') ? 'active' : '' }}" href="{{ route('admin.ingestion.index') }}">
                            <i class="bi bi-rss me-1"></i>Feeds & Ingestion
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.history.*') ? 'active' : '' }}" href="{{ route('admin.history.index') }}">
                            <i class="bi bi-clock-history me-1"></i>Import History & Failures
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                            <i class="bi bi-folder2-open me-1"></i>Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.games.*') ? 'active' : '' }}" href="{{ route('admin.games.index') }}">
                            <i class="bi bi-controller me-1"></i>Games Metadata
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.health.*') || request()->routeIs('admin.status.*') ? 'active' : '' }}" href="{{ route('admin.health.index') }}">
                            <i class="bi bi-heart-pulse me-1"></i>System Health
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-secondary text-light">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Public Site
                    </a>

                    @auth
                    <div class="dropdown">
                        <button class="btn btn-sm btn-dark border border-secondary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="rounded-circle bg-warning text-dark fw-bold px-2 py-0 small">A</span>
                            <span>{{ Auth::user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><span class="dropdown-item-text small text-muted">{{ Auth::user()->email }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('admin.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Notifications & Container --}}
    <main class="py-4">
        <div class="container-fluid px-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon me-1"></i>Please fix the following validation errors:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('admin_content')
        </div>
    </main>

    <!-- jQuery 3.7.1 -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script>window.jQuery || document.write('<script src="https://code.jquery.com/jquery-3.7.1.min.js"><\/script>')</script>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>

    @stack('scripts')
</body>
</html>
