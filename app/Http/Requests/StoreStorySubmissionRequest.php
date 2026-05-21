<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreStorySubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255'],
            'category_id'    => ['required', 'integer', 'exists:categories,id'],
            'tags'           => ['nullable', 'string', 'max:255'],
            'story_content'  => ['required', 'string', 'min:50'],
            'captcha_answer' => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required'    => 'Please select a category.',
            'category_id.exists'      => 'The selected category is invalid.',
            'tags.max'                => 'Tags must not exceed 255 characters.',
            'story_content.required'  => 'Please write your story before submitting.',
            'story_content.min'       => 'Your story is too short. Please write at least 50 characters.',
            'captcha_answer.required' => 'Please answer the math question.',
            'captcha_answer.integer'  => 'The answer must be a number.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $expected = $this->session()->get('captcha_sum');
            $answer   = $this->input('captcha_answer');

            if ($expected === null || (int) $answer !== (int) $expected) {
                $validator->errors()->add(
                    'captcha_answer',
                    'The answer is incorrect. Please try again.'
                );
            }
        });
    }
}
