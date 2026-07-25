<?php

namespace App\Concerns;

use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait SubjectValidationRules
{
    /**
     * Validation rules for a subject, including the faculty it belongs to and
     * the lecturers attached to it.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function subjectRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'faculty_id' => ['required', Rule::exists('faculties', 'id')],
            'lecturer_ids' => ['nullable', 'array'],
            'lecturer_ids.*' => [Rule::exists('users', 'id')->where('role', UserRole::Lecturer->value)],
        ];
    }
}
