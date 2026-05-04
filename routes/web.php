<?php

use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// ── Public blog ────────────────────────────────────────────
Route::get('/', [BlogController::class, 'index'])->name('blog.index');
Route::get('/post/{blogPost}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/tags/{tag}', [TagController::class, 'show'])->name('tag.show');

// ── Admin auth (guest only) ────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// ── Protected admin ────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    Route::get('dashboard', fn () => redirect()->route('admin.blog-posts.index'))->name('dashboard');

    Route::resource('blog-posts', BlogPostController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('categories', CategoryController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('authors', AuthorController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});
