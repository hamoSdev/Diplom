<?php

namespace App\Http\Controllers;

use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Http\Requests\StoreQuestionRequest;
use App\Models\Course;
use App\Models\Question;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The per-course question bank a lecturer authors by hand. Each question is
 * tagged with a topic and a difficulty, and carries its answer options with
 * exactly one marked correct. The adaptive engine later draws the course exam
 * from these.
 */
class QuestionController extends Controller
{
    /**
     * List the course's questions and everything the author form needs.
     */
    public function index(Course $course): Response
    {
        $this->authorize('update', $course);

        $questions = $course->questions()->with('topic:id,title')->get()
            ->map(fn (Question $question): array => [
                'id' => $question->id,
                'body' => $question->body,
                'topic_title' => $question->topic?->title,
                'type_label' => $question->type->label(),
                'difficulty_label' => $question->difficulty->label(),
            ]);

        return Inertia::render('courses/Questions', [
            'course' => $course->only('id', 'title'),
            'topics' => $course->topics->map(fn (Topic $topic): array => [
                'id' => $topic->id,
                'title' => $topic->title,
            ]),
            'questions' => $questions,
            'typeOptions' => $this->enumOptions(QuestionType::cases()),
            'difficultyOptions' => $this->enumOptions(QuestionDifficulty::cases()),
        ]);
    }

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

        return to_route('courses.questions.index', $course);
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

        return to_route('courses.questions.index', $course);
    }

    /**
     * Turn a set of backed enum cases into value/label options for a select.
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
}
