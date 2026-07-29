<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEditorCourseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * A lecturer may only save into a subject they are attached to; an admin
     * may save into any subject.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $subjectRule = $this->user()?->role === UserRole::Admin
            ? Rule::exists('subjects', 'id')
            : Rule::exists('subject_lecturer', 'subject_id')->where('user_id', $this->user()?->id);

        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'subject_id' => ['required', 'integer', $subjectRule],
        ];
    }
}
