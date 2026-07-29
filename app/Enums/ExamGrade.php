<?php

namespace App\Enums;

enum ExamGrade: string
{
    case Excellent = 'excellent';
    case Good = 'good';
    case Satisfactory = 'satisfactory';
    case Unsatisfactory = 'unsatisfactory';

    /**
     * Map a 0-20 score to a grade band.
     */
    public static function fromScore(int $score): self
    {
        return match (true) {
            $score >= 18 => self::Excellent,
            $score >= 13 => self::Good,
            $score >= 8 => self::Satisfactory,
            default => self::Unsatisfactory,
        };
    }

    /**
     * Get the human readable name of the grade.
     */
    public function label(): string
    {
        return match ($this) {
            self::Excellent => __('Excellent'),
            self::Good => __('Good'),
            self::Satisfactory => __('Satisfactory'),
            self::Unsatisfactory => __('Unsatisfactory'),
        };
    }
}
