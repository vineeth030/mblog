<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Inertia\Inertia;
use Inertia\Response;

class AuthorController extends Controller
{
    public function show(Author $author): Response
    {
        $posts = $author->blogPosts()
            ->with(['category', 'author'])
            ->where('publish_status', true)
            ->latest()
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
            ]);

        return Inertia::render('Blog/Author', [
            'author' => ['name' => $author->name, 'slug' => $author->slug, 'bio' => $author->bio],
            'posts'  => $posts,
        ]);
    }
}
