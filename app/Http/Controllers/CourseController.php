<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    /**
     * List the courses owned by the current lecturer.
     */
    public function index(Request $request): Response
    {
        $courses = $request->user()->courses()
            ->latest()
            ->get()
            ->map(fn (Course $course): array => [
                'id' => $course->id,
                'title' => $course->title,
                'status' => $course->status->value,
                'status_label' => $course->status->label(),
                'created_at' => $course->created_at?->toDateString(),
            ]);

        return Inertia::render('courses/Index', [
            'courses' => $courses,
        ]);
    }
}
