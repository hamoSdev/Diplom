<?php

use App\Models\Course;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Scope an attempt to a course (null = the overall level exam) and fix its
     * planned number of questions at start time.
     */
    public function up(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->foreignIdFor(Course::class)->nullable()->after('student_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('length')->default(15)->after('ability');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Course::class);
            $table->dropColumn('length');
        });
    }
};
