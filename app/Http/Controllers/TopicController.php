<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopicRequest;
use App\Models\Course;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Topics of a course, managed by the owning lecturer. Later steps (per-topic
 * material and the question bank) hang off these; for now the lecturer just
 * names and orders them.
 */
class TopicController extends Controller
{
    /**
     * List and manage the topics of one of the lecturer's courses.
     */
    public function index(Course $course): Response
    {
        $this->authorize('update', $course);

        return Inertia::render('courses/Topics', [
            'course' => $course->only('id', 'title'),
            'topics' => $course->topics->map(fn (Topic $topic): array => [
                'id' => $topic->id,
                'title' => $topic->title,
            ]),
        ]);
    }

    /**
     * Add a topic to the end of the course's list.
     */
    public function store(TopicRequest $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $course->topics()->create([
            'title' => $request->validated('title'),
            'position' => (int) $course->topics()->max('position') + 1,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Topic added.')]);

        return to_route('courses.topics.index', $course);
    }

    /**
     * Rename a topic.
     */
    public function update(TopicRequest $request, Course $course, Topic $topic): RedirectResponse
    {
        $this->authorize('update', $course);
        abort_unless($topic->course_id === $course->id, 404);

        $topic->update(['title' => $request->validated('title')]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Topic updated.')]);

        return to_route('courses.topics.index', $course);
    }

    /**
     * Remove a topic from the course.
     */
    public function destroy(Course $course, Topic $topic): RedirectResponse
    {
        $this->authorize('update', $course);
        abort_unless($topic->course_id === $course->id, 404);

        $topic->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Topic deleted.')]);

        return to_route('courses.topics.index', $course);
    }
}
