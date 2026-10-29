<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ChessLiv News Feed Sources Configuration
    |--------------------------------------------------------------------------
    |
    | Dedicated configuration for official and public chess RSS/Atom feeds.
    | These feeds respect robots.txt, copyright, terms of service, and
    | attribution standards.
    |
    */

    'request' => [
        'user_agent' => env('CHESSLIV_USER_AGENT', 'ChessLivNewsBot/1.0 (+http://chessliv.vkrishna.in:8084/)'),
        'timeout' => (int)env('CHESSLIV_HTTP_TIMEOUT', 15), // seconds
        'connect_timeout' => (int)env('CHESSLIV_CONNECT_TIMEOUT', 8), // seconds
        'retry_limit' => (int)env('CHESSLIV_HTTP_RETRIES', 2),
        'retry_delay_ms' => (int)env('CHESSLIV_HTTP_RETRY_DELAY_MS', 500),
        'max_items_per_feed' => (int)env('CHESSLIV_MAX_ITEMS_PER_FEED', 15),
        'rate_limit_delay_seconds' => 1,
    ],

    'tracking_parameters_to_strip' => [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'gclid',
        'fbclid',
        'msclkid',
        'mc_cid',
        'mc_eid',
        'ref',
        'source',
        'rss',
    ],

    'default_sources' => [
        [
            'name' => 'FIDE - International Chess Federation',
            'slug' => 'fide-news',
            'feed_url' => 'https://fide.com/news/rss',
            'site_url' => 'https://fide.com',
            'description' => 'Official world chess governing body news, world championship updates, candidates, and ratings.',
            'default_category' => 'world-championship',
            'language' => 'en',
            'is_active' => true,
            'fetch_frequency_minutes' => 60,
        ],
        [
            'name' => 'The Week in Chess (TWIC)',
            'slug' => 'the-week-in-chess',
            'feed_url' => 'https://theweekinchess.com/twic-rss-feed',
            'site_url' => 'https://theweekinchess.com',
            'description' => 'Mark Crowther\'s definitive record of international tournament reports, game archives, and master results.',
            'default_category' => 'tournaments',
            'language' => 'en',
            'is_active' => true,
            'fetch_frequency_minutes' => 120,
        ],
        [
            'name' => 'US Chess Federation (Chess Life)',
            'slug' => 'us-chess',
            'feed_url' => 'https://new.uschess.org/rss.xml',
            'site_url' => 'https://new.uschess.org',
            'description' => 'Official US Chess Federation publication featuring tournament recaps, grandmaster profiles, and youth chess.',
            'default_category' => 'player-profiles',
            'language' => 'en',
            'is_active' => true,
            'fetch_frequency_minutes' => 180,
        ],
        [
            'name' => 'ChessBase News',
            'slug' => 'chessbase-news',
            'feed_url' => 'https://en.chessbase.com/feed',
            'site_url' => 'https://en.chessbase.com',
            'description' => 'Grandmaster game analyses, opening studies, history, and tournament reports.',
            'default_category' => 'gm-insights',
            'language' => 'en',
            'is_active' => true,
            'fetch_frequency_minutes' => 120,
        ],
    ],
];
