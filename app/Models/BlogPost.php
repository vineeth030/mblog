<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'title',
    'slug',
    'description',
    'category_id',
    'author_id',
    'content',
    'publish_status',
    'cover_image',
])]
class BlogPost extends Model
{
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /** Individual anonymous "like" rows; the aggregate lives on the `likes` column. */
    public function storyLikes(): HasMany
    {
        return $this->hasMany(StoryLike::class);
    }

    protected function casts(): array
    {
        return [
            'publish_status' => 'boolean',
            'views'          => 'integer',
            'likes'          => 'integer',
        ];
    }

    /**
     * Posts that are live: published and not dated in the future. Used by the
     * listings and the "Most Read" widget so neither drafts nor
     * future-dated posts ever leak out.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('publish_status', true)
            ->where('created_at', '<=', now());
    }

    /** Full public URL for the cover image, or null. */
    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image ? Storage::url($this->cover_image) : null;
    }

    /** Delete the stored cover image file from disk. */
    public function deleteCoverImage(): void
    {
        if ($this->cover_image) {
            Storage::delete($this->cover_image);
        }
    }
}
