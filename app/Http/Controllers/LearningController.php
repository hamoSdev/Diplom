<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LearningController extends Controller
{
    /**
     * Show the student everything their faculty gives them access to:
     * the faculty's subjects and the courses taught under each one.
     */
    public function index(Request $request): Response
    {
        $faculty = $request->user()->faculty;

        $subjects = $faculty === null ? collect() : Subject::query()
            ->where('faculty_id', $faculty->id)
            ->with(['courses' => fn ($query) => $query->with('lecturer:id,name')])
            ->orderBy('name')
            ->get()
            ->map(fn (Subject $subject): array => [
                'id' => $subject->id,
                'name' => $subject->name,
                'courses' => $subject->courses->map(fn (Course $course): array => [
                    'id' => $course->id,
                    'title' => $course->title,
                    'lecturer_name' => $course->lecturer->name,
                    'status_label' => $course->status->label(),
                ]),
            ]);

        return Inertia::render('learning/Index', [
            'faculty' => $faculty?->only('id', 'name'),
            'subjects' => $subjects,
        ]);
    }
}
