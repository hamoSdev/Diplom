<?php

namespace App\Models;

use App\Enums\CourseGenerationMode;
use App\Enums\CourseStatus;
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $lecturer_id
 * @property int|null $subject_id
 * @property string $title
 * @property string|null $description
 * @property CourseGenerationMode $generation_mode
 * @property CourseStatus $status
 * @property int $exam_length
 * @property int $questions_per_topic
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['title', 'description', 'subject_id', 'lecturer_id', 'generation_mode', 'exam_length', 'questions_per_topic'])]
class Course extends Model
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory;

    /**
     * Default in-memory values for a fresh course (mirrors the DB defaults).
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'status' => CourseStatus::Draft->value,
        'generation_mode' => CourseGenerationMode::SubjectOnly->value,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'generation_mode' => CourseGenerationMode::class,
            'status' => CourseStatus::class,
        ];
    }

    /**
     * The lecturer who owns the course.
     *
     * @return BelongsTo<User, $this>
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * The subject this course is taught for.
     *
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
