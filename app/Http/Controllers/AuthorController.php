<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Inertia\Inertia;
use Inertia\Response;

class AuthorController extends Controller
{
    public function index(): Response
    {
        $authors = Author::query()
            ->withCount(['blogPosts as posts_count' => fn ($q) => $q->where('publish_status', true)])
            ->whereHas('blogPosts', fn ($q) => $q->where('publish_status', true))
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'bio'])
            ->map(fn ($author) => [
                'name'        => $author->name,
                'slug'        => $author->slug,
                'bio'         => $author->bio,
                'posts_count' => $author->posts_count,
            ]);

        return Inertia::render('Blog/Authors', [
            'authors' => $authors,
        ]);
    }

    public function show(Author $author): Response
    {
        $posts = $author->blogPosts()
            ->with(['category', 'author'])
            ->where('publish_status', true)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($post) => [
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

        return Inertia::render('Blog/Author', [
            'author' => ['name' => $author->name, 'slug' => $author->slug, 'bio' => $author->bio],
            'posts'  => $posts,
        ]);
    }
}
