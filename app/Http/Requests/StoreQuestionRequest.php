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
        $isOpen = $this->input('type') === QuestionType::Open->value;

        return [
            'topic_id' => [
                'required',
                Rule::exists('topics', 'id')->where('course_id', $course instanceof Course ? $course->id : null),
            ],
            'type' => ['required', Rule::enum(QuestionType::class)],
            'difficulty' => ['required', Rule::enum(QuestionDifficulty::class)],
            'body' => ['required', 'string'],
            'explanation' => ['nullable', 'string'],
            'expected_answer' => [$isOpen ? 'nullable' : 'prohibited', 'string'],
            'options' => [$isOpen ? 'prohibited' : 'required', 'array', 'max:6'],
            'options.*.body' => ['required_with:options', 'string', 'max:255'],
            'options.*.is_correct' => ['required_with:options', 'boolean'],
        ];
    }

    /**
     * Option questions need at least two options and the right number of correct
     * ones: exactly one for single / true-false, at least one for multiple.
     * Open questions carry no options.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->input('type') === QuestionType::Open->value) {
                return;
            }

            $options = $this->input('options');
            $options = is_array($options) ? $options : [];

            if (count($options) < 2) {
                $validator->errors()->add('options', __('Provide at least two options.'));

                return;
            }

            $correct = 0;

            foreach ($options as $option) {
                if (is_array($option) && ! empty($option['is_correct'])) {
                    $correct++;
                }
            }

            $multiple = $this->input('type') === QuestionType::Multiple->value;

            if ($multiple && $correct < 1) {
                $validator->errors()->add('options', __('Mark at least one option correct.'));
            } elseif (! $multiple && $correct !== 1) {
                $validator->errors()->add('options', __('Exactly one option must be marked correct.'));
            }
        });
    }
}
