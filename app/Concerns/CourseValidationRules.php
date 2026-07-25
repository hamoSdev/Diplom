<?php

namespace App\Concerns;

use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait CourseValidationRules
{
    /**
     * Validation rules for a course created by an administrator: the subject
     * it belongs to and the lecturer who will own it.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function courseRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'subject_id' => ['required', Rule::exists('subjects', 'id')],
            'lecturer_id' => ['required', Rule::exists('users', 'id')->where('role', UserRole::Lecturer->value)],
        ];
    }
}
