<?php

namespace App\Services\Exam;

use App\Enums\ExamGrade;
use App\Enums\ExamStartLevel;
use App\Enums\ExamStatus;
use App\Enums\QuestionType;
use App\Models\Course;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Adaptive level exam engine — a simplified Rasch / Elo model.
 *
 * The student has an ability estimate theta (attempt->ability), starting from
 * their self-assessed level. Each question has a difficulty b (easy -1, medium
 * 0, hard +1). The next question is the unanswered one whose difficulty is
 * closest to the current theta (≈ 50% success — most informative). After each
 * answer theta is nudged: P = 1 / (1 + e^(b - theta)); theta += K * (result - P).
 * The exam ends after `length` questions (capped at EXAM_LENGTH, but never more
 * than the bank holds); the final score (0-20) is the difficulty-weighted share
 * of correct answers, mapped to a grade band. An attempt without a course draws
 * from the global level-exam bank; a course attempt draws from that course's
 * authored questions.
 */
class AdaptiveExamService
{
    public const EXAM_LENGTH = 15;

    private const K = 0.5;

    private const CATEGORY = 'IT';

    /** Topics scoring below this percentage are suggested for revision. */
    private const REVISE_THRESHOLD = 60;

    public function __construct(private readonly OpenAnswerGrader $openGrader) {}

    /**
     * Begin a new attempt seeded from the student's self-assessed level. Pass a
     * course to run its exam instead of the overall level exam.
     */
    public function start(User $student, ExamStartLevel $level, ?Course $course = null): ExamAttempt
    {
        $bankCount = $this->bankQuery($course?->id)->count();
        $maxLength = Setting::int('default_exam_length', self::EXAM_LENGTH);

        return $student->examAttempts()->create([
            'course_id' => $course?->id,
            'start_level' => $level,
            'ability' => $level->startingTheta(),
            'length' => max(1, min($maxLength, $bankCount)),
            'status' => ExamStatus::InProgress,
            'started_at' => now(),
        ]);
    }

    /**
     * The next question to ask, or null when the exam is complete.
     */
    public function nextQuestion(ExamAttempt $attempt): ?Question
    {
        if ($this->isComplete($attempt)) {
            return null;
        }

        $answeredIds = $attempt->answers()->pluck('question_id');
        $theta = $attempt->ability;

        return $this->bankQuery($attempt->course_id)
            ->whereNotIn('id', $answeredIds)
            ->with('options')
            ->get()
            ->sortBy(fn (Question $question): float => abs($question->difficulty->bValue() - $theta))
            ->first();
    }

    /**
     * The pool of questions an attempt draws from.
     *
     * @return Builder<Question>
     */
    private function bankQuery(?int $courseId): Builder
    {
        return $courseId === null
            ? Question::query()->where('category', self::CATEGORY)
            : Question::query()->where('course_id', $courseId);
    }

    /**
     * Record the student's answer and update the ability estimate. The payload
     * carries the chosen option ids (single/multiple/true-false) and/or free
     * text (open). Open answers are graded later in a batch, so they are stored
     * as not-yet-correct and do not move the ability estimate here.
     *
     * @param  array{option_ids?: array<int, int|string>, text?: string|null}  $payload
     */
    public function submitAnswer(ExamAttempt $attempt, Question $question, array $payload): ExamAnswer
    {
        $isOpen = $question->type === QuestionType::Open;
        $optionIds = $isOpen ? [] : $this->validOptionIds($question, $payload['option_ids'] ?? []);
        $text = is_string($payload['text'] ?? null) ? trim($payload['text']) : null;

        $isCorrect = ! $isOpen && $this->optionsAreCorrect($question, $optionIds);

        $answer = $attempt->answers()->create([
            'question_id' => $question->id,
            'topic_id' => $question->topic_id,
            'selected_option_id' => count($optionIds) === 1 ? $optionIds[0] : null,
            'selected_option_ids' => $isOpen ? null : $optionIds,
            'answer_text' => $isOpen ? $text : null,
            'is_correct' => $isCorrect,
            'difficulty_at_time' => $question->difficulty,
            'position' => $attempt->answers()->count() + 1,
        ]);

        if (! $isOpen) {
            $b = $question->difficulty->bValue();
            $expected = 1 / (1 + exp($b - $attempt->ability));
            $attempt->ability += self::K * (($isCorrect ? 1 : 0) - $expected);
            $attempt->save();
        }

        return $answer;
    }

