<?php

namespace App\Http\Requests;

use App\Models\EditorialPost;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateEditorialPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $slug = $this->input('slug');
        if (!filled($slug) && filled($this->input('title'))) {
            $current = $this->route('editorial_post');
            $ignoreId = $current?->id;
            $slug = EditorialPost::uniqueSlugFrom((string) $this->input('title'), $ignoreId);
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
        $current = $this->route('editorial_post');

        return [
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => [
                'required', 'string', 'max:255',
                Rule::unique('editorial_posts', 'slug')->ignore($current?->id),
            ],
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
