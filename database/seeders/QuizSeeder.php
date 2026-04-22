<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $mathTeacher = User::where('email', 'teacher@nabha.edu')->first();
        $scienceTeacher = User::where('email', 'priya@nabha.edu')->first();
        $englishTeacher = User::where('email', 'gurpreet@nabha.edu')->first();

        $quizzes = [
            // Mathematics Quiz - Class 9
            [
                'teacher' => $mathTeacher,
                'data' => [
                    'title' => 'Algebra Basics Quiz',
                    'subject' => 'Mathematics',
                    'class_level' => 'Class 9',
                    'description' => 'Test your understanding of basic algebra concepts',
                    'time_limit' => 20,
                    'passing_marks' => 40,
                    'max_attempts' => 3,
                    'status' => 'active',
                ],
                'questions' => [
                    ['q' => 'If 2x + 5 = 13, what is the value of x?', 'a' => '3', 'b' => '4', 'c' => '5', 'd' => '6', 'ans' => 'b', 'exp' => '2x = 13-5 = 8, so x = 8/2 = 4'],
                    ['q' => 'What is the value of x in 3x - 9 = 0?', 'a' => '1', 'b' => '2', 'c' => '3', 'd' => '4', 'ans' => 'c', 'exp' => '3x = 9, so x = 3'],
                    ['q' => 'Simplify: 3a + 2b - a + 4b', 'a' => '2a + 6b', 'b' => '4a + 6b', 'c' => '2a + 2b', 'd' => '3a + 4b', 'ans' => 'a', 'exp' => '(3a-a) + (2b+4b) = 2a + 6b'],
                    ['q' => 'What is (a+b)² equal to?', 'a' => 'a² + b²', 'b' => 'a² - 2ab + b²', 'c' => 'a² + 2ab + b²', 'd' => '2a² + 2b²', 'ans' => 'c', 'exp' => '(a+b)² = a² + 2ab + b²'],
                    ['q' => 'Which is a linear equation?', 'a' => 'x² = 9', 'b' => '2x + 3 = 7', 'c' => 'x³ = 8', 'd' => 'x² + x = 6', 'ans' => 'b', 'exp' => 'A linear equation has the highest power of variable as 1'],
                ]
            ],
            // Science Quiz - Class 9
            [
                'teacher' => $scienceTeacher,
                'data' => [
                    'title' => 'Newton\'s Laws Quiz',
                    'subject' => 'Science',
                    'class_level' => 'Class 9',
                    'description' => 'Test your knowledge of Newton\'s three laws of motion',
                    'time_limit' => 20,
                    'passing_marks' => 40,
                    'max_attempts' => 3,
                    'status' => 'active',
                ],
                'questions' => [
                    ['q' => 'Newton\'s First Law is also known as the Law of:', 'a' => 'Acceleration', 'b' => 'Inertia', 'c' => 'Gravitation', 'd' => 'Action-Reaction', 'ans' => 'b', 'exp' => 'Newton\'s First Law is called the Law of Inertia - objects resist changes in motion'],
                    ['q' => 'Force = Mass × Acceleration is Newton\'s:', 'a' => 'First Law', 'b' => 'Second Law', 'c' => 'Third Law', 'd' => 'Law of Gravitation', 'ans' => 'b', 'exp' => 'F = ma is Newton\'s Second Law of Motion'],
                    ['q' => 'The unit of Force is:', 'a' => 'Joule', 'b' => 'Watt', 'c' => 'Newton', 'd' => 'Pascal', 'ans' => 'c', 'exp' => 'Force is measured in Newton (N)'],
                    ['q' => 'If a 5 kg object has acceleration of 3 m/s², the force is:', 'a' => '5 N', 'b' => '10 N', 'c' => '15 N', 'd' => '20 N', 'ans' => 'c', 'exp' => 'F = ma = 5 × 3 = 15 N'],
                    ['q' => '"For every action there is equal and opposite reaction" - this is Newton\'s:', 'a' => 'First Law', 'b' => 'Second Law', 'c' => 'Third Law', 'd' => 'Fourth Law', 'ans' => 'c', 'exp' => 'Newton\'s Third Law: Action = Reaction (equal and opposite)'],
                    ['q' => 'Why does a rocket fly upward when gases exit downward?', 'a' => 'First Law', 'b' => 'Second Law', 'c' => 'Third Law', 'd' => 'None of these', 'ans' => 'c', 'exp' => 'Rocket propulsion is based on Newton\'s Third Law - action (gas downward) and reaction (rocket upward)'],
                ]
            ],
            // English Quiz - Class 8
            [
                'teacher' => $englishTeacher,
                'data' => [
                    'title' => 'English Tenses Quiz',
                    'subject' => 'English',
                    'class_level' => 'Class 8',
                    'description' => 'Test your knowledge of English tenses',
                    'time_limit' => 15,
                    'passing_marks' => 50,
                    'max_attempts' => 3,
                    'status' => 'active',
                ],
                'questions' => [
                    ['q' => 'Which sentence is in Simple Past Tense?', 'a' => 'She is going to school', 'b' => 'She went to school', 'c' => 'She will go to school', 'd' => 'She goes to school', 'ans' => 'b', 'exp' => 'Simple Past Tense uses V2 form of verb (went = past of go)'],
                    ['q' => '"I am eating lunch." is in which tense?', 'a' => 'Simple Present', 'b' => 'Past Continuous', 'c' => 'Present Continuous', 'd' => 'Future Simple', 'ans' => 'c', 'exp' => 'am/is/are + V-ing = Present Continuous Tense'],
                    ['q' => 'The correct past tense of "write" is:', 'a' => 'writed', 'b' => 'written', 'c' => 'wrote', 'd' => 'writ', 'ans' => 'c', 'exp' => 'Write - Wrote - Written (irregular verb)'],
                    ['q' => 'Which is correct? "She ___ to Delhi tomorrow."', 'a' => 'go', 'b' => 'goes', 'c' => 'went', 'd' => 'will go', 'ans' => 'd', 'exp' => 'For future actions, we use "will + base verb"'],
                ]
            ],
        ];

        foreach ($quizzes as $quizData) {
            if (!$quizData['teacher']) continue;

            $totalMarks = array_sum(array_column($quizData['questions'], 'marks', null)) ?: count($quizData['questions']);

            $quiz = Quiz::create(array_merge($quizData['data'], [
                'teacher_id' => $quizData['teacher']->id,
                'total_marks' => count($quizData['questions']),
            ]));

            foreach ($quizData['questions'] as $index => $q) {
                Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $q['q'],
                    'option_a' => $q['a'],
                    'option_b' => $q['b'],
                    'option_c' => $q['c'],
                    'option_d' => $q['d'],
                    'correct_answer' => $q['ans'],
                    'explanation' => $q['exp'],
                    'marks' => 1,
                    'order' => $index + 1,
                ]);
            }
        }

        $this->command->info('✅ ' . Quiz::count() . ' sample quizzes seeded with questions!');
    }
}
