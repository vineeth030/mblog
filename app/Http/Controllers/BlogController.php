<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
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
        ]);
    }

    public function index(Request $request): Response
    {
        $category = $request->query('category');

        $posts = BlogPost::where('publish_status', true)
            ->when($category, fn ($q) => $q->where('category', $category))
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (BlogPost $post) => [
                'slug'            => $post->slug,
                'title'           => $post->title,
                'description'     => $post->description,
                'category'        => $post->category,
                'author_name'     => $post->author_name,
                'cover_image_url' => $post->cover_image_url,
                'created_at'      => $post->created_at->format('M j, Y'),
            ]);

        $categories = BlogPost::where('publish_status', true)
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return Inertia::render('Blog/Index', [
            'posts'           => $posts,
            'categories'      => $categories,
            'currentCategory' => $category,
        ]);
    }

    public function show(BlogPost $blogPost): Response
    {
        abort_if(! $blogPost->publish_status, 404);

        return Inertia::render('Blog/Show', [
            'post' => [
                'id'              => $blogPost->id,
                'title'           => $blogPost->title,
                'description'     => $blogPost->description,
                'category'        => $blogPost->category,
                'author_name'     => $blogPost->author_name,
                'cover_image_url' => $blogPost->cover_image_url,
                'content_html'    => (string) $this->md->convert($blogPost->content),
                'created_at'      => $blogPost->created_at->format('M j, Y'),
            ],
        ]);
    }
}
