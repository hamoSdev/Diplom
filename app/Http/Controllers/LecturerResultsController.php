<?php

namespace App\Http\Controllers;

use App\Enums\ExamGrade;
use App\Enums\ExamStatus;
use App\Models\Course;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Setting;
use App\Services\Exam\AdaptiveExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

/**
 * A lecturer's view of how their students did on the course exams: the overall
 * group result per course and each student's individual attempt. Everything is
 * read from finished ExamAttempts of the lecturer's own courses.
 */
class LecturerResultsController extends Controller
{
    public function __construct(private readonly AdaptiveExamService $exam) {}

    /**
     * The lecturer's courses with a headline of how the group did.
     */
    public function index(Request $request): Response
    {
        $courses = Course::query()
            ->where('lecturer_id', $request->user()->id)
            ->orderBy('title')
            ->get(['id', 'title']);

        $rows = $courses->map(function (Course $course): array {
            $finished = $this->finishedAttempts($course->id);
            $latest = $this->latestPerStudent($finished);

            return [
                'id' => $course->id,
                'title' => $course->title,
                'students' => $latest->count(),
                'attempts' => $finished->count(),
                'avg_score' => $this->average($latest),
            ];
        });

        return Inertia::render('lecturer/results/Index', [
            'courses' => $rows,
        ]);
    }

    /**
     * The group result for one course plus the per-student list.
     */
    public function show(Request $request, Course $course): Response
    {
        $this->authorize('update', $course);

        $finished = $this->finishedAttempts($course->id, withStudent: true);
        $byStudent = $finished->groupBy('student_id');
        $latest = $this->latestPerStudent($finished);
        $topics = $this->groupTopics($latest->pluck('id')->all());
        $reviseThreshold = Setting::int('revise_threshold', 60);

        return Inertia::render('lecturer/results/Show', [
            'course' => ['id' => $course->id, 'title' => $course->title],
            'summary' => [
                'students' => $latest->count(),
                'attempts' => $finished->count(),
                'avg_score' => $this->average($latest),
                'max_score' => 20,
                'grades' => $this->gradeDistribution($latest),
                'topics' => $topics,
                'revise' => collect($topics)
                    ->where('percent', '<', $reviseThreshold)
                    ->pluck('title')
                    ->values(),
            ],
            'students' => $byStudent
                ->map(function (Collection $group): array {
                    /** @var ExamAttempt $best */
                    $best = $group->sortByDesc('finished_at')->first();

                    return [
                        'attempt_id' => $best->id,
                        'name' => $best->student->name,
                        'score' => $best->score,
                        'grade' => $best->grade?->value,
                        'grade_label' => $best->grade?->label(),
                        'level_label' => $best->start_level->label(),
                        'finished_at' => $best->finished_at?->toDateString(),
                        'attempts_count' => $group->count(),
                    ];
                })
                ->sortByDesc('score')
                ->values(),
        ]);
    }

    /**
     * One student's attempt, broken down question-by-question and by topic.
     */
    public function attempt(Request $request, ExamAttempt $attempt): Response
    {
        $attempt->loadMissing('course', 'student:id,name');

        abort_unless(
            $attempt->course !== null
                && $attempt->course->lecturer_id === $request->user()->id
                && $attempt->status === ExamStatus::Finished,
            404,
        );

        return Inertia::render('lecturer/results/Attempt', [
            'studentName' => $attempt->student->name,
            'course' => ['id' => $attempt->course->id, 'title' => $attempt->course->title],
            'result' => $this->exam->summary($attempt),
        ]);
    }

    /**
     * Finished attempts for a course.
     *
     * @return Collection<int, ExamAttempt>
     */
    private function finishedAttempts(int $courseId, bool $withStudent = false): Collection
    {
        return ExamAttempt::query()
            ->where('course_id', $courseId)
            ->where('status', ExamStatus::Finished)
            ->when($withStudent, fn ($query) => $query->with('student:id,name'))
            ->get();
    }

    /**
     * Keep each student's most recent attempt, so retakes don't double-count.
     *
     * @param  Collection<int, ExamAttempt>  $attempts
     * @return Collection<int, ExamAttempt>
     */
    private function latestPerStudent(Collection $attempts): Collection
    {
        return $attempts
            ->groupBy('student_id')
            ->map(fn (Collection $group): ExamAttempt => $group->sortByDesc('finished_at')->first())
            ->values();
    }

    /**
     * Average score (0-20, one decimal) over a set of attempts.
     *
     * @param  Collection<int, ExamAttempt>  $attempts
     */
    private function average(Collection $attempts): ?float
    {
        $scored = $attempts->whereNotNull('score');

        return $scored->isEmpty() ? null : round((float) $scored->avg('score'), 1);
    }

    /**
     * Count of attempts in each grade band.
     *
     * @param  Collection<int, ExamAttempt>  $attempts
     * @return array<int, array{grade: string, label: string, count: int}>
     */
    private function gradeDistribution(Collection $attempts): array
    {
        return collect(ExamGrade::cases())
            ->map(fn (ExamGrade $grade): array => [
                'grade' => $grade->value,
                'label' => $grade->label(),
                'count' => $attempts->where('grade', $grade)->count(),
            ])
            ->all();
    }

    /**
     * Per-topic success across the given attempts, weakest first.
     *
     * @param  array<int, int>  $attemptIds
     * @return array<int, array{title: string, correct: int, total: int, percent: int}>
     */
    private function groupTopics(array $attemptIds): array
    {
        if ($attemptIds === []) {
            return [];
        }

        return ExamAnswer::query()
            ->whereIn('exam_attempt_id', $attemptIds)
            ->whereNotNull('topic_id')
            ->with('topic:id,title')
            ->get()
            ->groupBy(fn (ExamAnswer $answer): string => $answer->topic->title)
            ->filter(fn (Collection $group, string $title): bool => $title !== '')
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
            ->values()
            ->all();
    }
}
