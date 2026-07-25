<?php

use App\Enums\CourseGenerationMode;
use App\Enums\CourseStatus;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'lecturer_id')->constrained()->cascadeOnDelete();
            $table->string('title')->index();
            $table->text('description')->nullable();
            $table->string('generation_mode', 20)->default(CourseGenerationMode::SubjectOnly->value);
            $table->string('status', 20)->default(CourseStatus::Draft->value)->index();
            $table->unsignedTinyInteger('exam_length')->default(20);
            $table->unsignedTinyInteger('questions_per_topic')->default(10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
