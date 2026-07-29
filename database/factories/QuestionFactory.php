<?php

namespace Database\Factories;

use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => QuestionType::Single,
            'difficulty' => fake()->randomElement(QuestionDifficulty::cases()),
            'category' => null,
            'body' => rtrim(fake()->sentence(), '.').'?',
            'explanation' => fake()->optional()->sentence(),
        ];
    }
}
