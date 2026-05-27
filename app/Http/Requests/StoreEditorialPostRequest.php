<?php

namespace App\Http\Requests;

use App\Models\EditorialPost;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreEditorialPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Auto-generate slug from title if blank
        $slug = $this->input('slug');
        if (!filled($slug) && filled($this->input('title'))) {
            $slug = EditorialPost::uniqueSlugFrom((string) $this->input('title'));
        } elseif (filled($slug)) {
            $slug = Str::slug((string) $slug);
        }

        $this->merge([
            'slug'        => $slug,
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['required', 'string', 'max:255', 'unique:editorial_posts,slug'],
            'excerpt'          => ['nullable', 'string', 'max:500'],
            'content'          => ['required', 'string'],
            'featured_image'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
            'status'           => ['required', 'in:draft,published'],
            'is_featured'      => ['required', 'boolean'],
            'published_at'     => ['nullable', 'date'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
