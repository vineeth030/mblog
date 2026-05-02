<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Models\Author;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AuthorController extends Controller
{
    public function index(): Response
    {
        $authors = Author::withCount('blogPosts')->orderBy('name')->get();

        return Inertia::render('Admin/Authors/Index', [
            'authors' => $authors,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Authors/Create');
    }

    public function store(StoreAuthorRequest $request): RedirectResponse
    {
        Author::create($request->validated());

        return redirect()
            ->route('admin.authors.index')
            ->with('success', 'Author created.');
    }

    public function edit(Author $author): Response
    {
        return Inertia::render('Admin/Authors/Edit', [
            'author' => $author->only('id', 'name', 'bio'),
        ]);
    }

    public function update(UpdateAuthorRequest $request, Author $author): RedirectResponse
    {
        $author->update($request->validated());

        return redirect()
            ->route('admin.authors.index')
            ->with('success', 'Author updated.');
    }

    public function destroy(Author $author): RedirectResponse
    {
        if ($author->blogPosts()->exists()) {
            return back()->with('error', 'Cannot delete an author that has posts assigned to them.');
        }

        $author->delete();

        return redirect()
            ->route('admin.authors.index')
            ->with('success', 'Author deleted.');
    }
}
