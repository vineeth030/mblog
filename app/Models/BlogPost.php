<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'title',
    'description',
    'category',
    'author_name',
    'content',
    'publish_status',
    'cover_image',
])]
class BlogPost extends Model
{
    protected function casts(): array
    {
        return [
            'publish_status' => 'boolean',
        ];
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
