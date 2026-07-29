<?php

namespace App\Http\Requests;

use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\Course;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request. The topic must belong
     * to the course from the route; a question needs at least two options.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $course = $this->route('course');

        return [
            'topic_id' => [
                'required',
                Rule::exists('topics', 'id')->where('course_id', $course instanceof Course ? $course->id : null),
            ],
            'type' => ['required', Rule::enum(QuestionType::class)],
            'difficulty' => ['required', Rule::enum(QuestionDifficulty::class)],
            'body' => ['required', 'string'],
            'explanation' => ['nullable', 'string'],
            'options' => ['required', 'array', 'min:2', 'max:6'],
            'options.*.body' => ['required', 'string', 'max:255'],
            'options.*.is_correct' => ['required', 'boolean'],
        ];
    }

    /**
     * Exactly one option must be the correct one.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $options = $this->input('options');
            $correct = 0;

            if (is_array($options)) {
                foreach ($options as $option) {
                    if (is_array($option) && ! empty($option['is_correct'])) {
                        $correct++;
                    }
                }
            }

            if ($correct !== 1) {
                $validator->errors()->add('options', __('Exactly one option must be marked correct.'));
            }
        });
    }
}
