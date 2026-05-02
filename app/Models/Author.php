<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'bio'])]
class Author extends Model
{
    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }
}
