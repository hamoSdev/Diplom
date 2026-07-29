<?php

namespace App\Enums;

enum ExamStartLevel: string
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';

    /**
     * The ability (theta) the exam starts from for this self-assessed level.
     */
    public function startingTheta(): float
    {
        return match ($this) {
            self::Beginner => -1.0,
            self::Intermediate => 0.0,
            self::Advanced => 1.0,
        };
    }

    /**
     * Get the human readable name of the level.
     */
    public function label(): string
    {
        return match ($this) {
            self::Beginner => __('Beginner'),
            self::Intermediate => __('Intermediate'),
            self::Advanced => __('Advanced'),
        };
    }
}
