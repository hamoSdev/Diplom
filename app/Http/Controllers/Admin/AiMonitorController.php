<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin oversight of the AI content pipeline: every course with its generation
 * status, so an administrator can watch generations in flight and spot failures
 * across the platform. Failed and in-progress courses are surfaced first.
 */
class AiMonitorController extends Controller
{
    /**
     * How the statuses are ordered on the board: problems and activity first.
     */
    private const PRIORITY = [
        'failed' => 0,
        'generating' => 1,
        'draft' => 2,
        'ready' => 3,
    ];

    public function index(): Response
    {
        $counts = Course::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $courses = Course::query()
            ->with(['lecturer:id,name', 'subject:id,name'])
            ->withCount(['topics', 'questions', 'materials'])
            ->get()
            ->map(fn (Course $course): array => [
                'id' => $course->id,
                'title' => $course->title,
                'lecturer' => $course->lecturer->name,
                'subject' => $course->subject_id === null ? null : $course->subject->name,
                'status' => $course->status->value,
                'status_label' => $course->status->label(),
                'topics_count' => $course->topics_count,
                'questions_count' => $course->questions_count,
                'materials_count' => $course->materials_count,
                'updated_at' => $course->updated_at?->format('M j, H:i'),
                'priority' => self::PRIORITY[$course->status->value],
            ])
            ->sortBy('priority')
            ->values();

        return Inertia::render('admin/AiMonitor', [
            'summary' => collect(CourseStatus::cases())
                ->map(fn (CourseStatus $status): array => [
                    'status' => $status->value,
                    'label' => $status->label(),
                    'count' => (int) ($counts[$status->value] ?? 0),
                ])
                ->all(),
            'courses' => $courses,
        ]);
    }
}
