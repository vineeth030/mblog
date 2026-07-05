<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogPostRequest;
use App\Http\Requests\UpdateBlogPostRequest;
use App\Models\Author;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
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
        unset($data['tags']);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')
                ->store('blog-covers', 'public');
        }

        $post = BlogPost::create($data);
        $post->tags()->sync($this->resolveTagIds($tagNames));

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Post created successfully.');
    }

    public function edit(BlogPost $blogPost): Response
    {
        $blogPost->loadMissing('tags');

        return Inertia::render('Admin/BlogPosts/Edit', [
            'post' => [
                'slug'            => $blogPost->slug,
                'title'           => $blogPost->title,
                'description'     => $blogPost->description,
                'category_id'     => $blogPost->category_id,
                'author_id'       => $blogPost->author_id,
                'content'         => $blogPost->content,
                'publish_status'  => $blogPost->publish_status,
                'cover_image_url' => $blogPost->cover_image_url,
                'tags'            => $blogPost->tags->pluck('name'),
            ],
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'authors'    => Author::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $data = $request->validated();
        $tagNames = $data['tags'] ?? [];
        unset($data['tags']);

        if ($request->hasFile('cover_image')) {
            $blogPost->deleteCoverImage();
            $data['cover_image'] = $request->file('cover_image')
                ->store('blog-covers', 'public');
        } else {
            // Keep the existing image — don't overwrite with null
            unset($data['cover_image']);
        }

        $blogPost->update($data);
        $blogPost->tags()->sync($this->resolveTagIds($tagNames));

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Post updated successfully.');
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
