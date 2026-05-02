<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('slug')) {
            $this->merge(['slug' => Str::slug($this->slug)]);
        }
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'slug'           => ['required', 'string', 'max:255', 'unique:blog_posts,slug'],
            'description'    => ['required', 'string', 'max:500'],
            'category_id'    => ['required', 'integer', 'exists:categories,id'],
            'author_id'      => ['required', 'integer', 'exists:authors,id'],
            'content'        => ['required', 'string'],
            'publish_status' => ['required', 'boolean'],
            'cover_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ];
    }
}
