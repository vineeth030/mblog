<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'bio'])]
class Author extends Model
{
    protected static function booted(): void
    {
        // Keep a unique slug in sync with the name unless one was set explicitly.
        static::saving(function (Author $author) {
            if ($author->slug && ! $author->isDirty('name')) {
                return;
            }

            $author->slug = $author->uniqueSlug(Str::slug($author->name) ?: 'author');
        });
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
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
