<?php

namespace App\Services;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PostViewService
{
    /** Cache key holding the rendered "Most Read Stories" list. */
    private const TOP_READ_KEY = 'blog:most-read-stories';

    /**
     * Record a view for a post, de-duplicated per visitor within the
     * configured window. Refreshes and repeat visits are ignored, and known
     * crawlers are skipped so counts reflect real readers (and so view
     * counting never interferes with how content is served for SEO).
     */
    public function record(BlogPost $post, Request $request): void
    {
        if ($this->isBot($request)) {
            return;
        }

        $window = (int) config('views.dedupe_window');
        $key = "post-view:{$post->id}:{$this->visitorHash($request)}";

        // Cache::add is atomic: it only succeeds the first time within the
        // window, so concurrent requests can't double-count.
        if (! Cache::add($key, true, $window)) {
            return;
        }

        $post->increment('views');

        $this->maybeInvalidateTopRead((int) $post->views);
    }

    /**
     * Top viewed published posts for the sidebar widget. Cached, and selects
     * only the columns the widget needs — no N+1, no heavy payload.
     *
     * @return Collection<int, array{slug: string, title: string, views: int}>
     */
    public function topRead(): Collection
    {
        $limit = (int) config('views.top_count');
        $ttl = (int) config('views.cache_ttl');

        // Cache a plain array (serialization-stable across the file/database
        // cache stores) and wrap it on the way out, rather than caching a
        // Collection object.
        $stories = Cache::remember(self::TOP_READ_KEY, $ttl, fn () => BlogPost::query()
            ->published()
            ->orderByDesc('views')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get(['slug', 'title', 'views'])
            ->map(fn (BlogPost $post) => [
                'slug'  => $post->slug,
                'title' => $post->title,
                'views' => (int) $post->views,
            ])
            ->all());

        return collect($stories);
    }

    public function forgetTopRead(): void
    {
        Cache::forget(self::TOP_READ_KEY);
    }

    /**
     * Flush the widget cache only when a count crosses an "invalidate_every"
     * boundary, so the list stays fresh without busting cache on every hit.
     */
    private function maybeInvalidateTopRead(int $views): void
    {
        $every = (int) config('views.invalidate_every');

        if ($every > 0 && $views % $every === 0) {
            $this->forgetTopRead();
        }
    }

    /** Stable, privacy-preserving fingerprint of the visitor. */
    private function visitorHash(Request $request): string
    {
        return hash('sha256', implode('|', [
            $request->ip(),
            (string) $request->userAgent(),
            (string) config('app.key'),
        ]));
    }

    private function isBot(Request $request): bool
    {
        $agent = $request->userAgent();

        if (! $agent) {
            return true; // no UA → almost always automated traffic
        }

        return (bool) preg_match(
            '/bot|crawl|spider|slurp|mediapartners|facebookexternalhit|embedly|preview|fetch|monitor/i',
            $agent,
        );
    }
}
