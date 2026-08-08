<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Subject;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Read-only directory of lecturers for students to browse who teaches what.
 */
class LecturerDirectoryController extends Controller
{
    public function index(): Response
    {
        $lecturers = User::query()
            ->where('role', UserRole::Lecturer)
            ->with('taughtSubjects:id,name')
            ->withCount('courses')
            ->orderBy('name')
            ->get()
            ->map(fn (User $lecturer): array => [
                'id' => $lecturer->id,
                'name' => $lecturer->name,
                'subjects' => $lecturer->taughtSubjects->map(fn (Subject $subject): string => $subject->name),
                'courses_count' => $lecturer->courses_count,
            ]);

        return Inertia::render('student/Lecturers', [
            'lecturers' => $lecturers,
        ]);
    }
}
