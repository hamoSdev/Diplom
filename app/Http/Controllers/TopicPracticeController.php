<?php

namespace App\Http\Controllers;

use App\Enums\QuestionType;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Topic;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * A self-check practice over a single topic's questions. Unlike the graded
 * exam, this is a study aid the student can retry freely: the option questions
 * are shown with their answers so the page can give instant feedback. Open
 * questions are left out — they belong to the graded, AI-marked exam.
 */
class TopicPracticeController extends Controller
{
    public function show(Request $request, Topic $topic): Response
    {
        $topic->load('course.subject:id,faculty_id');
        $course = $topic->course;

        abort_unless(
            $course->subject !== null
                && $course->subject->faculty_id === $request->user()->faculty_id,
            403,
        );

        $questions = $topic->questions()
            ->where('type', '!=', QuestionType::Open->value)
            ->with('options')
            ->get()
            ->map(fn (Question $question): array => [
                'id' => $question->id,
                'body' => $question->body,
                'type' => $question->type->value,
                'explanation' => $question->explanation,
                'options' => $question->options->map(fn (QuestionOption $option): array => [
                    'id' => $option->id,
                    'body' => $option->body,
                    'is_correct' => $option->is_correct,
                ]),
            ]);

        return Inertia::render('student/practice/Topic', [
            'topic' => ['id' => $topic->id, 'title' => $topic->title],
            'course' => ['id' => $course->id, 'title' => $course->title],
            'questions' => $questions,
        ]);
    }
}
