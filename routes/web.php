<?php

use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\EditorialPostController;
use App\Http\Controllers\Admin\StorySubmissionController as AdminStorySubmissionController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AuthorController as PublicAuthorController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController as PublicCategoryController;
use App\Http\Controllers\EditorialController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StorySubmissionController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ── Public blog ────────────────────────────────────────────
Route::get('/', [BlogController::class, 'index'])->name('blog.index');
Route::get('/most-read-stories', [BlogController::class, 'mostRead'])->name('blog.most-read');
Route::get('/search', [BlogController::class, 'search'])->name('blog.search');
Route::get('/post/{blogPost}', [BlogController::class, 'show'])->name('blog.show');
// Anonymous like toggle. Rate limited to curb scripted abuse.
Route::post('/post/{blogPost}/like', [LikeController::class, 'toggle'])
    ->middleware('throttle:30,1')
    ->name('blog.like');
Route::get('/tags/{tag}', [TagController::class, 'show'])->name('tag.show');
Route::get('/category/{category:slug}', [PublicCategoryController::class, 'show'])->name('category.show');
Route::get('/authors', [PublicAuthorController::class, 'index'])->name('author.index');
Route::get('/authors/{author:slug}', [PublicAuthorController::class, 'show'])->name('author.show');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/post-sitemap.xml', [SitemapController::class, 'posts'])->name('sitemap.posts');
Route::get('/category-sitemap.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/author-sitemap.xml', [SitemapController::class, 'authors'])->name('sitemap.authors');
Route::get('/page-sitemap.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');

// ── Contact ────────────────────────────────────────────────
Route::get('/contact', fn () => Inertia::render('Contact', [
    'email' => 'kambikutan@gmail.com',
]))->name('contact');

// ── Legal ──────────────────────────────────────────────────
Route::get('/privacy-policy', fn () => Inertia::render('Privacy'))->name('privacy');
Route::get('/terms-and-conditions', fn () => Inertia::render('Terms'))->name('terms');

// ── Public editorial ───────────────────────────────────────
Route::get('/editorial', [EditorialController::class, 'index'])->name('editorial.index');
Route::get('/editorial/{editorialPost}', [EditorialController::class, 'show'])->name('editorial.show');

// ── Public story submission ────────────────────────────────
Route::get('/submit-story', [StorySubmissionController::class, 'create'])->name('stories.submit');
Route::post('/submit-story', [StorySubmissionController::class, 'store'])->name('stories.submit.store');

// ── Admin auth (guest only) ────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// ── Protected admin ────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    Route::get('dashboard', fn () => redirect()->route('admin.blog-posts.index'))->name('dashboard');

    Route::get('blog-posts/search-parts', [BlogPostController::class, 'searchParts'])->name('blog-posts.search-parts');
    Route::resource('blog-posts', BlogPostController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('editorial-posts', EditorialPostController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('categories', CategoryController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('authors', AuthorController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('story-submissions', AdminStorySubmissionController::class)
        ->only(['index', 'show', 'destroy']);
    Route::patch('story-submissions/{storySubmission}/status', [AdminStorySubmissionController::class, 'updateStatus'])
        ->name('story-submissions.status');
});
