{{-- Search Modal with Real-time jQuery AJAX --}}
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-bottom pb-2">
                <div class="input-group input-group-lg border-0">
                    <span class="input-group-text bg-transparent border-0 pe-2 text-warning">
                        <i class="bi bi-search fs-4"></i>
                    </span>
                    <input type="text" id="searchModalInput" class="form-control border-0 shadow-none ps-0" placeholder="Search articles, GMs, openings, tournaments..." autocomplete="off">
                    <span id="searchSpinner" class="input-group-text bg-transparent border-0 d-none">
                        <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                    </span>
                </div>
                <button type="button" class="btn-close ms-2" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div id="searchResults" style="max-height: 400px; overflow-y: auto;">
                    <p class="text-muted text-center py-4">
                        <i class="bi bi-search me-2"></i>Type at least 2 characters to search chess news...
                    </p>
                </div>
            </div>
            <div class="modal-footer justify-content-between py-2 bg-body-tertiary">
                <small class="text-muted">
                    <kbd>ESC</kbd> to close &nbsp;•&nbsp; <kbd>ENTER</kbd> to view full results
                </small>
                <a href="{{ route('articles.index') }}" class="btn btn-sm btn-outline-secondary">
                    View All News <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
