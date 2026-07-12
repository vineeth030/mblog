<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    /** Sitemap index — points at the per-type child sitemaps. */
    public function index(): Response
    {
        $sitemaps = [
            ['loc' => route('sitemap.posts'),      'lastmod' => $this->maxPublishedPostDate()],
            ['loc' => route('sitemap.categories'), 'lastmod' => $this->maxPublishedPostDate()],
            ['loc' => route('sitemap.authors'),    'lastmod' => $this->carbon(
                Author::whereHas('blogPosts', fn ($q) => $q->where('publish_status', true))->max('updated_at')
            )],
            ['loc' => route('sitemap.pages'),      'lastmod' => now()],
        ];

        return $this->xml('sitemap.index', ['sitemaps' => $sitemaps]);
    }

    /** Published blog posts. */
    public function posts(): Response
    {
        $urls = BlogPost::query()
            ->where('publish_status', true)
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at'])
            ->map(fn (BlogPost $post) => [
                'loc'        => route('blog.show', $post->slug),
                'lastmod'    => $post->updated_at,
                'changefreq' => 'weekly',
                'priority'   => '0.8',
            ])
            ->all();

        return $this->xml('sitemap.urlset', ['urls' => $urls]);
    }

    /** Categories that have at least one published post. */
    public function categories(): Response
    {
        $urls = Category::query()
            ->whereHas('blogPosts', fn ($q) => $q->where('publish_status', true))
            ->withMax(['blogPosts as last_post_at' => fn ($q) => $q->where('publish_status', true)], 'updated_at')
            ->orderBy('name')
            ->get(['id', 'slug'])
            ->map(fn (Category $category) => [
                'loc'        => route('category.show', $category->slug),
                'lastmod'    => $this->carbon($category->last_post_at),
                'changefreq' => 'weekly',
                'priority'   => '0.7',
            ])
            ->all();

        return $this->xml('sitemap.urlset', ['urls' => $urls]);
    }

    /** Authors that have at least one published post. */
    public function authors(): Response
    {
        $urls = Author::query()
            ->whereHas('blogPosts', fn ($q) => $q->where('publish_status', true))
            ->orderBy('name')
            ->get(['slug', 'updated_at'])
            ->map(fn (Author $author) => [
                'loc'        => route('author.show', $author->slug),
                'lastmod'    => $author->updated_at,
                'changefreq' => 'weekly',
                'priority'   => '0.6',
            ])
            ->all();

        return $this->xml('sitemap.urlset', ['urls' => $urls]);
    }

    /** Static/marketing pages. */
    public function pages(): Response
    {
        $urls = [
            ['loc' => route('blog.index'),     'lastmod' => now(), 'changefreq' => 'daily',  'priority' => '1.0'],
            ['loc' => route('blog.most-read'), 'lastmod' => now(), 'changefreq' => 'daily',  'priority' => '0.7'],
            ['loc' => route('author.index'),   'lastmod' => now(), 'changefreq' => 'weekly', 'priority' => '0.6'],
            ['loc' => route('privacy'),        'lastmod' => null,  'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('terms'),          'lastmod' => null,  'changefreq' => 'yearly', 'priority' => '0.3'],
        ];

        return $this->xml('sitemap.urlset', ['urls' => $urls]);
    }

    private function maxPublishedPostDate(): ?Carbon
    {
        return $this->carbon(BlogPost::where('publish_status', true)->max('updated_at'));
    }

    private function carbon(mixed $value): ?Carbon
    {
        return $value ? Carbon::parse($value) : null;
    }

    private function xml(string $view, array $data): Response
    {
        return response(view($view, $data)->render(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
