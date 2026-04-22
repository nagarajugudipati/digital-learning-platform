<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = User::where('role', 'teacher')->get()->keyBy('subject_specialization');

        $lessons = [
            // Mathematics
            [
                'subject' => 'Mathematics',
                'teacher_email' => 'teacher@nabha.edu',
                'items' => [
                    ['title' => 'Introduction to Algebra', 'class' => 'Class 8', 'desc' => 'Learn the fundamentals of algebra including variables, expressions, and simple equations. This lesson covers the NCERT Class 8 Maths Chapter 2 curriculum.'],
                    ['title' => 'Pythagoras Theorem & Applications', 'class' => 'Class 9', 'desc' => 'Comprehensive coverage of Pythagoras theorem with proofs, real-life applications, and practice problems from the NCERT Class 9 syllabus.'],
                    ['title' => 'Linear Equations in Two Variables', 'class' => 'Class 9', 'desc' => 'Understanding linear equations, their graphical representation, and solving systems of equations as per NCERT guidelines.'],
                    ['title' => 'Mensuration: Area and Volume', 'class' => 'Class 8', 'desc' => 'Calculating area, perimeter, and volume of 2D and 3D shapes. Includes problems on cylinders, cones, and spheres.'],
                    ['title' => 'Quadratic Equations', 'class' => 'Class 10', 'desc' => 'Solving quadratic equations by factorisation, completing the square, and using the quadratic formula. NCERT Class 10 Chapter 4.'],
                ]
            ],
            // Science
            [
                'subject' => 'Science',
                'teacher_email' => 'priya@nabha.edu',
                'items' => [
                    ['title' => 'Photosynthesis - How Plants Make Food', 'class' => 'Class 7', 'desc' => 'Complete explanation of photosynthesis process, chlorophyll, light reactions, and dark reactions. Includes diagrams and NCERT exercises.'],
                    ['title' => 'Newton\'s Laws of Motion', 'class' => 'Class 9', 'desc' => 'All three laws of Newton explained with real-life examples from Punjab\'s agricultural and industrial context. Numericals included.'],
                    ['title' => 'Chemical Reactions and Equations', 'class' => 'Class 10', 'desc' => 'Types of chemical reactions, balancing equations, oxidation and reduction. Practical experiments and NCERT solutions.'],
                    ['title' => 'The Cell - Fundamental Unit of Life', 'class' => 'Class 8', 'desc' => 'Structure and functions of plant and animal cells, cell organelles, and differences between prokaryotic and eukaryotic cells.'],
                ]
            ],
            // English
            [
                'subject' => 'English',
                'teacher_email' => 'gurpreet@nabha.edu',
                'items' => [
                    ['title' => 'English Grammar - Tenses', 'class' => 'Class 7', 'desc' => 'All 12 tenses explained with simple examples, rules, and exercises. Perfect for CBSE Board exam preparation.'],
                    ['title' => 'Writing Skills - Letter Writing', 'class' => 'Class 8', 'desc' => 'Formal and informal letter writing formats, with model letters and practice exercises aligned to CBSE pattern.'],
                    ['title' => 'The Road Not Taken - Poem Analysis', 'class' => 'Class 9', 'desc' => 'Line-by-line analysis of Robert Frost\'s poem, themes, literary devices, and important questions for exams.'],
                ]
            ],
            // Hindi
            [
                'subject' => 'Hindi',
                'teacher_email' => 'sunita@nabha.edu',
                'items' => [
                    ['title' => 'हिंदी व्याकरण - संज्ञा और सर्वनाम', 'class' => 'Class 6', 'desc' => 'संज्ञा के प्रकार, सर्वनाम का उपयोग, और व्याकरण के नियम। NCERT Class 6 Hindi के अनुसार।'],
                    ['title' => 'कबीर दास के दोहे', 'class' => 'Class 9', 'desc' => 'कबीर दास के प्रसिद्ध दोहों की व्याख्या, अर्थ, और परीक्षा के लिए महत्वपूर्ण प्रश्न।'],
                ]
            ],
            // Social Studies
            [
                'subject' => 'Social Studies',
                'teacher_email' => 'amrit@nabha.edu',
                'items' => [
                    ['title' => 'Indian Independence Movement', 'class' => 'Class 8', 'desc' => 'Journey from 1857 to 1947, major freedom fighters including Punjab\'s Bhagat Singh, Lala Lajpat Rai, and the role of ordinary people.'],
                    ['title' => 'The Indian Constitution', 'class' => 'Class 9', 'desc' => 'Preamble, Fundamental Rights, Directive Principles, and structure of Indian democracy. Important for civics examination.'],
                    ['title' => 'Geography of Punjab', 'class' => 'Class 7', 'desc' => 'Physical features, rivers, climate, agriculture, and important cities of Punjab. Special focus on Nabha and Patiala district.'],
                ]
            ],
        ];

        $admin = User::where('role', 'admin')->first();

        foreach ($lessons as $subjectGroup) {
            $teacher = User::where('email', $subjectGroup['teacher_email'])->first();
            if (!$teacher) continue;

            foreach ($subjectGroup['items'] as $item) {
                Lesson::create([
                    'teacher_id' => $teacher->id,
                    'title' => $item['title'],
                    'description' => $item['desc'],
                    'subject' => $subjectGroup['subject'],
                    'class_level' => $item['class'],
                    'file_type' => 'text',
                    'content' => $this->generateSampleContent($item['title'], $subjectGroup['subject']),
                    'status' => 'published',
                    'approved_by' => $admin->id,
                    'approved_at' => now(),
                    'view_count' => rand(10, 150),
                    'download_count' => rand(5, 50),
                ]);
            }
        }

        $this->command->info('✅ ' . Lesson::count() . ' sample lessons seeded!');
    }

    private function generateSampleContent(string $title, string $subject): string
    {
        return "# {$title}\n\n"
            . "## Learning Objectives\n"
            . "By the end of this lesson, students will be able to:\n"
            . "• Understand the core concepts of this topic\n"
            . "• Apply the knowledge to solve related problems\n"
            . "• Connect the topic to real-life situations in their environment\n\n"
            . "## Introduction\n"
            . "This lesson covers important concepts from the NCERT curriculum for {$subject}. "
            . "Read carefully and make notes as you go through the material.\n\n"
            . "## Main Content\n"
            . "This lesson content covers: {$title}\n\n"
            . "The study of {$subject} helps us understand the world around us. "
            . "In Nabha and throughout Punjab, these concepts apply to everyday life.\n\n"
            . "## Key Points to Remember\n"
            . "• Focus on understanding the core concept, not just memorizing\n"
            . "• Practice with examples from your textbook\n"
            . "• Ask your teacher if you have any doubts\n\n"
            . "## Practice Questions\n"
            . "1. Define the main concept discussed in this lesson.\n"
            . "2. Give two real-life examples related to this topic.\n"
            . "3. Solve the numerical problems given in your NCERT textbook.\n\n"
            . "## Summary\n"
            . "In this lesson, we covered the fundamental concepts of {$title}. "
            . "Practice the exercises in your notebook and take the quiz to test your understanding!\n\n"
            . "**Remember:** Consistent practice is the key to success. Study daily! 📚";
    }
}
