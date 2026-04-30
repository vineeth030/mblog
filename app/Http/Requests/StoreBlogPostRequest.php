<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['required', 'string', 'max:500'],
            'category'       => ['required', 'string', 'max:100'],
            'author_name'    => ['required', 'string', 'max:100'],
            'content'        => ['required', 'string'],
            'publish_status' => ['required', 'boolean'],
            'cover_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ];
    }
}
