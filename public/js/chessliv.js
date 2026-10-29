/**
 * ChessLiv - Custom jQuery Interactive Script
 * Powers dynamic UI, search, theme switcher, ticker, puzzles, and newsletter
 */

$(document).ready(function () {
    // 1. Setup CSRF token for all jQuery AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // 2. Real-time Live Clock in Header
    function updateLiveClock() {
        const now = new Date();
        const options = {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        $('#liveClock').text(now.toLocaleDateString('en-US', options));
    }
    if ($('#liveClock').length) {
        updateLiveClock();
        setInterval(updateLiveClock, 1000);
    }

    // 3. Theme Toggle (Light / Dark)
    const storedTheme = localStorage.getItem('chessliv_theme') || 'light';
    $('html').attr('data-bs-theme', storedTheme);
    updateThemeIcon(storedTheme);

    $('#themeToggleBtn').on('click', function (e) {
        e.preventDefault();
        const currentTheme = $('html').attr('data-bs-theme') === 'dark' ? 'dark' : 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        $('html').attr('data-bs-theme', newTheme);
        localStorage.setItem('chessliv_theme', newTheme);
        updateThemeIcon(newTheme);
    });

    function updateThemeIcon(theme) {
        const $icon = $('#themeToggleBtn i');
        if (theme === 'dark') {
            $icon.removeClass('bi-moon-stars-fill').addClass('bi-sun-fill');
            $('#themeToggleBtn').attr('title', 'Switch to Light Mode');
        } else {
            $icon.removeClass('bi-sun-fill').addClass('bi-moon-stars-fill');
            $('#themeToggleBtn').attr('title', 'Switch to Dark Mode');
        }
    }

    // 4. Live Search Modal with Debounce
    let searchTimer = null;
    $('#searchModalInput').on('keyup', function () {
        const query = $(this).val().trim();
        const $resultsContainer = $('#searchResults');
        const $loadingSpinner = $('#searchSpinner');

        clearTimeout(searchTimer);

        if (query.length < 2) {
            $resultsContainer.html('<p class="text-muted text-center py-4"><i class="bi bi-search me-2"></i>Type at least 2 characters to search chess news...</p>');
            $loadingSpinner.addClass('d-none');
            return;
        }

        $loadingSpinner.removeClass('d-none');

        searchTimer = setTimeout(function () {
            $.ajax({
                url: '/search',
                method: 'GET',
                data: { q: query },
                dataType: 'json',
                success: function (data) {
                    $loadingSpinner.addClass('d-none');
                    if (data.length === 0) {
                        $resultsContainer.html('<div class="text-center py-4 text-muted"><i class="bi bi-exclamation-circle me-1"></i>No articles found matching "' + $('<div>').text(query).html() + '"</div>');
                        return;
                    }

                    let html = '<div class="list-group list-group-flush">';
                    $.each(data, function (index, item) {
                        html += `
                            <a href="${item.url}" class="list-group-item list-group-item-action py-3">
                                <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">${item.category}</span>
                                    <small class="text-muted">${item.published}</small>
                                </div>
                                <h6 class="mb-1 text-primary fw-semibold">${$('<div>').text(item.title).html()}</h6>
                            </a>
                        `;
                    });
                    html += '</div>';
                    $resultsContainer.html(html);
                },
                error: function () {
                    $loadingSpinner.addClass('d-none');
                    $resultsContainer.html('<div class="text-danger text-center py-3">Error fetching search results. Please try again.</div>');
                }
            });
        }, 300);
    });

    // Clear search on modal hide
    $('#searchModal').on('hidden.bs.modal', function () {
        $('#searchModalInput').val('');
        $('#searchResults').html('<p class="text-muted text-center py-4"><i class="bi bi-search me-2"></i>Type at least 2 characters to search chess news...</p>');
    });

    // Focus input when modal opens
    $('#searchModal').on('shown.bs.modal', function () {
        $('#searchModalInput').focus();
    });

    // 5. Daily Puzzle Interactive Controls
    $('.btn-puzzle-hint').on('click', function () {
        const $target = $(this).closest('.puzzle-card, .sidebar-widget').find('.puzzle-hint-box');
        $target.slideToggle(200);
        $(this).toggleClass('active');
    });

    $('.btn-puzzle-solution').on('click', function () {
        const $target = $(this).closest('.puzzle-card, .sidebar-widget').find('.puzzle-solution-box');
        $target.slideToggle(200);
        $(this).toggleClass('active');
    });

    // 6. Homepage Category Filter Tabs
    $('.category-filter-btn').on('click', function (e) {
        e.preventDefault();
        $('.category-filter-btn').removeClass('active');
        $(this).addClass('active');

        const filter = $(this).data('filter');
        if (filter === 'all') {
            $('.article-grid-item').fadeIn(250);
        } else {
            $('.article-grid-item').hide();
            $('.article-grid-item[data-category="' + filter + '"]').fadeIn(250);
        }
    });

    // 7. Article Font Size Adjuster
    let currentFontSize = 18; // default in px
    $('#fontIncrease').on('click', function () {
        if (currentFontSize < 26) {
            currentFontSize += 2;
            $('.article-content').css('font-size', currentFontSize + 'px');
        }
    });
    $('#fontDecrease').on('click', function () {
        if (currentFontSize > 14) {
            currentFontSize -= 2;
            $('.article-content').css('font-size', currentFontSize + 'px');
        }
    });
    $('#fontReset').on('click', function () {
        currentFontSize = 18;
        $('.article-content').css('font-size', '1.12rem');
    });

    // 8. Newsletter Subscription AJAX
    $('.newsletter-form').on('submit', function (e) {
        e.preventDefault();
        const $form = $(this);
        const $input = $form.find('input[type="email"]');
        const $btn = $form.find('button[type="submit"]');
        const $msg = $form.find('.newsletter-response');
        const email = $input.val();

        if (!email) return;

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Subscribing...');

        $.ajax({
            url: '/newsletter/subscribe',
            method: 'POST',
            data: { email: email },
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false).html('Subscribed!');
                $input.val('');
                $msg.removeClass('text-danger d-none').addClass('text-success').text(res.message).fadeIn();
                setTimeout(function () {
                    $btn.html('Subscribe');
                }, 3000);
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('Subscribe');
                let errText = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.email) {
                    errText = xhr.responseJSON.errors.email[0];
                }
                $msg.removeClass('text-success d-none').addClass('text-danger').text(errText).fadeIn();
            }
        });
    });

    // 9. Breaking News Ticker Pause on Hover
    const $tickerTrack = $('.ticker-items');
    $tickerTrack.on('mouseenter', function () {
        $(this).css('animation-play-state', 'paused');
    }).on('mouseleave', function () {
        $(this).css('animation-play-state', 'running');
    });

    // 10. Copy Link Button
    $('.btn-copy-link').on('click', function () {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            const $btn = $(this);
            const originalHtml = $btn.html();
            $btn.html('<i class="bi bi-check2 me-1"></i>Copied!');
            setTimeout(() => {
                $btn.html(originalHtml);
            }, 2000);
        });
    });

    // 11. Interactive Chess Game Viewer
    if ($('#gameViewer').length) {
        let currentMoveIndex = -1;
        const $moves = $('.pgn-move-btn');
        const totalMoves = $moves.length;

        function updateMoveHighlight(index) {
            $('.move-span').removeClass('bg-warning text-dark fw-bold');
            if (index >= 0 && index < totalMoves) {
                const $targetMove = $moves.eq(index);
                $targetMove.find('.move-span').addClass('bg-warning text-dark fw-bold');

                // Auto-scroll table to keep active move visible
                const container = $('#pgnMoveList');
                const row = $targetMove.closest('tr');
                if (row.length && container.length) {
                    const rowTop = row.position().top;
                    if (rowTop < 0 || rowTop > container.height() - 40) {
                        container.scrollTop(container.scrollTop() + rowTop - 40);
                    }
                }

                // Update text display
                const moveNum = Math.floor(index / 2) + 1;
                const isBlack = (index % 2 !== 0);
                const moveText = $targetMove.find('.move-span').text().trim();
                $('#activeMoveDisplay').text('Move ' + moveNum + (isBlack ? ' ... ' : '. ') + moveText);
                $('#boardTurnIndicator').text(isBlack ? 'White to move' : 'Black to move');
            } else {
                $('#activeMoveDisplay').text('Start Position');
                $('#boardTurnIndicator').text('White to move');
            }
        }

        $('#btnMoveNext').on('click', function () {
            if (currentMoveIndex < totalMoves - 1) {
                currentMoveIndex++;
                updateMoveHighlight(currentMoveIndex);
            }
        });

        $('#btnMovePrev').on('click', function () {
            if (currentMoveIndex >= 0) {
                currentMoveIndex--;
                updateMoveHighlight(currentMoveIndex);
            }
        });

        $('#btnMoveStart').on('click', function () {
            currentMoveIndex = -1;
            updateMoveHighlight(currentMoveIndex);
        });

        $('#btnMoveEnd').on('click', function () {
            currentMoveIndex = totalMoves - 1;
            updateMoveHighlight(currentMoveIndex);
        });

        $('.pgn-move-btn').on('click', function () {
            const idx = parseInt($(this).data('move-idx'), 10);
            if (!isNaN(idx)) {
                currentMoveIndex = idx;
                updateMoveHighlight(currentMoveIndex);
            }
        });

        // Flip Board perspective
        let boardFlipped = false;
        $('#btnFlipBoard').on('click', function () {
            boardFlipped = !boardFlipped;
            const $board = $('#interactiveBoard');
            if (boardFlipped) {
                $board.css('transform', 'rotate(180deg)');
                $board.find('.chess-square').css('transform', 'rotate(180deg)');
                $(this).addClass('btn-warning text-dark').removeClass('btn-outline-secondary');
            } else {
                $board.css('transform', 'none');
                $board.find('.chess-square').css('transform', 'none');
                $(this).removeClass('btn-warning text-dark').addClass('btn-outline-secondary');
            }
        });

        // Copy raw PGN
        $('#btnCopyPgn').on('click', function () {
            const rawPgn = $('#rawPgnText').val();
            navigator.clipboard.writeText(rawPgn).then(() => {
                const $b = $(this);
                const old = $b.html();
                $b.html('<i class="bi bi-check2 text-success me-1"></i>Copied!');
                setTimeout(() => $b.html(old), 2000);
            });
        });
    }

    // 12. Global Broken Image Fallback Listener
    $('img').on('error', function () {
        const placeholder = '/images/chess-placeholder.svg';
        if ($(this).attr('src') !== placeholder) {
            $(this).attr('src', placeholder);
        }
    });
});
