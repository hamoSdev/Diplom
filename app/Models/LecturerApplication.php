<?php

namespace App\Models;

use App\Enums\LecturerApplicationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $message
 * @property string $cv_path
 * @property string $cv_original_name
 * @property LecturerApplicationStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'message', 'cv_path', 'cv_original_name', 'status'])]
class LecturerApplication extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => LecturerApplicationStatus::class,
        ];
    }
}
