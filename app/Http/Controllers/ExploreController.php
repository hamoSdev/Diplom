<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Read-only catalogue of every subject and course on the platform. Browsing is
 * open to any student; full reading and exams stay limited to their own faculty
 * (see LearningController), so a course is only linked when it is theirs.
 */
class ExploreController extends Controller
{
    public function index(Request $request): Response
    {
        $facultyId = $request->user()->faculty_id;

        $subjects = Subject::query()
            ->with(['faculty:id,name', 'courses' => fn ($query) => $query->with('lecturer:id,name')])
            ->orderBy('name')
            ->get()
            ->map(fn (Subject $subject): array => [
                'id' => $subject->id,
                'name' => $subject->name,
                'faculty_name' => $subject->faculty?->name,
                'is_mine' => $subject->faculty_id === $facultyId,
                'courses' => $subject->courses->map(fn (Course $course): array => [
                    'id' => $course->id,
                    'title' => $course->title,
                    'lecturer_name' => $course->lecturer->name,
                    'status' => $course->status->value,
                    'status_label' => $course->status->label(),
                ])->all(),
            ]);

        return Inertia::render('student/Explore', [
            'subjects' => $subjects,
        ]);
    }
}
