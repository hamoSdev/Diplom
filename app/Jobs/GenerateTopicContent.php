<?php

namespace App\Jobs;

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\Topic;
use App\Services\Ai\CourseContentGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

/**
 * Scenario 2: the lecturer authored the topics; this fills one topic's study
 * material and questions with AI, off the request cycle. The course sits on
 * Generating while it runs so the hub shows progress and polls for the result.
 */
class GenerateTopicContent implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 600;

    public function __construct(
        public readonly Course $course,
        public readonly Topic $topic,
    ) {}

    public function handle(CourseContentGenerator $generator): void
    {
        $generator->generateForTopic($this->course, $this->topic);

        $this->course->status = CourseStatus::Ready;
        $this->course->save();
    }

    public function failed(?Throwable $exception): void
    {
        $this->course->status = CourseStatus::Failed;
        $this->course->save();
    }
}
