<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

/**
 * Per-record authorization for courses. Administrators bypass every method via
 * the Gate::before hook, so these rules only decide what a lecturer may do:
 * act on their own courses.
 */
class CoursePolicy
{
    /**
     * Determine whether the user can update the course.
     */
    public function update(User $user, Course $course): bool
    {
        return $course->lecturer_id === $user->id;
    }

    /**
     * Determine whether the user can delete the course.
     */
    public function delete(User $user, Course $course): bool
    {
        return $course->lecturer_id === $user->id;
    }
}
