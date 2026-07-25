<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Attach a few sample courses to the seeded lecturer.
     */
    public function run(): void
    {
        $lecturer = User::where('role', UserRole::Lecturer)->first();

        if ($lecturer === null) {
            return;
        }

        Course::factory()
            ->count(3)
            ->ready()
            ->for($lecturer, 'lecturer')
            ->create();
    }
}
