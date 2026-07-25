<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait LecturerCourseValidationRules
{
    /**
     * Validation rules for a course a lecturer manages. The subject must be one
     * the lecturer is attached to (a row in the subject_lecturer pivot).
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function lecturerCourseRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'subject_id' => [
                'required',
                Rule::exists('subject_lecturer', 'subject_id')->where('user_id', $this->user()?->id),
            ],
        ];
    }
}
