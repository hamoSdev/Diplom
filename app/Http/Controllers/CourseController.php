<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    /**
     * List the current lecturer's courses and the subjects they may create for.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $courses = $user->courses()
            ->with('subject:id,name')
            ->latest()
            ->get()
            ->map(fn (Course $course): array => [
                'id' => $course->id,
                'title' => $course->title,
                'subject_id' => $course->subject_id,
                'subject_name' => $course->subject?->name,
                'status' => $course->status->value,
                'status_label' => $course->status->label(),
            ]);

        return Inertia::render('courses/Index', [
            'courses' => $courses,
            'subjects' => $user->taughtSubjects()->orderBy('name')->get(['subjects.id', 'subjects.name']),
        ]);
    }

    /**
     * Create a course owned by the current lecturer.
     */
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $request->user()->courses()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Course created.')]);

        return to_route('courses.index');
    }

    /**
     * Update one of the lecturer's own courses.
     */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $course->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Course updated.')]);

        return to_route('courses.index');
    }

    /**
     * Delete one of the lecturer's own courses.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $this->authorize('delete', $course);

        $course->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Course deleted.')]);

        return to_route('courses.index');
    }
}
