<?php

namespace App\Http\Requests\Admin;

use App\Concerns\FacultyValidationRules;
use App\Models\Faculty;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFacultyRequest extends FormRequest
{
    use FacultyValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $faculty = $this->route('faculty');

        return $this->facultyRules($faculty instanceof Faculty ? $faculty->id : null);
    }
}
