<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionRequest;
use App\Models\Course;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

/**
 * The per-course question bank a lecturer authors by hand. Each question is
 * tagged with a topic and a difficulty, and carries its answer options with
 * exactly one marked correct. The adaptive engine later draws the course exam
 * from these.
 */
class QuestionController extends Controller
{
    /**
     * Store a new question with its options.
     */
    public function store(StoreQuestionRequest $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $data = $request->validated();

        $question = $course->questions()->create([
            'topic_id' => $data['topic_id'],
            'type' => $data['type'],
            'difficulty' => $data['difficulty'],
            'body' => $data['body'],
            'explanation' => $data['explanation'] ?? null,
        ]);

        foreach (array_values($data['options']) as $position => $option) {
            $question->options()->create([
                'body' => $option['body'],
                'is_correct' => (bool) $option['is_correct'],
                'position' => $position + 1,
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Question added.')]);

        return to_route('courses.show', $course);
    }

    /**
     * Remove a question from the course (its options cascade).
     */
    public function destroy(Course $course, Question $question): RedirectResponse
    {
        $this->authorize('update', $course);
        abort_unless($question->course_id === $course->id, 404);

        $question->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Question deleted.')]);

        return to_route('courses.show', $course);
    }
}
