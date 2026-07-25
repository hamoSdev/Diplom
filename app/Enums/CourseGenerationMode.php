<?php

namespace App\Enums;

enum CourseGenerationMode: string
{
    case SubjectOnly = 'subject_only';
    case TopicsProvided = 'topics_provided';

    /**
     * Get the human readable name of the generation mode.
     */
    public function label(): string
    {
        return match ($this) {
            self::SubjectOnly => __('Subject only — AI builds the topics'),
            self::TopicsProvided => __('Topics provided by lecturer'),
        };
    }
}