    /**
     * Keep only the ids that actually belong to the question, as ints.
     *
     * @param  array<int, int|string>  $ids
     * @return array<int, int>
     */
    private function validOptionIds(Question $question, array $ids): array
    {
        $valid = $question->options->pluck('id')->all();

        return array_values(array_intersect(array_map('intval', $ids), $valid));
    }

    /**
     * An option answer is correct when the chosen set matches the correct set
     * exactly — one option for single/true-false, all of them for multiple.
     *
     * @param  array<int, int>  $optionIds
     */
    private function optionsAreCorrect(Question $question, array $optionIds): bool
    {
        $correct = $question->options->where('is_correct', true)->pluck('id')->sort()->values()->all();
        $chosen = collect($optionIds)->unique()->sort()->values()->all();

        return $correct !== [] && $correct === $chosen;
    }

    /**
     * Finalize the attempt: compute the score and grade.
     */
    public function finish(ExamAttempt $attempt): void
    {
        // Open answers are graded now, in one batch, before scoring.
        $this->openGrader->gradeOpenAnswers($attempt);

        $score = $this->score($attempt);

        $attempt->update([
            'status' => ExamStatus::Finished,
            'score' => $score,
            'grade' => ExamGrade::fromScore($score),
            'finished_at' => now(),
        ]);
    }

    /**
     * Whether every question of the exam has been answered.
     */
    public function isComplete(ExamAttempt $attempt): bool
    {
        return $attempt->answers()->count() >= $attempt->length;
    }

    /**
     * Full result payload for a finished attempt: score, grade, per-question
     * breakdown, and per-topic analysis with topics suggested for revision.
     *
     * @return array<string, mixed>
     */
    public function summary(ExamAttempt $attempt): array
    {
        $answers = $attempt->answers()->with('topic:id,title')->get();

        $rows = $answers
            ->map(fn (ExamAnswer $answer): array => [
                'title' => $answer->topic?->title,
                'is_correct' => $answer->is_correct,
            ])
            ->filter(fn (array $row): bool => $row['title'] !== null);

        $topics = $rows
            ->groupBy('title')
            ->map(function (Collection $group, string $title): array {
                $total = $group->count();
                $correct = $group->where('is_correct', true)->count();

                return [
                    'title' => $title,
                    'correct' => $correct,
                    'total' => $total,
                    'percent' => (int) round(100 * $correct / $total),
                ];
            })
            ->sortBy('percent')
            ->values();

        return [
            'score' => $attempt->score,
            'max_score' => 20,
            'grade' => $attempt->grade?->value,
            'grade_label' => $attempt->grade?->label(),
            'level_label' => $attempt->start_level->label(),
            'correct' => $answers->where('is_correct', true)->count(),
            'total' => $answers->count(),
            'answers' => $answers->map(fn (ExamAnswer $answer): array => [
                'position' => $answer->position,
                'difficulty_label' => $answer->difficulty_at_time->label(),
                'is_correct' => $answer->is_correct,
            ]),
            'topics' => $topics,
            'revise' => $topics
                ->where('percent', '<', Setting::int('revise_threshold', self::REVISE_THRESHOLD))
                ->pluck('title')
                ->values(),
        ];
    }

    /**
     * Difficulty-weighted share of correct answers, on a 0-20 scale.
     */
    private function score(ExamAttempt $attempt): int
    {
        $answers = $attempt->answers()->get();

        if ($answers->isEmpty()) {
            return 0;
        }

        $earned = 0.0;
        $total = 0.0;

        foreach ($answers as $answer) {
            $weight = $answer->difficulty_at_time->weight();
            $total += $weight;

            if ($answer->is_correct) {
                $earned += $weight;
            }
        }

        return (int) round(20 * $earned / $total);
    }
}
