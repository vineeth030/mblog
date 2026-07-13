<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Support\Breadcrumbs;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function show(Category $category): Response
    {
        $posts = $category->blogPosts()
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
                'views'           => $post->views,
                'likes'           => (int) $post->likes,
            ]);

        $breadcrumbs = Breadcrumbs::make()
            ->push('Home', route('blog.index'))
            ->push($category->name, route('category.show', $category->slug))
            ->toArray();

        return Inertia::render('Blog/Category', [
            'category'    => ['name' => $category->name, 'slug' => $category->slug],
            'posts'       => $posts,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
