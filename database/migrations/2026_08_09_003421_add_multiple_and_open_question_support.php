<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Model answer / grading guidance for open questions.
            $table->text('expected_answer')->nullable()->after('explanation');
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            // Chosen option ids for multiple-choice answers, and free text for
            // open answers. selected_option_id stays for single/true-false.
            $table->json('selected_option_ids')->nullable()->after('selected_option_id');
            $table->text('answer_text')->nullable()->after('selected_option_ids');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('expected_answer');
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropColumn(['selected_option_ids', 'answer_text']);
        });
    }
};
