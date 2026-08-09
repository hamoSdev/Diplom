<?php

namespace App\Services\Exam;

use App\Enums\QuestionType;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Services\Ai\ClaudeClient;
use Throwable;

/**
 * Grades an attempt's open (free-text) answers in one batch at finish time.
 * Each answer is judged correct/incorrect against the question and its model
 * answer. If the model call fails the answers are left as recorded (incorrect),
 * so finishing an exam never hard-fails on the AI.
 */
class OpenAnswerGrader
{
    public function __construct(private readonly ClaudeClient $claude) {}

    public function gradeOpenAnswers(ExamAttempt $attempt): void
    {
        $open = $attempt->answers()
            ->with('question:id,type,body,expected_answer')
            ->get()
            ->filter(fn (ExamAnswer $answer): bool => $answer->question?->type === QuestionType::Open)
            ->values();

        if ($open->isEmpty()) {
            return;
        }

        $items = $open->map(fn (ExamAnswer $answer, int $i): array => [
            'index' => $i,
            'question' => $answer->question->body,
            'expected_answer' => $answer->question->expected_answer ?? '(none provided)',
            'student_answer' => $answer->answer_text ?? '',
        ])->all();

        try {
            $verdicts = $this->claude->json(
                'You are grading open-ended exam answers. For each item decide whether the student answer is essentially correct given the question and the expected answer. Be reasonable: accept correct answers phrased differently. '
                .'Return a JSON array of objects {"index": int, "correct": boolean}, one per item.',
                json_encode($items, JSON_UNESCAPED_UNICODE) ?: '[]',
            );
        } catch (Throwable) {
            return;
        }

        foreach ((array) $verdicts as $verdict) {
            if (! is_array($verdict) || ! isset($verdict['index'])) {
                continue;
            }

            $answer = $open->get((int) $verdict['index']);

            if ($answer !== null) {
                $answer->update(['is_correct' => (bool) ($verdict['correct'] ?? false)]);
            }
        }
    }
}
