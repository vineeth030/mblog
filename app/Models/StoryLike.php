<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'blog_post_id',
    'visitor_hash',
])]
class StoryLike extends Model
{
    public function blogPost(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class);
    }
}
