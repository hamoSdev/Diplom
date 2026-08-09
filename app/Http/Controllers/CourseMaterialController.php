<?php

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreCourseMaterialRequest;
use App\Jobs\GenerateCourseFromMaterials;
use App\Models\Course;
use App\Models\CourseMaterial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Course material files. A lecturer manages the files of their own courses;
 * downloading is also available to admins and to students of the course's
 * faculty. Files live on the private disk and are only reachable through the
 * authorized download action, never by a public URL.
 */
class CourseMaterialController extends Controller
{
    /**
     * Upload one or more files as material for the course.
     */
    public function store(StoreCourseMaterialRequest $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        foreach ($request->file('files') as $file) {
            $course->materials()->create([
                'original_name' => $file->getClientOriginalName(),
                'path' => $file->store('course-materials'),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Material uploaded.')]);

        return to_route('courses.show', $course);
    }

    /**
     * Scenario 3: generate a question bank from the uploaded materials with AI.
     * Runs on the queue; the course sits on Generating while it works.
     */
    public function generate(Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        if (! $course->materials()->exists()) {
            Inertia::flash('toast', ['type' => 'info', 'message' => __('Upload a file first.')]);

            return back();
        }

        if ($course->status === CourseStatus::Generating) {
            return back();
        }

        $course->status = CourseStatus::Generating;
        $course->save();

        GenerateCourseFromMaterials::dispatch($course);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Generating questions from materials…')]);

        return back();
    }

    /**
     * Remove a file from the course.
     */
    public function destroy(Course $course, CourseMaterial $material): RedirectResponse
    {
        $this->authorize('update', $course);
        abort_unless($material->course_id === $course->id, 404);

        Storage::delete($material->path);
        $material->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Material deleted.')]);

        return to_route('courses.show', $course);
    }

    /**
     * Stream a material file to anyone allowed to see the course.
     */
    public function download(Request $request, CourseMaterial $material): StreamedResponse
    {
        $material->load('course.subject:id,faculty_id');
        $user = $request->user();
        $course = $material->course;

        $allowed = $user->role === UserRole::Admin
            || $course->lecturer_id === $user->id
            || ($user->role === UserRole::Student && $course->subject?->faculty_id === $user->faculty_id);

        abort_unless($allowed, 403);

        return Storage::download($material->path, $material->original_name);
    }
}
