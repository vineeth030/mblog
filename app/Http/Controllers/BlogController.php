<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Services\LikeService;
use App\Services\PostViewService;
use App\Support\Breadcrumbs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;
use League\CommonMark\CommonMarkConverter;

class BlogController extends Controller
{
    private CommonMarkConverter $md;

    public function __construct()
    {
        $this->md = new CommonMarkConverter([
            'html_input'         => 'strip',
            'allow_unsafe_links' => false,
            'renderer'           => ['soft_break' => "<br />\n"],
        ]);
    }

    public function index(Request $request): Response|RedirectResponse
    {
        // Legacy category filter (?category=Name) now lives at /category/{slug}.
        // Permanently redirect so any indexed query-param URLs consolidate onto
        // the clean, self-canonical category pages.
        if ($category = $request->query('category')) {
            $model = Category::where('name', $category)->first();

            return $model
                ? redirect()->route('category.show', $model->slug, 301)
                : redirect()->route('blog.index', [], 301);
        }

        $posts = BlogPost::with(['category', 'author'])
            ->where('publish_status', true)
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (BlogPost $post) => [
                'slug'            => $post->slug,
                'title'           => $post->title,
                'description'     => $post->description,
                'category'        => $post->category?->name,
                'author_name'     => $post->author?->name,
                'author_slug'     => $post->author?->slug,
                'cover_image_url' => $post->cover_image_url,
                'created_at'      => $post->created_at->format('M j, Y'),
                'views'           => $post->views,
                'likes'           => (int) $post->likes,
            ]);

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
        ]);
    }

    public function search(Request $request): Response
    {
        $term = trim((string) $request->query('q', ''));

        // Skip the query entirely for empty/1-char input rather than running a
        // pointless full scan; render the page with an empty result set.
        if (mb_strlen($term) < 2) {
            $posts = new LengthAwarePaginator([], 0, 10);
            $posts->withQueryString();
        } else {
            // Escape LIKE metacharacters so the visitor's own input can't widen
            // their match with an unintended `%`/`_` wildcard.
            $escaped = addcslashes($term, '%_\\');

            $posts = BlogPost::published()
                ->with(['category', 'author'])
                ->where(function ($query) use ($escaped) {
                    $query->where('title', 'like', "%{$escaped}%")
                        ->orWhereHas('author', function ($authorQuery) use ($escaped) {
                            $authorQuery->where('name', 'like', "%{$escaped}%");
                        });
                })
                // Rank title matches above author-only matches.
                ->orderByRaw('CASE WHEN title LIKE ? THEN 0 ELSE 1 END', ["%{$escaped}%"])
                ->latest()
                ->paginate(10)
                ->withQueryString();
        }

        $posts->through(fn (BlogPost $post) => [
            'slug'            => $post->slug,
            'title'           => $post->title,
            'description'     => $post->description,
            'category'        => $post->category?->name,
            'author_name'     => $post->author?->name,
            'author_slug'     => $post->author?->slug,
            'cover_image_url' => $post->cover_image_url,
            'created_at'      => $post->created_at->format('M j, Y'),
            'views'           => $post->views,
            'likes'           => (int) $post->likes,
        ]);

        return Inertia::render('Blog/Search', [
            'posts' => $posts,
            'query' => $term,
        ]);
    }

    public function mostRead(): Response
    {
        $posts = BlogPost::with(['category', 'author'])
            ->where('publish_status', true)
            ->orderByDesc('views')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->through(fn (BlogPost $post) => [
                'slug'            => $post->slug,
                'title'           => $post->title,
                'description'     => $post->description,
                'category'        => $post->category?->name,
                'author_name'     => $post->author?->name,
                'author_slug'     => $post->author?->slug,
                'cover_image_url' => $post->cover_image_url,
                'created_at'      => $post->created_at->format('M j, Y'),
                'views'           => $post->views,
                'likes'           => (int) $post->likes,
            ]);

        $breadcrumbs = Breadcrumbs::make()
            ->push('Home', route('blog.index'))
            ->push('Most Read Stories', route('blog.most-read'))
            ->toArray();

        return Inertia::render('Blog/MostRead', [
            'posts'       => $posts,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function show(BlogPost $blogPost, Request $request, PostViewService $views, LikeService $likes): Response
    {
        abort_if(! $blogPost->publish_status, 404);

        // Count the visit (de-duplicated per visitor / window inside the service).
        $views->record($blogPost, $request);

        $pages = $this->splitIntoPages($blogPost->content);
        $totalPages = count($pages);
        $currentPage = max(1, min((int) $request->query('page', 1), $totalPages));

        $blogPost->loadMissing('tags');

        $breadcrumbs = Breadcrumbs::make()->push('Home', route('blog.index'));
        if ($blogPost->category) {
            $breadcrumbs->push(
                $blogPost->category->name,
                route('category.show', $blogPost->category->slug),
            );
        }
        // Current page: same URL as the canonical tag so the signals agree.
        $breadcrumbs->push($blogPost->title, route('blog.show', $blogPost->slug));

        return Inertia::render('Blog/Show', [
            'breadcrumbs' => $breadcrumbs->toArray(),
            'seriesParts' => $this->seriesParts($blogPost),
            ...$this->linkSections($blogPost),
            'post' => [
                'slug'            => $blogPost->slug,
                'title'           => $blogPost->title,
                'description'     => $blogPost->description,
                'category'        => $blogPost->category?->name,
                'author_name'     => $blogPost->author?->name,
                'author_slug'     => $blogPost->author?->slug,
                'cover_image_url' => $blogPost->cover_image_url,
                'content_html'    => (string) $this->md->convert($pages[$currentPage - 1]),
                'created_at'      => $blogPost->created_at->format('M j, Y'),
                'views'           => $blogPost->views,
                'likes'           => (int) $blogPost->likes,
                'liked'           => $likes->hasLiked($blogPost, $request),
                'tags'            => $blogPost->tags->map(fn ($t) => ['name' => $t->name, 'slug' => $t->slug]),
            ],
            'pagination' => $totalPages > 1 ? [
                'current_page'   => $currentPage,
                'last_page'      => $totalPages,
                'first_page_url' => route('blog.show', ['blogPost' => $blogPost->slug, 'page' => 1]),
                'prev_page_url'  => $currentPage > 1
                    ? route('blog.show', ['blogPost' => $blogPost->slug, 'page' => $currentPage - 1])
                    : null,
                'next_page_url'  => $currentPage < $totalPages
                    ? route('blog.show', ['blogPost' => $blogPost->slug, 'page' => $currentPage + 1])
                    : null,
            ] : null,
        ]);
    }

    /**
     * Four internal-link sections for the story page — Related, More by
     * this Author, More in this Category, Latest Stories — deduplicated
     * against each other via a shared running exclusion set, so the same
     * story never appears twice on one page. Combined they give readers
     * (and crawlers) 26 contextual internal links per page when the post
     * has both an author and a category (required fields, so true for all
     * normal posts).
     */
    private function linkSections(BlogPost $post): array
    {
        $excludeIds = [$post->id];

        $related = $this->pickPosts($this->relatedPostsQuery($post, $excludeIds), 8);
        $excludeIds = [...$excludeIds, ...$related->pluck('id')->all()];

        $byAuthor = $post->author_id
            ? $this->pickPosts(
                BlogPost::published()->whereNotIn('id', $excludeIds)
                    ->where('author_id', $post->author_id)
                    ->orderByDesc('created_at'),
                6,
            )
            : collect();
        $excludeIds = [...$excludeIds, ...$byAuthor->pluck('id')->all()];

        $inCategory = $post->category_id
            ? $this->pickPosts(
                BlogPost::published()->whereNotIn('id', $excludeIds)
                    ->where('category_id', $post->category_id)
                    ->orderByDesc('views')
                    ->orderByDesc('created_at'),
                6,
            )
            : collect();
        $excludeIds = [...$excludeIds, ...$inCategory->pluck('id')->all()];

        $latest = $this->pickPosts(
            BlogPost::published()->whereNotIn('id', $excludeIds)
                ->orderByDesc('created_at'),
            6,
        );

        return [
            'relatedPosts'   => $this->mapPostCards($related),
            'moreByAuthor'   => $this->mapPostCards($byAuthor),
            'moreInCategory' => $this->mapPostCards($inCategory),
            'latestStories'  => $this->mapPostCards($latest),
        ];
    }

    /**
     * Ranked by a weighted relevance score: shared-tag count (×3) + same
     * category (+2) + same author (+2). Ordered by that score, then
     * popularity, then recency — so once genuinely-related posts run out,
     * the remaining slots fill with the most-read stories. Never empty
     * (given enough other published posts to exclude only $excludeIds).
     */
    private function relatedPostsQuery(BlogPost $post, array $excludeIds): \Illuminate\Database\Eloquent\Builder
    {
        $tagIds = $post->tags->pluck('id')->all();

        $tagScore = $tagIds
            ? '(select count(*) from blog_post_tag'
                . ' where blog_post_tag.blog_post_id = blog_posts.id'
                . ' and blog_post_tag.tag_id in (' . implode(',', array_fill(0, count($tagIds), '?')) . ')) * 3'
            : '0';

        return BlogPost::query()
            ->published()
            ->whereNotIn('id', $excludeIds)
            ->select('blog_posts.*')
            ->selectRaw(
                "{$tagScore}"
                . ' + case when category_id = ? then 2 else 0 end'
                . ' + case when author_id = ? then 2 else 0 end as relevance',
                array_merge($tagIds, [$post->category_id, $post->author_id]),
            )
            ->orderByDesc('relevance')
            ->orderByDesc('views')
            ->orderByDesc('created_at');
    }

    private function pickPosts(\Illuminate\Database\Eloquent\Builder $query, int $limit): \Illuminate\Support\Collection
    {
        return $query->with(['category', 'author'])->limit($limit)->get();
    }

    private function mapPostCards(\Illuminate\Support\Collection $posts): \Illuminate\Support\Collection
    {
        return $posts->map(fn (BlogPost $p) => [
            'slug'            => $p->slug,
            'title'           => $p->title,
            'description'     => $p->description,
            'category'        => $p->category?->name,
            'author_name'     => $p->author?->name,
            'author_slug'     => $p->author?->slug,
            'cover_image_url' => $p->cover_image_url,
            'created_at'      => $p->created_at->format('M j, Y'),
            'views'           => $p->views,
            'likes'           => (int) $p->likes,
        ]);
    }

    /**
     * All parts of the multi-part story $post belongs to (including itself),
     * ordered by part number. Empty if the post isn't part of a series.
     */
    private function seriesParts(BlogPost $post): \Illuminate\Support\Collection
    {
        if (! $post->series_id) {
            return collect();
        }

        return BlogPost::where('series_id', $post->series_id)
            ->published()
            ->orderBy('part_number')
            ->get(['id', 'slug', 'title', 'part_number'])
            ->map(fn (BlogPost $p) => [
                'slug'        => $p->slug,
                'title'       => $p->title,
                'part_number' => $p->part_number,
                'is_current'  => (int) $p->id === (int) $post->id,
            ]);
    }

    private function splitIntoPages(string $markdown, int $wordsPerPage = 1000): array
    {
        if ($this->countWordsByWhiteSpaces($markdown) <= $wordsPerPage) {
            return [$markdown];
        }

        $lines = explode("\n", $markdown);
        $pages = [];
        $current = [];
        $count = 0;
        $inFence = false;

        foreach ($lines as $line) {
            if (preg_match('/^(`{3,}|~{3,})/', $line)) {
                $inFence = !$inFence;
            }

            $current[] = $line;

            if (!$inFence) {
                $count += $this->countWordsByWhiteSpaces($line);
            }

            // Break at a blank line once the page word budget is met
            if (!$inFence && $line === '' && $count >= $wordsPerPage) {
                $pages[] = trim(implode("\n", $current));
                $current = [];
                $count = 0;
            }
        }

        $remainder = trim(implode("\n", $current));
        if ($remainder !== '') {
            $pages[] = $remainder;
        }

        return $pages ?: [$markdown];
    }

    private function countWordsByWhiteSpaces($text)
    {
        $words = preg_split('/\s+/u', trim($text));
        return count(array_filter($words));
    }
}
