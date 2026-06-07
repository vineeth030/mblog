<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Per-visitor de-duplication window
    |--------------------------------------------------------------------------
    |
    | A single visitor only counts once per post within this many seconds.
    | Page refreshes and repeat visits inside the window are ignored, which
    | stops artificial inflation. Defaults to 24 hours.
    |
    */
    'dedupe_window' => (int) env('VIEWS_DEDUPE_WINDOW', 86400),

    /*
    |--------------------------------------------------------------------------
    | "Most Read Stories" widget
    |--------------------------------------------------------------------------
    |
    | top_count       — how many posts the sidebar widget shows.
    | cache_ttl       — how long (seconds) the widget list is cached.
    | invalidate_every — flush the widget cache once a post's view count crosses
    |                    a multiple of this value (i.e. it "changed significantly").
    |
    */
    'top_count'        => (int) env('VIEWS_TOP_COUNT', 6),
    'cache_ttl'        => (int) env('VIEWS_CACHE_TTL', 600),
    'invalidate_every' => (int) env('VIEWS_INVALIDATE_EVERY', 10),

];
