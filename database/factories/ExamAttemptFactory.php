<?php

namespace Database\Factories;

use App\Enums\ExamStartLevel;
use App\Enums\ExamStatus;
use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamAttempt>
 */
class ExamAttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => User::factory()->student(),
            'status' => ExamStatus::InProgress,
            'start_level' => ExamStartLevel::Intermediate,
            'ability' => 0,
            'started_at' => now(),
        ];
    }
}
