<?php

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Http\Requests\TopicRequest;
use App\Jobs\GenerateTopicContent;
use App\Models\Course;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

/**
 * Topics of a course, managed by the owning lecturer. Each topic can also be
 * filled by AI (Scenario 2): the lecturer names the topics and the model writes
 * their study material and questions.
 */
class TopicController extends Controller
{
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

        return to_route('courses.show', $course);
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

        return to_route('courses.show', $course);
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

        return to_route('courses.show', $course);
    }

    /**
     * Scenario 2: generate this topic's material and questions with AI. Runs on
     * the queue; the course sits on Generating meanwhile (the hub polls for it).
     */
    public function generate(Course $course, Topic $topic): RedirectResponse
    {
        $this->authorize('update', $course);
        abort_unless($topic->course_id === $course->id, 404);

        if ($course->status === CourseStatus::Generating) {
            return back();
        }

        $course->status = CourseStatus::Generating;
        $course->save();

        GenerateTopicContent::dispatch($course, $topic);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Generating topic content…')]);

        return back();
    }
}
