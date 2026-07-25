<?php

namespace Database\Factories;

use App\Enums\CourseGenerationMode;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lecturer_id' => User::factory()->lecturer(),
            'title' => rtrim(fake()->unique()->sentence(3), '.'),
            'description' => fake()->optional()->paragraph(),
            'generation_mode' => fake()->randomElement(CourseGenerationMode::cases()),
            'status' => CourseStatus::Draft,
            'exam_length' => 20,
            'questions_per_topic' => 10,
        ];
    }

    /**
     * Indicate that the course content is fully generated and available.
     */
    public function ready(): static
    {
        return $this->state(fn (array $attributes) => ['status' => CourseStatus::Ready]);
    }
}
