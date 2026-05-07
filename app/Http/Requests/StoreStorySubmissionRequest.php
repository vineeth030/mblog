<?php

namespace App\Http\Requests;

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
            'title'    => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255'],
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'pdf_file.required'  => 'Please upload your story as a PDF file.',
            'pdf_file.mimes'     => 'Only PDF files are allowed.',
            'pdf_file.mimetypes' => 'Only PDF files are allowed.',
            'pdf_file.max'       => 'The PDF must not be larger than 10 MB.',
        ];
    }
}
