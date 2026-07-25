<?php

namespace App\Models;

use Database\Factories\FacultyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name'])]
class Faculty extends Model
{
    /** @use HasFactory<FacultyFactory> */
    use HasFactory;

    /**
     * Subjects that belong to this faculty.
     *
     * @return HasMany<Subject, $this>
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Students enrolled in this faculty.
     *
     * @return HasMany<User, $this>
     */
    public function students(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
