<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogPostRequest;
use App\Http\Requests\UpdateBlogPostRequest;
use App\Models\Author;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlogPostController extends Controller
{
    public function index(): Response
    {
        $posts = BlogPost::with(['category', 'author'])
            ->orderByDesc('id')
            ->paginate(10)
            ->through(fn (BlogPost $post) => [
                'slug'            => $post->slug,
                'title'           => $post->title,
                'category'        => $post->category?->name,
                'author_name'     => $post->author?->name,
                'publish_status'  => $post->publish_status,
                'cover_image_url' => $post->cover_image_url,
                'created_at'      => $post->created_at->toDateString(),
            ]);

        return Inertia::render('Admin/BlogPosts/Index', [
            'posts' => $posts,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/BlogPosts/Create', [
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'authors'    => Author::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreBlogPostRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $tagNames = $data['tags'] ?? [];
        $previousPartId = isset($data['previous_part_id']) ? (int) $data['previous_part_id'] : null;
        unset($data['tags'], $data['previous_part_id']);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')
                ->store('blog-covers', 'public');
        }

        DB::transaction(function () use ($data, $tagNames, $previousPartId) {
            $post = BlogPost::create($data);
            $post->tags()->sync($this->resolveTagIds($tagNames));
            $this->linkToPreviousPart($post, $previousPartId);
        });

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Post created successfully.');
    }

    public function edit(BlogPost $blogPost): Response
    {
        $blogPost->loadMissing('tags');

        $previousPart = $this->findPreviousPart($blogPost);

        return Inertia::render('Admin/BlogPosts/Edit', [
            'post' => [
                'id'                  => $blogPost->id,
                'slug'                => $blogPost->slug,
                'title'               => $blogPost->title,
                'description'         => $blogPost->description,
                'category_id'         => $blogPost->category_id,
                'author_id'           => $blogPost->author_id,
                'content'             => $blogPost->content,
                'publish_status'      => $blogPost->publish_status,
                'cover_image_url'     => $blogPost->cover_image_url,
                'tags'                => $blogPost->tags->pluck('name'),
                'previous_part_id'    => $previousPart?->id,
                'previous_part_title' => $previousPart?->title,
            ],
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'authors'    => Author::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $data = $request->validated();
        $tagNames = $data['tags'] ?? [];
        $previousPartId = isset($data['previous_part_id']) ? (int) $data['previous_part_id'] : null;
        unset($data['tags'], $data['previous_part_id']);

        if ($request->hasFile('cover_image')) {
            $blogPost->deleteCoverImage();
            $data['cover_image'] = $request->file('cover_image')
                ->store('blog-covers', 'public');
        } else {
            // Keep the existing image — don't overwrite with null
            unset($data['cover_image']);
        }

        DB::transaction(function () use ($blogPost, $data, $tagNames, $previousPartId) {
            $previousPart = $this->findPreviousPart($blogPost);
            $currentPreviousId = $previousPart ? (int) $previousPart->id : null;

            $blogPost->update($data);
            $blogPost->tags()->sync($this->resolveTagIds($tagNames));

            if ($currentPreviousId !== $previousPartId) {
                $this->linkToPreviousPart($blogPost, $previousPartId);
            }
        });

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Lightweight title search used by the admin "previous part" picker.
     */
    public function searchParts(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));
        $excludeId = $request->integer('exclude') ?: null;

        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $escaped = addcslashes($term, '%_\\');

        $matches = BlogPost::query()
            ->where('title', 'like', "%{$escaped}%")
            ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))
            ->orderByDesc('id')
            ->limit(10)
            ->get(['id', 'title', 'slug']);

        return response()->json($matches);
    }

    /**
     * The part immediately before this one in its series, or null if this
     * post isn't part of a series (or is the first part).
     */
    private function findPreviousPart(BlogPost $post): ?BlogPost
    {
        if (! $post->series_id || ! $post->part_number) {
            return null;
        }

        return BlogPost::where('series_id', $post->series_id)
            ->where('part_number', $post->part_number - 1)
            ->first(['id', 'title']);
    }

    /**
     * Attach (or detach) $post as the newest part after $previousPartId.
     * Retroactively turns a standalone $previousPartId post into a series
     * root the first time it gains a successor.
     *
     * A series root can never be detached through this method: a post only
     * becomes a root retroactively (when something else links to it), so its
     * own "previous part" field is always empty by definition — there's no
     * way to tell "still root, unchanged" apart from "detach the root" using
     * that field alone. Only non-root members (which do have a real previous
     * part) can be detached here, which also keeps every other part's
     * series_id intact.
     */
    private function linkToPreviousPart(BlogPost $post, ?int $previousPartId): void
    {
        if (! $previousPartId) {
            if ($post->series_id && (int) $post->series_id !== (int) $post->id) {
                // series_id/part_number are deliberately not mass-assignable
                // (kept out of the model's Fillable list), so set them directly.
                $post->series_id = null;
                $post->part_number = null;
                $post->save();
            }

            return;
        }

        $previous = BlogPost::findOrFail($previousPartId);

        if (! $previous->series_id) {
            $previous->series_id = $previous->id;
            $previous->part_number = $previous->part_number ?? 1;
            $previous->save();
        }

        $nextPartNumber = BlogPost::where('series_id', $previous->series_id)->max('part_number') + 1;

        $post->series_id = $previous->series_id;
        $post->part_number = $nextPartNumber;
        $post->save();
    }

    private function resolveTagIds(array $tagNames): array
    {
        return collect($tagNames)
            ->map(fn (string $name) => trim(mb_strtolower($name)))
            ->filter()
            ->unique()
            ->map(function (string $name) {
                $slug = Str::slug($name);
                return Tag::firstOrCreate(['slug' => $slug], ['name' => $name])->id;
            })
            ->values()
            ->all();
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->deleteCoverImage();
        $blogPost->delete();

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}
