<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubmitAnswerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'selected_option_ids' => ['nullable', 'array'],
            'selected_option_ids.*' => ['integer', 'exists:question_options,id'],
            'answer_text' => ['nullable', 'string'],
        ];
    }
}
