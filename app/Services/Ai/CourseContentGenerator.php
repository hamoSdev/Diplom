<?php

namespace App\Services\Ai;

use App\Enums\CourseGenerationMode;
use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\Course;
use App\Models\Question;
use App\Models\Topic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * The AI authoring engine behind all three scenarios. It builds topics, writes
 * study material, and generates a difficulty-tagged question bank — writing into
 * the same tables the lecturer edits by hand, so the AI is just another author.
 * Scenario 3 additionally reads the lecturer's uploaded files and draws the
 * questions from that material.
 */
class CourseContentGenerator
{
    public function __construct(
        private readonly ClaudeClient $claude,
        private readonly MaterialTextExtractor $extractor,
    ) {}

    /**
     * Run the full pipeline for a course. Idempotent-ish: topics/questions are
     * only generated where missing, so a re-run fills gaps rather than dupes.
     */
    public function generate(Course $course): void
    {
        $course->loadMissing('subject:id,name', 'topics');

        // In "subject only" mode the AI outlines the topics; when the lecturer
        // provides them (Scenario 2) it only fills their material and questions.
        if ($course->generation_mode === CourseGenerationMode::SubjectOnly
            && $course->topics->isEmpty()) {
            $this->generateTopics($course);
            $course->load('topics');
        }

        foreach ($course->topics as $topic) {
            $this->generateForTopic($course, $topic);
        }

        if ($course->content === null || trim($course->content) === '') {
            $this->generateOverview($course);
        }
    }

    /**
     * Scenario 2: fill in one topic the lecturer authored — its study material
     * and its questions — leaving anything already present untouched.
     */
    public function generateForTopic(Course $course, Topic $topic): void
    {
        $course->loadMissing('subject:id,name');

        if ($topic->content === null || trim($topic->content) === '') {
            $this->generateTopicContent($course, $topic);
        }

        if ($topic->questions()->count() === 0) {
            $this->generateTopicQuestions($course, $topic);
        }
    }

    /**
     * Scenario 3: read the lecturer's uploaded files and draw a question bank
     * from that material only. The files themselves are never altered — the AI
     * just reads them. Questions are tagged with the closest existing topic when
     * the course has any.
     */
    public function generateFromMaterials(Course $course): void
    {
        $course->loadMissing('subject:id,name', 'topics', 'materials');

        $text = '';

        foreach ($course->materials as $material) {
            $chunk = $this->extractor->extract($material->path);

            if ($chunk !== '') {
                $text .= "\n\n".$chunk;
            }
        }

        $text = trim($text);

        if ($text === '') {
            throw new RuntimeException('No readable text found in the uploaded materials (PDF, DOCX or PPTX).');
        }

        // Keep the prompt bounded regardless of how large the files are.
        $text = mb_substr($text, 0, 24000);
        $count = max(5, min(20, $course->exam_length));
        $topics = $course->topics;
        $topicList = $topics->pluck('title')->implode(' | ');

        $questions = $this->claude->json(
            "You are an exam author. Using ONLY the study material provided, return a JSON array of exactly {$count} exam questions. "
            .'Each item: {"type":"single"|"multiple"|"boolean"|"open","difficulty":"easy"|"medium"|"hard","body":string,"explanation":string,'
            .'"options":[{"body":string,"is_correct":boolean}],"expected_answer":string,"topic":string}. '
            .($topics->isNotEmpty()
                ? "Set \"topic\" to the closest of these titles: {$topicList}. "
                : 'Omit the "topic" field. ')
            .'For "single" 3-4 options with one correct; "multiple" 4-5 options with two or more correct; '
            .'"boolean" options "True"/"False" with one correct; "open" no options but an "expected_answer". '
            .'Do not invent facts beyond the material. Vary type and difficulty.',
            "Study material:\n".$text,
        );

        foreach ((array) $questions as $item) {
            $topicId = $this->matchTopic($topics, is_array($item) ? ($item['topic'] ?? null) : null);
            $this->storeQuestion($course, $topicId, is_array($item) ? $item : []);
        }
    }

    /**
     * Resolve a model-provided topic title to one of the course's topic ids.
     *
     * @param  Collection<int, Topic>  $topics
     */
    private function matchTopic(Collection $topics, ?string $title): ?int
    {
        if (! is_string($title) || trim($title) === '') {
            return null;
        }

        $needle = mb_strtolower(trim($title));

        foreach ($topics as $topic) {
            if (mb_strtolower($topic->title) === $needle) {
                return $topic->id;
            }
        }

        return null;
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
            .'Each item: {"type":"single"|"multiple"|"boolean"|"open","difficulty":"easy"|"medium"|"hard","body":string,"explanation":string,'
            .'"options":[{"body":string,"is_correct":boolean}],"expected_answer":string}. '
            .'For "single" give 3-4 options with exactly one correct. '
            .'For "multiple" give 4-5 options with two or more correct. '
            .'For "boolean" give exactly two options "True" and "False" with one correct. '
            .'For "open" omit options and instead give a concise "expected_answer" model answer; no options. '
            .'Vary the type and difficulty across the set.',
            $this->context($course)."\nTopic: {$topic->title}",
        );

        foreach ((array) $questions as $item) {
            $this->storeQuestion($course, $topic->id, is_array($item) ? $item : []);
        }
    }

    /**
     * Persist one AI-authored question, guarding against malformed payloads.
     *
     * @param  array<string, mixed>  $item
     */
    private function storeQuestion(Course $course, ?int $topicId, array $item): void
    {
        $body = is_string($item['body'] ?? null) ? trim($item['body']) : '';

        if ($body === '') {
            return;
        }

        $type = QuestionType::tryFrom($item['type'] ?? '') ?? QuestionType::Single;
        $difficulty = QuestionDifficulty::tryFrom($item['difficulty'] ?? '') ?? QuestionDifficulty::Medium;
        $options = is_array($item['options'] ?? null) ? $item['options'] : [];

        // Option questions need a usable set; open questions need none.
        if ($type->hasOptions() && count($options) < 2) {
            return;
        }

        DB::transaction(function () use ($course, $topicId, $item, $body, $options, $type, $difficulty): void {
            /** @var Question $question */
            $question = $course->questions()->create([
                'topic_id' => $topicId,
                'type' => $type->value,
                'difficulty' => $difficulty->value,
                'body' => mb_substr($body, 0, 1000),
                'explanation' => is_string($item['explanation'] ?? null) ? $item['explanation'] : null,
                'expected_answer' => $type === QuestionType::Open && is_string($item['expected_answer'] ?? null)
                    ? $item['expected_answer']
                    : null,
            ]);

            if (! $type->hasOptions()) {
                return;
            }

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

            // Never leave an option question without a correct answer.
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
