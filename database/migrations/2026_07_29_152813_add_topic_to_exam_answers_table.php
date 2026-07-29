<?php

use App\Models\Topic;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Snapshot the question's topic on each answer, so per-topic analysis
     * survives later edits to the question bank (like difficulty_at_time).
     */
    public function up(): void
    {
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->foreignIdFor(Topic::class)->nullable()->after('question_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Topic::class);
        });
    }
};
