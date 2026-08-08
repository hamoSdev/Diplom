<?php

namespace App\Http\Controllers;

use App\Enums\ExamStartLevel;
use App\Enums\ExamStatus;
use App\Http\Requests\StartExamRequest;
use App\Http\Requests\SubmitAnswerRequest;
use App\Models\Course;
use App\Models\ExamAttempt;
use App\Models\QuestionOption;
use App\Models\User;
use App\Services\Exam\AdaptiveExamService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The per-course adaptive exam. Same flow and engine as the overall level exam
 * (AdaptiveExamService), but scoped to one course: attempts carry the course id
 * and the engine draws from that course's authored question bank. The student
 * must belong to the course's faculty, and the course must have questions.
 */
class CourseExamController extends Controller
{
    public function __construct(private readonly AdaptiveExamService $exam) {}

    /**
     * Level selection / resume screen for the course exam.
     */
    public function show(Request $request, Course $course): Response|RedirectResponse
    {
        $this->guard($request, $course);

        if (! $course->questions()->exists()) {
            Inertia::flash('toast', ['type' => 'info', 'message' => __('This course has no exam yet.')]);

            return redirect()->route('learning.courses.show', $course);
        }

        if ($this->activeAttempt($request, $course) !== null) {
            return redirect()->route('course-exam.take', $course);
        }

        $last = $this->attemptsFor($request, $course)
            ->where('status', ExamStatus::Finished)
            ->latest('id')
            ->first();

        return Inertia::render('student/exam/Start', [
            'nav' => $this->nav($course),
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
     * Begin a course attempt from the self-assessed level.
     */
    public function start(StartExamRequest $request, Course $course): RedirectResponse
    {
        $this->guard($request, $course);

        if ($this->activeAttempt($request, $course) !== null || ! $course->questions()->exists()) {
            return redirect()->route('course-exam.show', $course);
        }

        $this->exam->start($request->user(), $request->enum('start_level', ExamStartLevel::class), $course);

        return redirect()->route('course-exam.take', $course);
    }

    /**
     * Show the current question, or finish when none remain.
     */
    public function take(Request $request, Course $course): Response|RedirectResponse
    {
        $this->guard($request, $course);
        $attempt = $this->activeAttempt($request, $course);

        if ($attempt === null) {
            return redirect()->route('course-exam.show', $course);
        }

        $question = $this->exam->nextQuestion($attempt);

        if ($question === null) {
            $this->exam->finish($attempt);

            return redirect()->route('course-exam.result', [$course, $attempt]);
        }

        return Inertia::render('student/exam/Take', [
            'nav' => $this->nav($course),
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
     * Record the answer and advance the exam.
     */
    public function answer(SubmitAnswerRequest $request, Course $course): RedirectResponse
    {
        $this->guard($request, $course);
        $attempt = $this->activeAttempt($request, $course);

        if ($attempt === null) {
            return redirect()->route('course-exam.show', $course);
        }

        $question = $this->exam->nextQuestion($attempt);

        if ($question !== null) {
            $this->exam->submitAnswer($attempt, $question, $request->integer('selected_option_id'));
        }

        if ($this->exam->isComplete($attempt)) {
            $this->exam->finish($attempt);

            return redirect()->route('course-exam.result', [$course, $attempt]);
        }

        return redirect()->route('course-exam.take', $course);
    }

    /**
     * Testing shortcut: auto-answer the rest at random and finish.
     */
    public function skip(Request $request, Course $course): RedirectResponse
    {
        $this->guard($request, $course);
        $attempt = $this->activeAttempt($request, $course);

        if ($attempt === null) {
            return redirect()->route('course-exam.show', $course);
        }

        while ($question = $this->exam->nextQuestion($attempt)) {
            $this->exam->submitAnswer($attempt, $question, $question->options->random()->id);
        }

        $this->exam->finish($attempt);

        return redirect()->route('course-exam.result', [$course, $attempt]);
    }

    /**
     * Show a finished course attempt.
     */
    public function result(Request $request, Course $course, ExamAttempt $attempt): Response|RedirectResponse
    {
        $this->guard($request, $course);
        abort_unless($attempt->student_id === $request->user()->id && $attempt->course_id === $course->id, 403);

        if ($attempt->status !== ExamStatus::Finished) {
            return redirect()->route('course-exam.take', $course);
        }

        return Inertia::render('student/exam/Result', [
            'nav' => $this->nav($course),
            'result' => $this->exam->summary($attempt),
        ]);
    }

    /**
     * Route/label context so the shared exam screens point at course endpoints.
     *
     * @return array<string, mixed>
     */
    private function nav(Course $course): array
    {
        return [
            'title' => $course->title.' — exam',
            'breadcrumbs' => [
                ['title' => 'My learning', 'href' => route('learning.index')],
                ['title' => $course->title, 'href' => route('learning.courses.show', $course)],
                ['title' => 'Exam', 'href' => route('course-exam.show', $course)],
            ],
            'startUrl' => route('course-exam.start', $course),
            'answerUrl' => route('course-exam.answer', $course),
            'skipUrl' => route('course-exam.skip', $course),
            'retakeUrl' => route('course-exam.show', $course),
            'backUrl' => route('learning.courses.show', $course),
            'backLabel' => __('Back to course'),
        ];
    }

    /**
     * A student may only reach a course in their own faculty.
     */
    private function guard(Request $request, Course $course): void
    {
        $course->loadMissing('subject:id,faculty_id');

        abort_unless(
            $course->subject !== null
                && $course->subject->faculty_id === $request->user()->faculty_id,
            403,
        );
    }

    /**
     * The student's in-progress attempt for this course, if any.
     */
    private function activeAttempt(Request $request, Course $course): ?ExamAttempt
    {
        return $this->attemptsFor($request, $course)
            ->where('status', ExamStatus::InProgress)
            ->latest('id')
            ->first();
    }

    /**
     * Base query for this student's attempts at this course.
     *
     * @return HasMany<ExamAttempt, User>
     */
    private function attemptsFor(Request $request, Course $course)
    {
        return $request->user()->examAttempts()->where('course_id', $course->id);
    }
}
