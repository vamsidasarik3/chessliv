<footer class="site-footer">
    <div class="container-xl">
        <div class="row g-4">
            {{-- Col 1: About & Stack --}}
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="fs-2 text-warning"><i class="bi bi-shield-fill-check"></i></span>
                    <h4 class="m-0 text-white font-heading">CHESS<span class="text-warning">LIV</span></h4>
                </div>
                <p class="text-secondary small mb-3">
                    ChessLiv delivers premier grandmaster analysis, live championship coverage, tactical repertoires, and breaking news from international chess arenas.
                </p>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">Laravel 12</span>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">PHP 8.4</span>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">MySQL</span>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">Bootstrap 5.3</span>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">jQuery 3.7</span>
                </div>
            </div>

            {{-- Col 2: Categories --}}
            <div class="col-lg-2 col-md-6 col-6">
                <h5>Sections</h5>
                <ul>
                    <li><a href="{{ route('category.show', 'tournaments') }}"><i class="bi bi-chevron-right small me-1"></i>Tournaments</a></li>
                    <li><a href="{{ route('category.show', 'world-championship') }}"><i class="bi bi-chevron-right small me-1"></i>World Match</a></li>
                    <li><a href="{{ route('category.show', 'gm-insights') }}"><i class="bi bi-chevron-right small me-1"></i>GM Insights</a></li>
                    <li><a href="{{ route('category.show', 'opening-theory') }}"><i class="bi bi-chevron-right small me-1"></i>Opening Theory</a></li>
                    <li><a href="{{ route('category.show', 'player-profiles') }}"><i class="bi bi-chevron-right small me-1"></i>Player Profiles</a></li>
                </ul>
            </div>

            {{-- Col 3: Quick Links --}}
            <div class="col-lg-2 col-md-6 col-6">
                <h5>Features</h5>
                <ul>
                    <li><a href="{{ route('tournaments.index') }}"><i class="bi bi-chevron-right small me-1"></i>Live Events</a></li>
                    <li><a href="{{ route('puzzles.index') }}"><i class="bi bi-chevron-right small me-1"></i>Tactics Trainer</a></li>
                    <li><a href="{{ route('articles.index') }}"><i class="bi bi-chevron-right small me-1"></i>News Archive</a></li>
                    <li><a href="{{ route('about') }}"><i class="bi bi-chevron-right small me-1"></i>Editorial Team</a></li>
                </ul>
            </div>

            {{-- Col 4: Newsletter --}}
            <div class="col-lg-4 col-md-6">
                <h5>Grandmaster Dispatch</h5>
                <p class="text-secondary small">
                    Get weekly game analyses, tournament schedules, and daily tactics delivered straight to your inbox.
                </p>
                <form class="newsletter-form mt-3">
                    @csrf
                    <div class="input-group">
                        <input type="email" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="Enter your email" required>
                        <button class="btn btn-warning btn-sm fw-bold" type="submit">Subscribe</button>
                    </div>
                    <div class="newsletter-response mt-2 small" style="display:none;"></div>
                </form>
            </div>
        </div>

        {{-- Footer Bottom --}}
        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-secondary small">
                &copy; {{ date('Y') }} ChessLiv. All Rights Reserved. Running on local host <code>chessliv.vkrishna.in:8084</code>
            </div>
            <div class="d-flex align-items-center gap-3 small">
                <span class="text-muted"><i class="bi bi-database me-1"></i>MySQL: chessliv</span>
                <span class="text-muted"><i class="bi bi-cpu me-1"></i>PHP 8.4</span>
                <a href="#top" class="text-secondary text-decoration-none">
                    <i class="bi bi-arrow-up-circle-fill fs-5 text-warning"></i>
                </a>
            </div>
        </div>
    </div>
</footer>
