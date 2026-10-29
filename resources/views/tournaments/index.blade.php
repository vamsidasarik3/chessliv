@extends('layouts.app')

@section('title', 'World Chess Tournaments & Live Leaderboards - ChessLiv')
@section('meta_description', 'Track active and upcoming international chess championships, Candidates standings, Freestyle Chess, and Swiss Opens.')

@section('content')
<div class="container-xl">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tournaments</li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="font-heading fs-2 fw-bold mb-1">
                <i class="bi bi-trophy-fill text-warning me-2"></i>Global Tournament Arena
            </h1>
            <p class="text-muted mb-0">Follow elite classical, rapid, and blitz championships worldwide.</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <span class="badge bg-danger px-3 py-2 text-uppercase fw-bold">
                <i class="bi bi-broadcast me-1"></i>Live Broadcast Connected
            </span>
        </div>
    </div>

    {{-- Ongoing Section --}}
    <div class="mb-5">
        <div class="section-header">
            <h2 class="section-title fs-4">
                <span class="badge-live-pulse me-2">Live</span>Active Tournaments
            </h2>
        </div>
        <div class="row g-4">
            @forelse($ongoing as $tourn)
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100 p-4 border-start border-4 border-success">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-success-subtle text-success-emphasis fw-bold text-uppercase px-2 py-1">
                                <i class="bi bi-play-circle-fill me-1"></i>In Progress
                            </span>
                            <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $tourn->location }}</span>
                        </div>
                        <h3 class="fs-4 fw-bold mb-2">{{ $tourn->name }}</h3>
                        <div class="mb-3 text-secondary small">
                            <div><i class="bi bi-calendar3 me-2"></i><strong>Dates:</strong> {{ $tourn->dates }}</div>
                            <div><i class="bi bi-diagram-3 me-2"></i><strong>Format:</strong> {{ $tourn->format }}</div>
                        </div>
                        <div class="p-3 bg-body-tertiary rounded mt-auto border">
                            <div class="text-muted small text-uppercase fw-semibold">Current Leader / Top Seed</div>
                            <div class="fs-5 fw-bold text-warning-emphasis">
                                <i class="bi bi-award-fill text-warning me-1"></i>{{ $tourn->current_leader }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><p class="text-muted">No ongoing tournaments right now.</p></div>
            @endforelse
        </div>
    </div>

    {{-- Upcoming Section --}}
    <div class="mb-5">
        <div class="section-header">
            <h2 class="section-title fs-4">
                <i class="bi bi-calendar-event text-primary me-2"></i>Upcoming Super-Tournaments
            </h2>
        </div>
        <div class="row g-4">
            @forelse($upcoming as $tourn)
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100 p-4 border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-primary-subtle text-primary-emphasis fw-bold text-uppercase px-2 py-1">
                                <i class="bi bi-clock-history me-1"></i>Scheduled
                            </span>
                            <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $tourn->location }}</span>
                        </div>
                        <h3 class="fs-4 fw-bold mb-2">{{ $tourn->name }}</h3>
                        <div class="mb-3 text-secondary small">
                            <div><i class="bi bi-calendar3 me-2"></i><strong>Dates:</strong> {{ $tourn->dates }}</div>
                            <div><i class="bi bi-diagram-3 me-2"></i><strong>Format:</strong> {{ $tourn->format }}</div>
                        </div>
                        <div class="p-3 bg-body-tertiary rounded mt-auto border">
                            <div class="text-muted small text-uppercase fw-semibold">Field Status</div>
                            <div class="fw-bold text-primary">
                                {{ $tourn->current_leader }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><p class="text-muted">No upcoming tournaments listed.</p></div>
            @endforelse
        </div>
    </div>

    {{-- Completed Section --}}
    @if($completed->isNotEmpty())
    <div>
        <div class="section-header">
            <h2 class="section-title fs-4">
                <i class="bi bi-check2-circle text-secondary me-2"></i>Recent Results
            </h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle bg-body-tertiary rounded shadow-sm">
                <thead>
                    <tr class="table-dark">
                        <th>Tournament</th>
                        <th>Location</th>
                        <th>Dates</th>
                        <th>Winner / Champion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completed as $tourn)
                        <tr>
                            <td class="fw-bold">{{ $tourn->name }}</td>
                            <td>{{ $tourn->location }}</td>
                            <td>{{ $tourn->dates }}</td>
                            <td><span class="badge bg-warning text-dark"><i class="bi bi-trophy-fill me-1"></i>{{ $tourn->current_leader }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
