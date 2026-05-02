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

    public function show(BlogPost $blogPost, Request $request): Response
    {
        abort_if(! $blogPost->publish_status, 404);

        $pages = $this->splitIntoPages($blogPost->content);
        $totalPages = count($pages);
        $currentPage = max(1, min((int) $request->query('page', 1), $totalPages));

        return Inertia::render('Blog/Show', [
            'post' => [
                'slug'            => $blogPost->slug,
                'title'           => $blogPost->title,
                'description'     => $blogPost->description,
                'category'        => $blogPost->category,
                'author_name'     => $blogPost->author_name,
                'cover_image_url' => $blogPost->cover_image_url,
                'content_html'    => (string) $this->md->convert($pages[$currentPage - 1]),
                'created_at'      => $blogPost->created_at->format('M j, Y'),
            ],
            'pagination' => $totalPages > 1 ? [
                'current_page' => $currentPage,
                'total_pages'  => $totalPages,
            ] : null,
        ]);
    }

    private function splitIntoPages(string $markdown, int $wordsPerPage = 1000): array
    {
        if ($this->countWordsByWhiteSpaces($markdown) <= $wordsPerPage) {
            return [$markdown];
        }

        $lines = explode("\n", $markdown);
        $pages = [];
        $current = [];
        $count = 0;
        $inFence = false;

        foreach ($lines as $line) {
            if (preg_match('/^(`{3,}|~{3,})/', $line)) {
                $inFence = !$inFence;
            }

            $current[] = $line;

            if (!$inFence) {
                $count += $this->countWordsByWhiteSpaces($line);
            }

            // Break at a blank line once the page word budget is met
            if (!$inFence && $line === '' && $count >= $wordsPerPage) {
                $pages[] = trim(implode("\n", $current));
                $current = [];
                $count = 0;
            }
        }

        $remainder = trim(implode("\n", $current));
        if ($remainder !== '') {
            $pages[] = $remainder;
        }

        return $pages ?: [$markdown];
    }

    private function countWordsByWhiteSpaces($text)
    {
        $words = preg_split('/\s+/u', trim($text));
        return count(array_filter($words));
    }
}
