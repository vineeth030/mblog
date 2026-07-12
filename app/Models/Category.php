<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class Category extends Model
{
    protected static function booted(): void
    {
        // Keep a unique slug in sync with the name unless one was set explicitly.
        static::saving(function (Category $category) {
            if ($category->slug && ! $category->isDirty('name')) {
                return;
            }

            $category->slug = $category->uniqueSlug($category->slugify($category->name));
        });
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    /**
     * Unicode-aware slug: keeps letters/numbers/marks from any script (so
     * Malayalam category names produce descriptive Malayalam slugs) while
     * lowercasing and hyphenating. Str::slug would strip non-ASCII entirely.
     */
    private function slugify(string $value): string
    {
        $slug = mb_strtolower(trim($value));
        $slug = preg_replace('/[\s_]+/u', '-', $slug);
        $slug = preg_replace('/[^\p{L}\p{N}\p{M}-]+/u', '', $slug);
        $slug = preg_replace('/-+/', '-', $slug);

        return trim($slug, '-') ?: 'category';
    }

    private function uniqueSlug(string $base): string
    {
        $slug = $base;
        $n = 2;

        while (
            static::where('slug', $slug)
                ->when($this->exists, fn ($q) => $q->whereKeyNot($this->getKey()))
                ->exists()
        ) {
            $slug = "{$base}-{$n}";
            $n++;
        }

        return $slug;
    }
}
