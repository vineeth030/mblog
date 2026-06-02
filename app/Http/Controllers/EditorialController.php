<?php

namespace App\Http\Controllers;

use App\Models\EditorialPost;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use League\CommonMark\CommonMarkConverter;

class EditorialController extends Controller
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

    public function index(Request $request): Response
    {
        $posts = EditorialPost::published()
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (EditorialPost $post) => [
                'slug'              => $post->slug,
                'title'             => $post->title,
                'excerpt'           => $post->excerpt,
                'featured_image_url' => $post->featured_image_url,
                'is_featured'       => $post->is_featured,
                'published_at'      => ($post->published_at ?? $post->created_at)->format('M j, Y'),
            ]);

        return Inertia::render('Editorial/Index', [
            'posts' => $posts,
        ]);
    }

    public function show(EditorialPost $editorialPost): Response
    {
        abort_if($editorialPost->status !== EditorialPost::STATUS_PUBLISHED, 404);

        if ($editorialPost->published_at && $editorialPost->published_at->isFuture()) {
            abort(404);
        }

        $related = EditorialPost::published()
            ->where('id', '!=', $editorialPost->id)
            ->latest('published_at')
            ->limit(3)
            ->get()
            ->map(fn (EditorialPost $p) => [
                'slug'               => $p->slug,
                'title'              => $p->title,
                'excerpt'            => $p->excerpt,
                'featured_image_url' => $p->featured_image_url,
                'published_at'       => ($p->published_at ?? $p->created_at)->format('M j, Y'),
            ]);

        return Inertia::render('Editorial/Show', [
            'post' => [
                'slug'               => $editorialPost->slug,
                'title'              => $editorialPost->title,
                'excerpt'            => $editorialPost->excerpt,
                'featured_image_url' => $editorialPost->featured_image_url,
                'content_html'       => (string) $this->md->convert($editorialPost->content),
                'is_featured'        => $editorialPost->is_featured,
                'published_at'       => ($editorialPost->published_at ?? $editorialPost->created_at)->format('M j, Y'),
                'meta_title'         => $editorialPost->meta_title,
                'meta_description'   => $editorialPost->meta_description,
            ],
            'related' => $related,
        ]);
    }
}
