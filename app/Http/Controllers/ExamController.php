<?php

namespace App\Http\Controllers;

use App\Enums\ExamStartLevel;
use App\Enums\ExamStatus;
use App\Http\Requests\StartExamRequest;
use App\Http\Requests\SubmitAnswerRequest;
use App\Models\ExamAttempt;
use App\Models\QuestionOption;
use App\Services\Exam\AdaptiveExamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The overall adaptive level exam a student takes to reveal their level.
 * A student has at most one attempt in progress at a time; the engine
 * (AdaptiveExamService) drives question selection and scoring, while this
 * controller only orchestrates the flow between screens.
 */
class ExamController extends Controller
{
    public function __construct(private readonly AdaptiveExamService $exam) {}

    /**
     * Entry point: resume an active attempt, otherwise offer level selection
     * along with the student's most recent result.
     */
    public function show(Request $request): Response|RedirectResponse
    {
        if ($this->activeAttempt($request) !== null) {
            return redirect()->route('exam.take');
        }

        $last = $request->user()->examAttempts()
            ->whereNull('course_id')
            ->where('status', ExamStatus::Finished)
            ->latest('id')
            ->first();

        return Inertia::render('student/exam/Start', [
            'lastResult' => $last === null ? null : [
                'id' => $last->id,
                'score' => $last->score,
                'grade' => $last->grade?->value,
                'grade_label' => $last->grade?->label(),
                'level_label' => $last->start_level->label(),
            ],
        ]);
    }

    /**
     * Begin a new attempt seeded from the self-assessed level.
     */
    public function start(StartExamRequest $request): RedirectResponse
    {
        if ($this->activeAttempt($request) !== null) {
            return redirect()->route('exam.take');
        }

        $this->exam->start(
            $request->user(),
            $request->enum('start_level', ExamStartLevel::class),
        );

        return redirect()->route('exam.take');
    }

    /**
     * Show the current question, or finish the exam when none remain.
     */
    public function take(Request $request): Response|RedirectResponse
    {
        $attempt = $this->activeAttempt($request);

        if ($attempt === null) {
            return redirect()->route('exam.show');
        }

        $question = $this->exam->nextQuestion($attempt);

        if ($question === null) {
            $this->exam->finish($attempt);

            return redirect()->route('exam.result', $attempt);
        }

        return Inertia::render('student/exam/Take', [
            'question' => [
                'id' => $question->id,
                'body' => $question->body,
                'type' => $question->type->value,
                'options' => $question->options->map(fn (QuestionOption $option): array => [
                    'id' => $option->id,
                    'body' => $option->body,
                ]),
            ],
            'progress' => [
                'answered' => $attempt->answers()->count(),
                'current' => $attempt->answers()->count() + 1,
                'total' => $attempt->length,
            ],
        ]);
    }

    /**
     * Record the answer to the current question and advance the exam.
     */
    public function answer(SubmitAnswerRequest $request): RedirectResponse
    {
        $attempt = $this->activeAttempt($request);

        if ($attempt === null) {
            return redirect()->route('exam.show');
        }

        $question = $this->exam->nextQuestion($attempt);

        if ($question !== null) {
            $this->exam->submitAnswer($attempt, $question, $request->integer('selected_option_id'));
        }

        if ($this->exam->isComplete($attempt)) {
            $this->exam->finish($attempt);

            return redirect()->route('exam.result', $attempt);
        }

        return redirect()->route('exam.take');
    }

    /**
     * Testing shortcut: auto-answer the remaining questions at random and
     * finish, so the result screen can be reached without a full run.
     */
    public function skip(Request $request): RedirectResponse
    {
        $attempt = $this->activeAttempt($request);

        if ($attempt === null) {
            return redirect()->route('exam.show');
        }

        while ($question = $this->exam->nextQuestion($attempt)) {
            $this->exam->submitAnswer($attempt, $question, $question->options->random()->id);
        }

        $this->exam->finish($attempt);

        return redirect()->route('exam.result', $attempt);
    }

    /**
     * Show a finished attempt's outcome with a per-question breakdown.
     */
    public function result(Request $request, ExamAttempt $attempt): Response|RedirectResponse
    {
        abort_unless($attempt->student_id === $request->user()->id, 403);

        if ($attempt->status !== ExamStatus::Finished) {
            return redirect()->route('exam.take');
        }

        return Inertia::render('student/exam/Result', [
            'result' => $this->exam->summary($attempt),
        ]);
    }

    /**
     * The student's single in-progress attempt, if any.
     */
    private function activeAttempt(Request $request): ?ExamAttempt
    {
        return $request->user()->examAttempts()
            ->whereNull('course_id')
            ->where('status', ExamStatus::InProgress)
            ->latest('id')
            ->first();
    }
}
