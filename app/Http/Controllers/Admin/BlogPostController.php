<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogPostRequest;
use App\Http\Requests\UpdateBlogPostRequest;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BlogPostController extends Controller
{
    public function index(): Response
    {
        $posts = BlogPost::latest()
            ->paginate(10)
            ->through(fn (BlogPost $post) => [
                'slug'           => $post->slug,
                'title'          => $post->title,
                'category'       => $post->category,
                'author_name'    => $post->author_name,
                'publish_status' => $post->publish_status,
                'cover_image_url'=> $post->cover_image_url,
                'created_at'     => $post->created_at->toDateString(),
            ]);

        return Inertia::render('Admin/BlogPosts/Index', [
            'posts' => $posts,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/BlogPosts/Create');
    }

    public function store(StoreBlogPostRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')
                ->store('blog-covers', 'public');
        }

        BlogPost::create($data);

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Post created successfully.');
    }

    public function edit(BlogPost $blogPost): Response
    {
        return Inertia::render('Admin/BlogPosts/Edit', [
            'post' => [
                'slug'           => $blogPost->slug,
                'title'          => $blogPost->title,
                'description'    => $blogPost->description,
                'category'       => $blogPost->category,
                'author_name'    => $blogPost->author_name,
                'content'        => $blogPost->content,
                'publish_status' => $blogPost->publish_status,
                'cover_image_url'=> $blogPost->cover_image_url,
            ],
        ]);
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $blogPost->deleteCoverImage();
            $data['cover_image'] = $request->file('cover_image')
                ->store('blog-covers', 'public');
        } else {
            // Keep the existing image — don't overwrite with null
            unset($data['cover_image']);
        }

        $blogPost->update($data);

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Post updated successfully.');
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
