<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEditorialPostRequest;
use App\Http\Requests\UpdateEditorialPostRequest;
use App\Models\EditorialPost;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EditorialPostController extends Controller
{
    public function index(): Response
    {
        $posts = EditorialPost::latest()
            ->paginate(10)
            ->through(fn (EditorialPost $post) => [
                'slug'               => $post->slug,
                'title'              => $post->title,
                'status'             => $post->status,
                'is_featured'        => $post->is_featured,
                'featured_image_url' => $post->featured_image_url,
                'published_at'       => $post->published_at?->format('M j, Y'),
                'created_at'         => $post->created_at->toDateString(),
            ]);

        return Inertia::render('Admin/EditorialPosts/Index', [
            'posts' => $posts,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/EditorialPosts/Create');
    }

    public function store(StoreEditorialPostRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')
                ->store('editorial-covers', 'public');
        }

        if ($data['status'] === EditorialPost::STATUS_PUBLISHED && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $data['created_by'] = $request->session()->get('admin_user_id');

        EditorialPost::create($data);

        return redirect()
            ->route('admin.editorial-posts.index')
            ->with('success', 'Editorial created successfully.');
    }

    public function edit(EditorialPost $editorialPost): Response
    {
        return Inertia::render('Admin/EditorialPosts/Edit', [
            'post' => [
                'slug'               => $editorialPost->slug,
                'title'              => $editorialPost->title,
                'excerpt'            => $editorialPost->excerpt,
                'content'            => $editorialPost->content,
                'status'             => $editorialPost->status,
                'is_featured'        => $editorialPost->is_featured,
                'published_at'       => $editorialPost->published_at?->format('Y-m-d\TH:i'),
                'meta_title'         => $editorialPost->meta_title,
                'meta_description'   => $editorialPost->meta_description,
                'featured_image_url' => $editorialPost->featured_image_url,
            ],
        ]);
    }

    public function update(UpdateEditorialPostRequest $request, EditorialPost $editorialPost): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('featured_image')) {
            $editorialPost->deleteFeaturedImage();
            $data['featured_image'] = $request->file('featured_image')
                ->store('editorial-covers', 'public');
        } else {
            unset($data['featured_image']);
        }

        if (
            $data['status'] === EditorialPost::STATUS_PUBLISHED
            && empty($data['published_at'])
            && empty($editorialPost->published_at)
        ) {
            $data['published_at'] = now();
        }

        $editorialPost->update($data);

        return redirect()
            ->route('admin.editorial-posts.index')
            ->with('success', 'Editorial updated successfully.');
    }

    public function destroy(EditorialPost $editorialPost): RedirectResponse
    {
        $editorialPost->deleteFeaturedImage();
        $editorialPost->delete();

        return redirect()
            ->route('admin.editorial-posts.index')
            ->with('success', 'Editorial deleted successfully.');
    }
}
