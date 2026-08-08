<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseMaterial;
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
                    'description' => $course->description,
                    'lecturer_name' => $course->lecturer->name,
                    'status' => $course->status->value,
                    'status_label' => $course->status->label(),
                ])->all(),
            ]);

        return Inertia::render('learning/Index', [
            'faculty' => $faculty?->only('id', 'name'),
            'subjects' => $subjects,
        ]);
    }

    /**
     * Read a single course. Accessible only if the course belongs to a subject
     * of the student's faculty; its HTML body is sanitized before display.
     */
    public function show(Request $request, Course $course): Response
    {
        $course->load('subject:id,name,faculty_id', 'lecturer:id,name', 'materials');

        abort_unless(
            $course->subject !== null
                && $course->subject->faculty_id === $request->user()->faculty_id,
            403,
        );

        return Inertia::render('learning/CourseShow', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'subject_name' => $course->subject->name,
                'lecturer_name' => $course->lecturer->name,
                'status' => $course->status->value,
                'status_label' => $course->status->label(),
                'content' => $course->content === null ? null : clean($course->content, 'course'),
                'has_exam' => $course->questions()->exists(),
                'materials' => $course->materials->map(fn (CourseMaterial $material): array => [
                    'id' => $material->id,
                    'original_name' => $material->original_name,
                    'size' => $material->size,
                ]),
            ],
        ]);
    }
}
