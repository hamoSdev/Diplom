<?php

namespace App\Enums;

enum QuestionType: string
{
    case Single = 'single';
    case Multiple = 'multiple';
    case Boolean = 'boolean';
    case Open = 'open';

    /**
     * Get the human readable name of the question type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Single => __('Single choice'),
            self::Multiple => __('Multiple choice'),
            self::Boolean => __('True / false'),
            self::Open => __('Open answer'),
        };
    }

    /**
     * Whether the type is answered by picking from a fixed set of options.
     * Open questions carry no options and are graded from free text.
     */
    public function hasOptions(): bool
    {
        return $this !== self::Open;
    }

    /**
     * Whether more than one option may be marked correct.
     */
    public function allowsMultipleCorrect(): bool
    {
        return $this === self::Multiple;
    }
}
