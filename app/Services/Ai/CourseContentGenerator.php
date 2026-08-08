<?php

namespace App\Services\Ai;

use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\Course;
use App\Models\Question;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

/**
 * Scenario 1 of the project: given a course (optionally only a subject/title),
 * the AI builds the topics, writes short study material for each, and generates
 * a question bank tagged by difficulty. Everything is written into the same
 * tables the lecturer edits by hand, so the AI is just another author.
 */
class CourseContentGenerator
{
    public function __construct(private readonly ClaudeClient $claude) {}

    /**
     * Run the full pipeline for a course. Idempotent-ish: topics/questions are
     * only generated where missing, so a re-run fills gaps rather than dupes.
     */
    public function generate(Course $course): void
    {
        $course->loadMissing('subject:id,name', 'topics');

        if ($course->topics->isEmpty()) {
            $this->generateTopics($course);
            $course->load('topics');
        }

        foreach ($course->topics as $topic) {
            if ($topic->content === null || trim($topic->content) === '') {
                $this->generateTopicContent($course, $topic);
            }

            if ($topic->questions()->count() === 0) {
                $this->generateTopicQuestions($course, $topic);
            }
        }

        if ($course->content === null || trim($course->content) === '') {
            $this->generateOverview($course);
        }
    }

    /**
     * Ask for the course outline and persist it as ordered topics.
     */
    private function generateTopics(Course $course): void
    {
        $titles = $this->claude->json(
            'You are a university curriculum designer. Return a JSON array of 5 to 8 concise topic titles (plain strings, no numbering) that structure the given course from basics to advanced.',
            $this->context($course),
        );

        $position = 0;

        foreach ((array) $titles as $title) {
            if (! is_string($title) || trim($title) === '') {
                continue;
            }

            $course->topics()->create([
                'title' => mb_substr(trim($title), 0, 255),
                'position' => ++$position,
            ]);
        }
    }

    /**
     * Write short HTML study material for one topic.
     */
    private function generateTopicContent(Course $course, Topic $topic): void
    {
        $data = $this->claude->json(
            'You are a lecturer writing concise study material. Return a JSON object {"content": "<html>"} where content is a 200-350 word explanation in simple semantic HTML using only <h3>, <p>, <ul>, <li>, <strong>, <code> tags. No scripts, no styles, no images.',
            $this->context($course)."\nTopic: {$topic->title}",
        );

        $html = is_array($data) ? ($data['content'] ?? null) : null;

        if (is_string($html) && trim($html) !== '') {
            $topic->update(['content' => clean($html, 'course')]);
        }
    }

    /**
     * Generate the topic's questions with options and difficulty.
     */
    private function generateTopicQuestions(Course $course, Topic $topic): void
    {
        $count = max(1, $course->questions_per_topic);

        $questions = $this->claude->json(
            "You are an exam author. Return a JSON array of exactly {$count} exam questions for the topic. "
            .'Each item: {"type":"single"|"boolean","difficulty":"easy"|"medium"|"hard","body":string,"explanation":string,'
            .'"options":[{"body":string,"is_correct":boolean}]}. '
            .'For "single" give 3-4 options with exactly one correct. For "boolean" give exactly two options "True" and "False" with one correct. '
            .'Vary the difficulty across the set.',
            $this->context($course)."\nTopic: {$topic->title}",
        );

        foreach ((array) $questions as $item) {
            $this->storeQuestion($course, $topic, is_array($item) ? $item : []);
        }
    }

    /**
     * Persist one AI-authored question, guarding against malformed payloads.
     *
     * @param  array<string, mixed>  $item
     */
    private function storeQuestion(Course $course, Topic $topic, array $item): void
    {
        $body = is_string($item['body'] ?? null) ? trim($item['body']) : '';
        $options = is_array($item['options'] ?? null) ? $item['options'] : [];

        if ($body === '' || count($options) < 2) {
            return;
        }

        $type = QuestionType::tryFrom($item['type'] ?? '') ?? QuestionType::Single;
        $difficulty = QuestionDifficulty::tryFrom($item['difficulty'] ?? '') ?? QuestionDifficulty::Medium;

        DB::transaction(function () use ($course, $topic, $item, $body, $options, $type, $difficulty): void {
            /** @var Question $question */
            $question = $course->questions()->create([
                'topic_id' => $topic->id,
                'type' => $type->value,
                'difficulty' => $difficulty->value,
                'body' => mb_substr($body, 0, 1000),
                'explanation' => is_string($item['explanation'] ?? null) ? $item['explanation'] : null,
            ]);

            $hasCorrect = false;
            $position = 0;

            foreach ($options as $option) {
                if (! is_array($option) || ! is_string($option['body'] ?? null)) {
                    continue;
                }

                $isCorrect = (bool) ($option['is_correct'] ?? false);
                $hasCorrect = $hasCorrect || $isCorrect;

                $question->options()->create([
                    'body' => mb_substr(trim($option['body']), 0, 500),
                    'is_correct' => $isCorrect,
                    'position' => ++$position,
                ]);
            }

            // Never leave a question without a correct answer.
            if (! $hasCorrect) {
                $question->options()->orderBy('position')->first()?->update(['is_correct' => true]);
            }
        });
    }

    /**
     * Write a short course overview into the course's own content field.
     */
    private function generateOverview(Course $course): void
    {
        $topics = $course->topics->pluck('title')->implode(', ');

        $data = $this->claude->json(
            'You are a lecturer writing a course introduction. Return a JSON object {"content": "<html>"} with a 120-200 word overview in simple HTML (<h3>, <p>, <ul>, <li>). No scripts or styles.',
            $this->context($course)."\nTopics covered: {$topics}",
        );

        $html = is_array($data) ? ($data['content'] ?? null) : null;

        if (is_string($html) && trim($html) !== '') {
            $course->update(['content' => clean($html, 'course')]);
        }
    }

    /**
     * Shared context line describing the course to the model.
     */
    private function context(Course $course): string
    {
        $subject = $course->subject_id === null ? 'General' : $course->subject->name;

        return "Subject: {$subject}\nCourse: {$course->title}";
    }
}
