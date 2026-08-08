<?php

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Jobs\GenerateCourseContent;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Question;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    /**
     * The course management hub: content, topics, questions and materials in
     * one place. Shared by the owning lecturer and (via Gate::before) admins.
     */
    public function show(Course $course): Response
    {
        $this->authorize('update', $course);

        $course->load('subject:id,name');

        return Inertia::render('courses/Show', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'subject_name' => $course->subject?->name,
                'status' => $course->status->value,
                'status_label' => $course->status->label(),
                'content' => $course->content === null ? null : clean($course->content, 'course'),
            ],
            'topics' => $course->topics->map(fn (Topic $topic): array => [
                'id' => $topic->id,
                'title' => $topic->title,
                'content' => $topic->content === null ? null : clean($topic->content, 'course'),
            ]),
            'questions' => $course->questions()->with('topic:id,title')->get()
                ->map(fn (Question $question): array => [
                    'id' => $question->id,
                    'body' => $question->body,
                    'topic_title' => $question->topic?->title,
                    'type_label' => $question->type->label(),
                    'difficulty_label' => $question->difficulty->label(),
                ]),
            'materials' => $course->materials->map(fn (CourseMaterial $material): array => [
                'id' => $material->id,
                'original_name' => $material->original_name,
                'size' => $material->size,
            ]),
            'typeOptions' => $this->enumOptions(QuestionType::cases()),
            'difficultyOptions' => $this->enumOptions(QuestionDifficulty::cases()),
        ]);
    }

    /**
     * Kick off the Scenario 1 AI pipeline (topics, material, questions) for the
     * course. Runs on the queue; the course sits on Generating meanwhile.
     */
    public function generate(Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        if ($course->status === CourseStatus::Generating) {
            return back();
        }

        $course->update(['status' => CourseStatus::Generating]);

        GenerateCourseContent::dispatch($course);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('AI generation started.')]);

        return back();
    }

    /**
     * Turn backed enum cases into value/label options for a select.
     *
     * @param  array<int, QuestionType|QuestionDifficulty>  $cases
     * @return Collection<int, array{value: string, label: string}>
     */
    private function enumOptions(array $cases): Collection
    {
        return collect($cases)->map(fn (QuestionType|QuestionDifficulty $case): array => [
            'value' => $case->value,
            'label' => $case->label(),
        ]);
    }

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
