<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    public function show(Tag $tag): Response
    {
        $posts = $tag->blogPosts()
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
                'cover_image_url' => $post->cover_image_url,
                'created_at'      => $post->created_at->format('M j, Y'),
            ]);

        return Inertia::render('Blog/Tag', [
            'tag'   => ['name' => $tag->name, 'slug' => $tag->slug],
            'posts' => $posts,
        ]);
    }
}
