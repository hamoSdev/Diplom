<?php

namespace App\Enums;

enum QuestionType: string
{
    case Single = 'single';
    case Boolean = 'boolean';

    /**
     * Get the human readable name of the question type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Single => __('Single choice'),
            self::Boolean => __('True / false'),
        };
    }
}
