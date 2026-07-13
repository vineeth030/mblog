<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\StoryLike;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class LikeService
{
    /** Name of the first-party cookie that anonymously identifies a browser. */
    private const COOKIE = 'visitor_id';

    /** Cookie lifetime in minutes (~1 year), so a "like" survives future visits. */
    private const COOKIE_TTL = 525600;

    /**
     * Toggle the current visitor's like for a post and return the new state.
     *
     * A first-party UUID cookie identifies the browser; it is generated on the
     * first like and queued onto the response for subsequent requests. The
     * stored value is a salted hash, so the raw id never lands in the database.
     *
     * @return array{liked: bool, likes: int}
     */
    public function toggle(BlogPost $post, Request $request): array
    {
        $hash = $this->visitorHash($request);

        $existing = StoryLike::query()
            ->where('blog_post_id', $post->id)
            ->where('visitor_hash', $hash)
            ->first();

        if ($existing) {
            $existing->delete();
            // Guard the counter against ever going negative.
            $post->newQuery()->whereKey($post->id)->where('likes', '>', 0)->decrement('likes');
            $liked = false;
        } else {
            try {
                StoryLike::create([
                    'blog_post_id' => $post->id,
                    'visitor_hash' => $hash,
                ]);
                $post->increment('likes');
                $liked = true;
            } catch (QueryException) {
                // Unique-constraint race: the like already exists, treat as liked.
                $liked = true;
            }
        }

        return [
            'liked' => $liked,
            'likes' => (int) $post->newQuery()->whereKey($post->id)->value('likes'),
        ];
    }

    /** Whether the current visitor has already liked the given post. */
    public function hasLiked(BlogPost $post, Request $request): bool
    {
        if (! $request->cookie(self::COOKIE)) {
            return false;
        }

        return StoryLike::query()
            ->where('blog_post_id', $post->id)
            ->where('visitor_hash', $this->visitorHash($request))
            ->exists();
    }

    /**
     * Stable, privacy-preserving hash of the visitor cookie. Reads the existing
     * cookie or mints a fresh one, queuing it onto the outgoing response.
     */
    private function visitorHash(Request $request): string
    {
        $id = $request->cookie(self::COOKIE);

        if (! $id) {
            $id = (string) Str::uuid();
            Cookie::queue(self::COOKIE, $id, self::COOKIE_TTL);
            // Make it readable within this same request for hasLiked() etc.
            $request->cookies->set(self::COOKIE, $id);
        }

        return hash('sha256', $id.'|'.config('app.key'));
    }
}
