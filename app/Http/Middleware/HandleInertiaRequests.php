<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Services\PostViewService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            'publicCategories' => fn () => $request->is('admin*')
                ? []
                : Category::query()
                    ->whereHas('blogPosts', fn ($q) => $q->where('publish_status', true))
                    ->withCount(['blogPosts' => fn ($q) => $q->where('publish_status', true)])
                    ->orderBy('name')
                    ->get()
                    ->map(fn (Category $c) => [
                        'name'  => $c->name,
                        'count' => $c->blog_posts_count,
                    ]),
            'mostReadStories' => fn () => $request->is('admin*')
                ? []
                : app(PostViewService::class)->topRead(),
        ];
    }
}
