<?php

namespace Database\Factories;

use App\Models\Faculty;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'faculty_id' => Faculty::factory(),
            'name' => rtrim(fake()->unique()->sentence(2), '.'),
            'description' => fake()->optional()->paragraph(),
        ];
    }
}
