<?php

namespace App\Jobs;

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Services\Ai\CourseContentGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

/**
 * Scenario 3: build a question bank from the course's uploaded materials, off
 * the request cycle. The course sits on Generating while the files are read and
 * the questions are drafted, then lands on Ready (or Failed on error).
 */
class GenerateCourseFromMaterials implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 600;

    public function __construct(public readonly Course $course) {}

    public function handle(CourseContentGenerator $generator): void
    {
        $generator->generateFromMaterials($this->course);

        $this->course->status = CourseStatus::Ready;
        $this->course->save();
    }

    public function failed(?Throwable $exception): void
    {
        $this->course->status = CourseStatus::Failed;
        $this->course->save();
    }
}
