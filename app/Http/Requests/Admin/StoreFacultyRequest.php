<?php

namespace App\Http\Requests\Admin;

use App\Concerns\FacultyValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFacultyRequest extends FormRequest
{
    use FacultyValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->facultyRules();
    }
}
