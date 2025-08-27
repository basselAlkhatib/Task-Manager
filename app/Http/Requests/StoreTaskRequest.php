<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:high,medium,low'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'A title is required to create a task.'
        ];
    }
}
