@extends('layouts.app')

@section('title', 'About ChessLiv - Independent Chess Journalism')
@section('meta_description', 'About ChessLiv - dedicated to live tournament news, grandmaster analysis, and chess culture.')

@section('content')
<div class="container-xl">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">About ChessLiv</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="p-4 p-md-5 bg-body-tertiary rounded shadow-sm border mb-5">
                <span class="badge bg-warning text-dark text-uppercase fw-bold px-3 py-1 mb-3">About Us</span>
                <h1 class="font-heading display-6 fw-bold mb-4">
                    The Grandmaster Pulse of Global Chess
                </h1>

                <p class="lead text-secondary-emphasis mb-4">
                    ChessLiv is an independent chess media portal built to provide accurate, real-time coverage of the royal game, from the FIDE World Championship cycle to grassroots open tournaments.
                </p>

                <h3 class="font-heading fs-4 fw-bold mt-5 mb-3">
                    <i class="bi bi-shield-check text-warning me-2"></i>Editorial Principles
                </h3>
                <p>
                    Every game analysis, tournament report, and tactical puzzle on ChessLiv is curated with rigorous grandmaster-level review. We respect the rich classical traditions of chess while utilizing state-of-the-art neural engine evaluations to explain deep strategic ideas to players of all strengths.
                </p>

                <h3 class="font-heading fs-4 fw-bold mt-5 mb-3">
                    <i class="bi bi-layers-fill text-warning me-2"></i>Architecture & Standards
                </h3>
                <p>
                    ChessLiv is built with a rock-solid, production-ready stack emphasizing simplicity, lightning-fast response times, and zero client-side framework bloat:
                </p>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item bg-transparent px-0">
                        <i class="bi bi-check-circle-fill text-success me-2"></i><strong>Framework:</strong> Laravel 12 (PHP 8.4)
                    </li>
                    <li class="list-group-item bg-transparent px-0">
                        <i class="bi bi-check-circle-fill text-success me-2"></i><strong>Database:</strong> MySQL / MariaDB (<code>chessliv</code>)
                    </li>
                    <li class="list-group-item bg-transparent px-0">
                        <i class="bi bi-check-circle-fill text-success me-2"></i><strong>Frontend Architecture:</strong> Server-Rendered Blade Layouts &amp; Partials
                    </li>
                    <li class="list-group-item bg-transparent px-0">
                        <i class="bi bi-check-circle-fill text-success me-2"></i><strong>UI &amp; Styling:</strong> Bootstrap 5.3 &amp; Pure Custom CSS
                    </li>
                    <li class="list-group-item bg-transparent px-0">
                        <i class="bi bi-check-circle-fill text-success me-2"></i><strong>Interactivity:</strong> Lightweight jQuery 3.7 (live search, ticker, puzzle hint engine, dark mode)
                    </li>
                    <li class="list-group-item bg-transparent px-0">
                        <i class="bi bi-check-circle-fill text-success me-2"></i><strong>Local Host:</strong> <code>http://chessliv.vkrishna.in:8084/</code>
                    </li>
                </ul>

                <div class="mt-4 pt-3 border-top d-flex gap-3">
                    <a href="{{ route('home') }}" class="btn btn-warning fw-bold">
                        <i class="bi bi-house-door me-1"></i>Back to Home
                    </a>
                    <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-newspaper me-1"></i>Explore News
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
