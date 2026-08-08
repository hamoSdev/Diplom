<?php

namespace Database\Seeders;

use App\Enums\CourseStatus;
use App\Enums\ExamStartLevel;
use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use App\Services\Exam\AdaptiveExamService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Isolated demo data so the student dashboard can be seen fully populated.
 *
 * Everything lives under a "Demo Faculty" and *@demo.local accounts, is created
 * idempotently (firstOrCreate / guarded inserts), and never touches real rows.
 * Remove it all with `php artisan demo:clear`.
 */
class DemoSeeder extends Seeder
{
    private const PASSWORD = 'password';

    private const WEAK_TOPIC = 'Advanced';

    public function run(): void
    {
        $faculty = Faculty::firstOrCreate(['name' => 'Demo Faculty']);

        User::firstOrCreate(
            ['email' => 'demo.admin@demo.local'],
            ['name' => 'Demo Admin', 'password' => self::PASSWORD, 'role' => UserRole::Admin],
        );

        $lecturer = User::firstOrCreate(
            ['email' => 'demo.lecturer@demo.local'],
            ['name' => 'Demo Lecturer', 'password' => self::PASSWORD, 'role' => UserRole::Lecturer],
        );

        $student = User::firstOrCreate(
            ['email' => 'demo.student@demo.local'],
            ['name' => 'Demo Student', 'password' => self::PASSWORD, 'role' => UserRole::Student, 'faculty_id' => $faculty->id],
        );

        if ($student->faculty_id !== $faculty->id) {
            $student->update(['faculty_id' => $faculty->id]);
        }

        $definitions = [
            ['subject' => 'Demo Web Development', 'title' => 'Demo — HTML & CSS Basics'],
            ['subject' => 'Demo Web Development', 'title' => 'Demo — JavaScript Essentials'],
            ['subject' => 'Demo Databases', 'title' => 'Demo — SQL Foundations'],
        ];

        $subjects = [];

        foreach ($definitions as $definition) {
            $subject = $subjects[$definition['subject']] ??= Subject::firstOrCreate(
                ['name' => $definition['subject'], 'faculty_id' => $faculty->id],
            );

            $lecturer->taughtSubjects()->syncWithoutDetaching([$subject->id]);

            $course = $this->course($subject, $lecturer, $definition['title']);
            $this->seedTopicsAndQuestions($course);
            $this->seedMaterial($course);
            $this->seedCourseExam($student, $course);
        }

        $this->seedLevelExam($student);
    }

    private function course(Subject $subject, User $lecturer, string $title): Course
    {
        return Course::firstOrCreate(
            ['title' => $title, 'subject_id' => $subject->id],
            [
                'lecturer_id' => $lecturer->id,
                'status' => CourseStatus::Ready,
                'content' => '<h2>'.$title.'</h2><p>Demo course content for preview.</p>',
            ],
        );
    }

    private function seedTopicsAndQuestions(Course $course): void
    {
        if ($course->questions()->exists()) {
            return;
        }

        $difficulties = [QuestionDifficulty::Easy, QuestionDifficulty::Medium, QuestionDifficulty::Hard];

        foreach (['Fundamentals', 'Practical', self::WEAK_TOPIC] as $position => $title) {
            $topic = $course->topics()->firstOrCreate(['title' => $title], ['position' => $position + 1]);

            foreach ($difficulties as $index => $difficulty) {
                $question = $course->questions()->create([
                    'topic_id' => $topic->id,
                    'type' => QuestionType::Single,
                    'difficulty' => $difficulty,
                    'body' => $title.' — question '.($index + 1).'?',
                ]);

                $question->options()->create(['body' => 'Correct answer', 'is_correct' => true, 'position' => 1]);
                $question->options()->create(['body' => 'Wrong answer A', 'is_correct' => false, 'position' => 2]);
                $question->options()->create(['body' => 'Wrong answer B', 'is_correct' => false, 'position' => 3]);
            }
        }
    }

    private function seedMaterial(Course $course): void
    {
        $path = 'course-materials/demo-course-'.$course->id.'.pdf';

        if (! Storage::exists($path)) {
            Storage::put($path, 'Demo material for '.$course->title);
        }

        $course->materials()->firstOrCreate(
            ['original_name' => 'Demo material.pdf'],
            ['path' => $path, 'mime_type' => 'application/pdf', 'size' => Storage::size($path)],
        );
    }

    /**
     * Run a finished course exam, deliberately failing the "weak" topic so the
     * dashboard's revise-topics widget has something to show.
     */
    private function seedCourseExam(User $student, Course $course): void
    {
        if ($student->examAttempts()->where('course_id', $course->id)->exists()) {
            return;
        }

        $exam = app(AdaptiveExamService::class);
        $attempt = $exam->start($student, ExamStartLevel::Intermediate, $course);
        $weakTopicId = $course->topics()->where('title', self::WEAK_TOPIC)->value('id');

        while ($question = $exam->nextQuestion($attempt)) {
            $wantCorrect = $question->topic_id !== $weakTopicId;
            $option = $question->options->firstWhere('is_correct', $wantCorrect) ?? $question->options->first();
            $exam->submitAnswer($attempt, $question, $option->id);
        }

        $exam->finish($attempt);
    }

    /**
     * Run a finished overall level exam (~2/3 correct) if the IT bank exists.
     */
    private function seedLevelExam(User $student): void
    {
        if (Question::where('category', 'IT')->doesntExist()
            || $student->examAttempts()->whereNull('course_id')->exists()) {
            return;
        }

        $exam = app(AdaptiveExamService::class);
        $attempt = $exam->start($student, ExamStartLevel::Intermediate);
        $index = 0;

        while ($question = $exam->nextQuestion($attempt)) {
            $wantCorrect = (++$index % 3) !== 0;
            $option = $question->options->firstWhere('is_correct', $wantCorrect) ?? $question->options->first();
            $exam->submitAnswer($attempt, $question, $option->id);
        }

        $exam->finish($attempt);
    }
}
