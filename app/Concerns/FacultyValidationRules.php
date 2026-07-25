<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait FacultyValidationRules
{
    /**
     * Validation rules for a faculty. Pass the current id on update so the
     * unique check ignores the row being edited.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function facultyRules(?int $ignoreId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('faculties', 'name')->ignore($ignoreId)],
        ];
    }
}
