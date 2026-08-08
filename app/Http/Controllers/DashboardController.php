<?php

namespace App\Http\Controllers;

use App\Enums\ExamStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Faculty;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the role-specific dashboard for the current user.
     */
    public function index(Request $request): Response
    {
        return match ($request->user()->role) {
            UserRole::Student => $this->studentDashboard($request),
            UserRole::Lecturer => $this->lecturerDashboard($request),
            UserRole::Admin => $this->adminDashboard(),
        };
    }

    /**
     * Platform-wide counts for an administrator.
     */
    private function adminDashboard(): Response
    {
        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'students' => User::query()->where('role', UserRole::Student)->count(),
                'lecturers' => User::query()->where('role', UserRole::Lecturer)->count(),
                'faculties' => Faculty::query()->count(),
                'subjects' => Subject::query()->count(),
                'courses' => Course::query()->count(),
            ],
        ]);
    }

    /**
     * A teaching overview for a lecturer: their own courses and bank.
     */
    private function lecturerDashboard(Request $request): Response
    {
        $user = $request->user();
        $ownCourse = fn ($query) => $query->where('lecturer_id', $user->id);

        return Inertia::render('lecturer/Dashboard', [
            'stats' => [
                'courses' => $user->courses()->count(),
                'subjects' => $user->taughtSubjects()->count(),
                'topics' => Topic::query()->whereHas('course', $ownCourse)->count(),
                'questions' => Question::query()->whereHas('course', $ownCourse)->count(),
            ],
        ]);
    }

    /**
     * A first-glance overview for a student: their faculty and how much
     * learning it opens up.
     */
    private function studentDashboard(Request $request): Response
    {
        $user = $request->user();
        $faculty = $user->faculty;

        $subjectsCount = 0;
        $coursesCount = 0;

        if ($faculty !== null) {
            $subjectsCount = $faculty->subjects()->count();
            $coursesCount = Course::query()
                ->whereHas('subject', fn ($query) => $query->where('faculty_id', $faculty->id))
                ->count();
        }

        $courseExams = $user->examAttempts()
            ->whereNotNull('course_id')
            ->where('status', ExamStatus::Finished);

        $examsTaken = (clone $courseExams)->count();
        $coursesExamined = (clone $courseExams)->distinct()->count('course_id');
        $avgScore = $examsTaken > 0 ? (int) round((float) (clone $courseExams)->avg('score')) : null;

        $level = $user->examAttempts()
            ->whereNull('course_id')
            ->where('status', ExamStatus::Finished)
            ->latest('id')
            ->first();

        $recentResults = $user->examAttempts()
            ->where('status', ExamStatus::Finished)
            ->with('course:id,title')
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (ExamAttempt $attempt): array => [
                'id' => $attempt->id,
                'label' => $attempt->course_id === null ? __('Level exam') : $attempt->course->title,
                'grade' => $attempt->grade?->value,
                'grade_label' => $attempt->grade?->label(),
                'score' => $attempt->score,
                'date' => $attempt->finished_at?->format('M j, Y'),
                'url' => $attempt->course_id === null
                    ? route('exam.result', $attempt->id)
                    : route('course-exam.result', [$attempt->course_id, $attempt->id]),
            ]);

        $topicMastery = ExamAnswer::query()
            ->whereNotNull('topic_id')
            ->whereHas('attempt', fn ($query) => $query
                ->where('student_id', $user->id)
                ->where('status', ExamStatus::Finished))
            ->selectRaw('topic_id, COUNT(*) as total, SUM(is_correct) as correct')
            ->groupBy('topic_id')
            ->with(['topic:id,title,course_id', 'topic.course:id,title'])
            ->get()
            ->map(function (ExamAnswer $row): array {
                $total = (int) $row->getAttribute('total');
                $correct = (int) $row->getAttribute('correct');

                return [
                    'title' => $row->topic->title,
                    'course' => $row->topic->course->title,
                    'percent' => (int) round(100 * $correct / max(1, $total)),
                    'url' => route('learning.courses.show', $row->topic->course_id),
                ];
            })
            ->sortBy('percent')
            ->take(8)
            ->values();

        $courses = $faculty === null ? collect() : Course::query()
            ->whereHas('subject', fn ($query) => $query->where('faculty_id', $faculty->id))
            ->with('subject:id,name')
            ->latest('id')
            ->limit(6)
            ->get()
            ->map(fn (Course $course): array => [
                'id' => $course->id,
                'title' => $course->title,
                'subject_name' => $course->subject->name,
                'url' => route('learning.courses.show', $course->id),
            ]);

        return Inertia::render('student/Dashboard', [
            'faculty' => $faculty?->only('id', 'name'),
            'stats' => [
                'subjects' => $subjectsCount,
                'courses' => $coursesCount,
                'courses_examined' => $coursesExamined,
                'exams_taken' => $examsTaken,
                'avg_score' => $avgScore,
            ],
            'level' => $level === null ? null : [
                'grade_label' => $level->grade?->label(),
                'score' => $level->score,
                'level_label' => $level->start_level->label(),
            ],
            'recentResults' => $recentResults,
            'topicMastery' => $topicMastery,
            'courses' => $courses,
        ]);
    }
}
