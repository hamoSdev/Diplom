<?php

use App\Models\Course;
use App\Models\Topic;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Scope questions to a course and (optionally) a topic. The existing global
     * level-exam bank keeps both null and is still selected by `category`.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignIdFor(Course::class)->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Topic::class)->nullable()->after('course_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Course::class);
            $table->dropConstrainedForeignIdFor(Topic::class);
        });
    }
};
