<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\TeacherApprovalController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ChatbotQaController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\QuizAttemptController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\StudentCourseController;
use App\Http\Controllers\Teacher\AnalyticsController;
use App\Http\Controllers\Teacher\CourseController;
use App\Http\Controllers\Teacher\LessonController;
use App\Http\Controllers\Teacher\ProfileController as TeacherProfileController;
use App\Http\Controllers\Teacher\QuizController;
use App\Http\Controllers\Teacher\ReportController as TeacherReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return match(auth()->user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            default   => redirect()->route('student.dashboard'),
        };
    }
    return redirect()->route('login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');

    // Profile
    Route::get('/profile', [StudentProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [StudentProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [StudentProfileController::class, 'updatePassword'])->name('profile.password');

    // Courses
    Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses');
    Route::get('/courses/my-courses', [StudentCourseController::class, 'myCourses'])->name('my-courses');
    Route::get('/courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');
    Route::post('/courses/{course}/enroll', [StudentCourseController::class, 'enroll'])->name('courses.enroll');
    Route::post('/courses/{course}/purchase', [StudentCourseController::class, 'purchase'])->name('courses.purchase');
    Route::get('/courses/{course}/lessons/{lesson}', [StudentCourseController::class, 'lesson'])->name('courses.lesson');
    Route::post('/courses/{course}/lessons/{lesson}/complete', [StudentCourseController::class, 'completeLesson'])->name('courses.lesson.complete');

    // Cart
    Route::get('/cart', [StudentCourseController::class, 'viewCart'])->name('cart');
    Route::post('/cart/{course}/add', [StudentCourseController::class, 'addToCart'])->name('cart.add');
    Route::delete('/cart/{course}/remove', [StudentCourseController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/checkout', [StudentCourseController::class, 'checkout'])->name('cart.checkout');

    // Lessons (standalone)
    Route::get('/lessons', [StudentController::class, 'lessons'])->name('lessons');
    Route::get('/lessons/{lesson}', [StudentController::class, 'showLesson'])->name('lesson.show');
    Route::post('/lessons/{lesson}/complete', [StudentController::class, 'completeLesson'])->name('lesson.complete');
    Route::get('/lessons/{lesson}/download', [StudentController::class, 'downloadLesson'])->name('lesson.download');

    // Quizzes
    Route::get('/quizzes', [QuizAttemptController::class, 'index'])->name('quizzes');
    Route::get('/quizzes/{quiz}/start', [QuizAttemptController::class, 'startQuiz'])->name('quiz.start');
    Route::post('/quizzes/{quiz}/submit', [QuizAttemptController::class, 'submitQuiz'])->name('quiz.submit');
    Route::get('/quiz-result/{attempt}', [QuizAttemptController::class, 'result'])->name('quiz.result');

    // Chatbot
    Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot');
    Route::post('/chatbot/chat', [ChatbotController::class, 'chat'])->name('chatbot.chat');
    Route::post('/chatbot/feedback/{log}', [ChatbotController::class, 'feedback'])->name('chatbot.feedback');
});

// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', function () {
        $teacher = auth()->user();
        $lessonsCount   = \App\Models\Lesson::where('teacher_id', $teacher->id)->count();
        $quizzesCount   = \App\Models\Quiz::where('teacher_id', $teacher->id)->count();
        $studentsReached = \App\Models\ProgressReport::whereIn('lesson_id',
            \App\Models\Lesson::where('teacher_id', $teacher->id)->pluck('id')
        )->distinct('student_id')->count();
        return view('teacher.dashboard', compact('teacher', 'lessonsCount', 'quizzesCount', 'studentsReached'));
    })->name('dashboard');

    // Profile
    Route::get('/profile', [TeacherProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [TeacherProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [TeacherProfileController::class, 'updatePassword'])->name('profile.password');

    // Reports
    Route::get('/reports', [TeacherReportController::class, 'index'])->name('reports');

    // Chatbot Q&A training
    Route::get('/chatbot-qa', [ChatbotQaController::class, 'index'])->name('chatbot-qa');
    Route::post('/chatbot-qa', [ChatbotQaController::class, 'store'])->name('chatbot-qa.store');
    Route::put('/chatbot-qa/{qa}', [ChatbotQaController::class, 'update'])->name('chatbot-qa.update');
    Route::delete('/chatbot-qa/{qa}', [ChatbotQaController::class, 'destroy'])->name('chatbot-qa.destroy');

    // Lessons
    Route::get('/lessons', [LessonController::class, 'index'])->name('lessons');
    Route::get('/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
    Route::get('/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');

    // Quizzes
    Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes');
    Route::get('/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
    Route::post('/quizzes', [QuizController::class, 'store'])->name('quizzes.store');
    Route::patch('/quizzes/{quiz}/toggle-status', [QuizController::class, 'toggleStatus'])->name('quizzes.toggle');
    Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/student-progress', [AnalyticsController::class, 'studentProgress'])->name('student.progress');
    Route::get('/student-progress/export', [AnalyticsController::class, 'exportCsv'])->name('student.progress.export');

    // Course management
    Route::get('/courses', [CourseController::class, 'index'])->name('courses');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::post('/courses/{course}/submit', [CourseController::class, 'submit'])->name('courses.submit');
    Route::get('/courses/{course}/add-lesson', [CourseController::class, 'addLesson'])->name('courses.add-lesson');
    Route::post('/courses/{course}/add-lesson', [CourseController::class, 'storeLesson'])->name('courses.store-lesson');
    Route::delete('/courses/{course}/lessons/{lesson}', [CourseController::class, 'destroyLesson'])->name('courses.destroy-lesson');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');

    // Teacher approval
    Route::get('/teachers', [TeacherApprovalController::class, 'index'])->name('teachers');
    Route::patch('/teachers/{user}/approve', [TeacherApprovalController::class, 'approve'])->name('teachers.approve');
    Route::patch('/teachers/{user}/reject', [TeacherApprovalController::class, 'reject'])->name('teachers.reject');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Content
    Route::get('/content', [ContentController::class, 'index'])->name('content');
    Route::get('/content/{lesson}/preview', [ContentController::class, 'preview'])->name('content.preview');
    Route::patch('/content/{lesson}/approve', [ContentController::class, 'approve'])->name('content.approve');
    Route::patch('/content/{lesson}/reject', [ContentController::class, 'reject'])->name('content.reject');
    Route::delete('/content/{lesson}', [ContentController::class, 'destroy'])->name('content.destroy');

    // Chatbot Q&A training
    Route::get('/chatbot-qa', [ChatbotQaController::class, 'index'])->name('chatbot-qa');
    Route::post('/chatbot-qa', [ChatbotQaController::class, 'store'])->name('chatbot-qa.store');
    Route::put('/chatbot-qa/{qa}', [ChatbotQaController::class, 'update'])->name('chatbot-qa.update');
    Route::delete('/chatbot-qa/{qa}', [ChatbotQaController::class, 'destroy'])->name('chatbot-qa.destroy');

    // Course approval
    Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses');
    Route::get('/courses/{course}/preview', [AdminCourseController::class, 'preview'])->name('courses.preview');
    Route::patch('/courses/{course}/approve', [AdminCourseController::class, 'approve'])->name('courses.approve');
    Route::patch('/courses/{course}/reject', [AdminCourseController::class, 'reject'])->name('courses.reject');
    Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');
});
