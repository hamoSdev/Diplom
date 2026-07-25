<?php

namespace App\Enums;

enum CourseStatus: string
{
    case Draft = 'draft';
    case Generating = 'generating';
    case Ready = 'ready';
    case Failed = 'failed';

    /**
     * Get the human readable name of the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => __('Draft'),
            self::Generating => __('Generating'),
            self::Ready => __('Ready'),
            self::Failed => __('Failed'),
        };
    }
}
