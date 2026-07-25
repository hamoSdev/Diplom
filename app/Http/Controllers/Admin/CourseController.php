<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    /**
     * List courses with the subjects and lecturers the forms need.
     */
    public function index(): Response
    {
        $courses = Course::query()
            ->with(['subject:id,name', 'lecturer:id,name'])
            ->latest()
            ->get()
            ->map(fn (Course $course): array => [
                'id' => $course->id,
                'title' => $course->title,
                'subject_id' => $course->subject_id,
                'subject_name' => $course->subject?->name,
                'lecturer_id' => $course->lecturer_id,
                'lecturer_name' => $course->lecturer->name,
                'status' => $course->status->value,
                'status_label' => $course->status->label(),
            ]);

        return Inertia::render('admin/Courses', [
            'courses' => $courses,
            'subjects' => Subject::query()->orderBy('name')->get(['id', 'name']),
            'lecturers' => User::query()
                ->where('role', UserRole::Lecturer)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Create a course.
     */
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        Course::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Course created.')]);

        return to_route('admin.courses.index');
    }

    /**
     * Update a course.
     */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Course updated.')]);

        return to_route('admin.courses.index');
    }

    /**
     * Delete a course.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Course deleted.')]);

        return to_route('admin.courses.index');
    }
}
