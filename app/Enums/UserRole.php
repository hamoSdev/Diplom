<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Lecturer = 'lecturer';
    case Student = 'student';

    /**
     * Get the human readable name of the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => __('Administrator'),
            self::Lecturer => __('Lecturer'),
            self::Student => __('Student'),
        };
    }
}
