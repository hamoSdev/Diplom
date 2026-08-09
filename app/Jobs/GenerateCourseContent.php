<?php

namespace App\Jobs;

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Services\Ai\CourseContentGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

/**
 * Runs the Scenario 1 AI pipeline for a course off the request cycle. The course
 * is flipped to Generating before dispatch; this job lands it on Ready, or on
 * Failed if the model call or parsing breaks.
 */
class GenerateCourseContent implements ShouldQueue
{
    use Queueable;

    /**
     * One attempt — regenerating on a flaky reply would risk duplicate content.
     */
    public int $tries = 1;

    public int $timeout = 600;

    public function __construct(public readonly Course $course) {}

    public function handle(CourseContentGenerator $generator): void
    {
        $generator->generate($this->course);

        // status is guarded (not fillable), so set it directly.
        $this->course->status = CourseStatus::Ready;
        $this->course->save();
    }

    public function failed(?Throwable $exception): void
    {
        $this->course->status = CourseStatus::Failed;
        $this->course->save();
    }
}
