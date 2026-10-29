<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ChessLiv - The Grandmaster Pulse of Global Chess')</title>
    <meta name="description" content="@yield('meta_description', 'ChessLiv brings you real-time chess news, Candidates tournament recaps, GM game analyses, opening novelties, and daily tactics.')">

    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>♞</text></svg>">

    <!-- Bootstrap 5.3 CSS (Local with CDN fallback) -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom ChessLiv CSS -->
    <link rel="stylesheet" href="{{ asset('css/chessliv.css') }}">

    @stack('styles')
</head>
<body id="top">

    {{-- Header Partial --}}
    @include('partials.header')

    {{-- Main Navigation Bar Partial --}}
    @include('partials.navbar')

    {{-- Breaking News Ticker Partial --}}
    @include('partials.ticker')

    {{-- Main Content Area --}}
    <main class="main-content py-4">
        @yield('content')
    </main>

    {{-- Footer Partial --}}
    @include('partials.footer')

    {{-- Reusable Search Modal Partial --}}
    @include('partials.search-modal')

    <!-- jQuery 3.7.1 (Local with CDN fallback) -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script>window.jQuery || document.write('<script src="https://code.jquery.com/jquery-3.7.1.min.js"><\/script>')</script>

    <!-- Bootstrap 5.3.3 Bundle JS -->
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>

    <!-- Custom ChessLiv jQuery JS -->
    <script src="{{ asset('js/chessliv.js') }}"></script>

    @stack('scripts')
</body>
</html>
