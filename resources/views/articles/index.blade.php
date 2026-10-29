@extends('layouts.app')

@section('title', 'All Chess News & Reports - ChessLiv')
@section('meta_description', 'Browse complete chess news archives, championship recaps, grandmaster profiles, and theoretical analyses.')

@section('content')
<div class="container-xl">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">News Archive</li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <h1 class="font-heading fs-2 fw-bold mb-1">
                <i class="bi bi-newspaper text-warning me-2"></i>Chess News Archive
            </h1>
            <p class="text-muted mb-0">
                Comprehensive coverage of tournaments, theoretical breakthroughs, and master game reviews.
            </p>
        </div>
        <div class="col-md-5 mt-3 mt-md-0">
            <form action="{{ route('articles.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="q" class="form-control" placeholder="Search news..." value="{{ request('q') }}">
                <button type="submit" class="btn btn-warning px-3"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>

    {{-- Category Filter Bar --}}
    <div class="d-flex flex-wrap gap-2 mb-4 p-3 bg-body-tertiary rounded border">
        <a href="{{ route('articles.index') }}" class="btn btn-sm {{ !request('category') ? 'btn-dark' : 'btn-outline-secondary' }}">
            All Categories ({{ $articles->total() }})
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('articles.index', ['category' => $cat->slug]) }}" class="btn btn-sm {{ request('category') === $cat->slug ? 'btn-dark' : 'btn-outline-secondary' }}">
                <i class="bi {{ $cat->icon }} me-1"></i>{{ $cat->name }}
            </a>
        @endforeach
    </div>

    {{-- Articles Grid --}}
    <div class="row g-4">
        @forelse($articles as $article)
            <div class="col-lg-4 col-md-6">
                <div class="article-card">
                    <div class="article-img-wrap">
                        <span class="category-badge">{{ $article->category->name ?? 'News' }}</span>
                        <img src="{{ $article->image_url ?? 'https://images.unsplash.com/photo-1529699211952-734e80c4d42b?auto=format&fit=crop&w=600&q=80' }}" alt="{{ $article->title }}" loading="lazy">
                    </div>
                    <div class="article-body">
                        <h3 class="article-title">
                            <a href="{{ route('articles.show', $article->slug) }}">
                                {{ $article->title }}
                            </a>
                        </h3>
                        <p class="article-summary">
                            {{ $article->summary }}
                        </p>
                        <div class="article-meta">
                            <span><i class="bi bi-person me-1"></i>{{ $article->author_name }}</span>
                            <span><i class="bi bi-clock me-1"></i>{{ $article->read_time }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <div class="text-muted fs-4 mb-2"><i class="bi bi-search"></i> No articles found.</div>
                <p class="text-secondary">Try searching with different keywords or check out all categories.</p>
                <a href="{{ route('articles.index') }}" class="btn btn-outline-warning">Clear Filters</a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-5">
        {{ $articles->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
