<?php

namespace Database\Seeders;

use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\Question;
use Illuminate\Database\Seeder;

/**
 * A hardcoded IT question bank for the adaptive level exam (AI generation is v2).
 * Each entry: difficulty, type, body, optional explanation, and options where
 * the second value marks the correct one. Re-running replaces the IT bank.
 */
class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        Question::where('category', 'IT')->delete();

        foreach ($this->questions() as $data) {
            $question = Question::create([
                'category' => 'IT',
                'difficulty' => $data['difficulty'],
                'type' => $data['type'],
                'body' => $data['body'],
                'explanation' => $data['explanation'] ?? null,
            ]);

            foreach ($data['options'] as $position => [$body, $isCorrect]) {
                $question->options()->create([
                    'body' => $body,
                    'is_correct' => $isCorrect,
                    'position' => $position + 1,
                ]);
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function questions(): array
    {
        $E = QuestionDifficulty::Easy;
        $M = QuestionDifficulty::Medium;
        $H = QuestionDifficulty::Hard;
        $single = QuestionType::Single;
        $bool = QuestionType::Boolean;

        return [
            // Easy
            ['difficulty' => $E, 'type' => $single, 'body' => 'What does CPU stand for?', 'options' => [
                ['Central Processing Unit', true], ['Computer Personal Unit', false],
                ['Central Program Utility', false], ['Core Processing Item', false],
            ]],
            ['difficulty' => $E, 'type' => $bool, 'body' => 'HTML is a programming language.', 'explanation' => 'HTML is a markup language, not a programming language.', 'options' => [
                ['True', false], ['False', true],
            ]],
            ['difficulty' => $E, 'type' => $single, 'body' => 'Which symbol starts a variable in PHP?', 'options' => [
                ['$', true], ['@', false], ['#', false], ['&', false],
            ]],
            ['difficulty' => $E, 'type' => $single, 'body' => 'What does "HTTP" stand for?', 'options' => [
                ['HyperText Transfer Protocol', true], ['High Transfer Text Protocol', false],
                ['HyperText Transmission Process', false], ['Home Tool Transfer Protocol', false],
            ]],
            ['difficulty' => $E, 'type' => $bool, 'body' => 'RAM loses its data when the computer is turned off.', 'options' => [
                ['True', true], ['False', false],
            ]],
            ['difficulty' => $E, 'type' => $single, 'body' => 'Which company develops Windows?', 'options' => [
                ['Microsoft', true], ['Apple', false], ['Google', false], ['IBM', false],
            ]],
            ['difficulty' => $E, 'type' => $single, 'body' => 'Which of these is a web browser?', 'options' => [
                ['Firefox', true], ['Linux', false], ['Photoshop', false], ['MySQL', false],
            ]],

            // Medium
            ['difficulty' => $M, 'type' => $single, 'body' => 'Which data structure works on a FIFO principle?', 'options' => [
                ['Queue', true], ['Stack', false], ['Tree', false], ['Graph', false],
            ]],
            ['difficulty' => $M, 'type' => $bool, 'body' => 'In OOP, encapsulation means hiding an object\'s internal state.', 'options' => [
                ['True', true], ['False', false],
            ]],
            ['difficulty' => $M, 'type' => $single, 'body' => 'What is the time complexity of binary search?', 'options' => [
                ['O(log n)', true], ['O(n)', false], ['O(n^2)', false], ['O(1)', false],
            ]],
            ['difficulty' => $M, 'type' => $single, 'body' => 'Which HTTP status code means "Not Found"?', 'options' => [
                ['404', true], ['200', false], ['500', false], ['301', false],
            ]],
            ['difficulty' => $M, 'type' => $bool, 'body' => 'TCP is a connectionless protocol.', 'explanation' => 'TCP is connection-oriented; UDP is connectionless.', 'options' => [
                ['True', false], ['False', true],
            ]],
            ['difficulty' => $M, 'type' => $single, 'body' => 'What does "API" stand for?', 'options' => [
                ['Application Programming Interface', true], ['Applied Program Instruction', false],
                ['Automated Process Integration', false], ['Advanced Programming Input', false],
            ]],
            ['difficulty' => $M, 'type' => $single, 'body' => 'Which SQL clause is used to filter rows?', 'options' => [
                ['WHERE', true], ['ORDER BY', false], ['GROUP BY', false], ['HAVING', false],
            ]],

            // Hard
            ['difficulty' => $H, 'type' => $single, 'body' => 'Which algorithm finds shortest paths from a single source with non-negative weights?', 'options' => [
                ['Dijkstra', true], ['Kruskal', false], ['Prim', false], ['Floyd-Warshall', false],
            ]],
            ['difficulty' => $H, 'type' => $bool, 'body' => 'A hash table has O(1) average-case lookup time.', 'options' => [
                ['True', true], ['False', false],
            ]],
            ['difficulty' => $H, 'type' => $single, 'body' => 'In the CAP theorem, what does "C" stand for?', 'options' => [
                ['Consistency', true], ['Concurrency', false], ['Caching', false], ['Capacity', false],
            ]],
            ['difficulty' => $H, 'type' => $single, 'body' => 'Which normal form removes transitive dependencies?', 'options' => [
                ['Third Normal Form (3NF)', true], ['First Normal Form (1NF)', false],
                ['Second Normal Form (2NF)', false], ['Zero Normal Form', false],
            ]],
            ['difficulty' => $H, 'type' => $bool, 'body' => 'Deadlock requires the "circular wait" condition to occur.', 'options' => [
                ['True', true], ['False', false],
            ]],
            ['difficulty' => $H, 'type' => $single, 'body' => 'What is the worst-case time complexity of quicksort?', 'options' => [
                ['O(n^2)', true], ['O(n log n)', false], ['O(n)', false], ['O(log n)', false],
            ]],
            ['difficulty' => $H, 'type' => $single, 'body' => 'Which design pattern ensures a class has only one instance?', 'options' => [
                ['Singleton', true], ['Factory', false], ['Observer', false], ['Strategy', false],
            ]],
        ];
    }
}
