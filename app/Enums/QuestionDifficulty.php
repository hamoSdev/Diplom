<?php

namespace App\Enums;

enum QuestionDifficulty: string
{
    case Easy = 'easy';
    case Medium = 'medium';
    case Hard = 'hard';

    /**
     * Difficulty on the ability scale (b) used by the adaptive engine.
     */
    public function bValue(): float
    {
        return match ($this) {
            self::Easy => -1.0,
            self::Medium => 0.0,
            self::Hard => 1.0,
        };
    }

    /**
     * Weight of the question when computing the final 20-point score.
     */
    public function weight(): float
    {
        return match ($this) {
            self::Easy => 1.0,
            self::Medium => 1.5,
            self::Hard => 2.0,
        };
    }

    /**
     * Get the human readable name of the difficulty.
     */
    public function label(): string
    {
        return match ($this) {
            self::Easy => __('Easy'),
            self::Medium => __('Medium'),
            self::Hard => __('Hard'),
        };
    }
}
