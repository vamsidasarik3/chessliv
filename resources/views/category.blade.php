@extends('layouts.app')

@section('title', $category->name . ' - ChessLiv News')
@section('meta_description', $category->description ?? 'Browse all chess articles in ' . $category->name)

@section('content')
<div class="container-xl">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('articles.index') }}" class="text-decoration-none">News</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    {{-- Category Header Hero --}}
    <div class="p-4 p-md-5 mb-5 rounded bg-dark text-white border-bottom border-warning border-3" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-warning text-dark text-uppercase fw-bold px-3 py-1 mb-2">Category Section</span>
                <h1 class="font-heading display-6 fw-bold mb-2 text-white">
                    <i class="bi {{ $category->icon }} text-warning me-2"></i>{{ $category->name }}
                </h1>
                <p class="lead text-secondary mb-0">
                    {{ $category->description ?? 'All the latest developments, analyses, and stories.' }}
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="fs-4 text-warning fw-bold">{{ $articles->total() }}</span>
                <span class="text-muted d-block small">Stories Published</span>
            </div>
        </div>
    </div>

    {{-- Articles Grid --}}
    <div class="row g-4">
        @forelse($articles as $article)
            <div class="col-lg-4 col-md-6">
                <div class="article-card">
                    <div class="article-img-wrap">
                        <span class="category-badge">{{ $category->name }}</span>
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
            <div class="col-12 text-center py-5">
                <p class="text-muted fs-5">No stories found in this section yet.</p>
                <a href="{{ route('articles.index') }}" class="btn btn-warning">View All News</a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-5">
        {{ $articles->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
