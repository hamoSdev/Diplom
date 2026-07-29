<?php

namespace Database\Factories;

use App\Enums\QuestionDifficulty;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamAnswer>
 */
class ExamAnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_attempt_id' => ExamAttempt::factory(),
            'question_id' => Question::factory(),
            'selected_option_id' => null,
            'is_correct' => fake()->boolean(),
            'difficulty_at_time' => fake()->randomElement(QuestionDifficulty::cases()),
            'position' => 1,
        ];
    }
}
