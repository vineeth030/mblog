<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'title',
    'slug',
    'excerpt',
    'content',
    'featured_image',
    'status',
    'is_featured',
    'published_at',
    'meta_title',
    'meta_description',
    'created_by',
])]
class EditorialPost extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_PUBLISHED = 'published';

    protected static function booted(): void
    {
        static::saving(function (EditorialPost $post) {
            if (empty($post->slug) && !empty($post->title)) {
                $post->slug = static::uniqueSlugFrom($post->title, $post->id);
            }
        });
    }

    public static function uniqueSlugFrom(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'editorial';
        $slug = $base;
        $i = 1;

        while (
            static::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . (++$i);
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    protected function casts(): array
    {
        return [
            'is_featured'  => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->featured_image ? Storage::url($this->featured_image) : null;
    }

    public function deleteFeaturedImage(): void
    {
        if ($this->featured_image) {
            Storage::delete($this->featured_image);
        }
    }
}
