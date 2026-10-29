@php
    $breakingItems = \App\Models\Article::latest('published_at')->take(5)->get();
@endphp

<div class="ticker-bar">
    <div class="container-xl d-flex align-items-center p-0">
        <div class="ticker-label">
            <i class="bi bi-lightning-charge-fill"></i>
            <span>Flash News</span>
        </div>
        <div class="ticker-items flex-grow-1">
            @forelse($breakingItems as $item)
                <a href="{{ route('articles.show', $item->slug) }}" class="ticker-item-link">
                    <span class="badge bg-warning text-dark me-1">{{ $item->category->name ?? 'Update' }}</span>
                    {{ $item->title }}
                    <span class="text-secondary ms-2">•</span>
                </a>
            @empty
                <span class="ticker-item-link">Welcome to ChessLiv - World Championship and Grandmaster coverage updated 24/7.</span>
            @endforelse
        </div>
    </div>
</div>
