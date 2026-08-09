<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateBlogPostRequest extends FormRequest
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
            'slug'           => ['required', 'string', 'max:255', Rule::unique('blog_posts', 'slug')->ignore($this->route('blog_post'))],
            'description'    => ['nullable', 'string', 'max:500'],
            'category_id'    => ['required', 'integer', 'exists:categories,id'],
            'author_id'      => ['required', 'integer', 'exists:authors,id'],
            'content'        => ['required', 'string'],
            'publish_status' => ['required', 'boolean'],
            // Optional on update — only validated when a new file is uploaded
            'cover_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
            'tags'           => ['nullable', 'array'],
            'tags.*'         => ['string', 'max:50'],
            'previous_part_id' => [
                'nullable', 'integer', 'exists:blog_posts,id',
                function (string $attribute, $value, \Closure $fail): void {
                    if ((int) $value === (int) $this->route('blog_post')?->id) {
                        $fail('A story cannot be linked to itself.');
                    }
                },
            ],
        ];
    }
}
