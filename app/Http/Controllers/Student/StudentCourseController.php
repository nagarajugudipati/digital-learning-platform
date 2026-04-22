<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\ProgressReport;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StudentCourseController extends Controller
{
    public function __construct(private EnrollmentService $enrollment) {}

    // ─── Course Catalog ───────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Course::published()->with('teacher')->withCount('lessons', 'enrollments');

        if ($s = $request->subject) {
            $query->where('subject', $s);
        }
        if ($cl = $request->class_level) {
            $query->where('class_level', $cl);
        }
        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses  = $query->latest()->paginate(12);
        $enrolled = auth()->user()->enrolledCourses()->pluck('courses.id')->toArray();

        // Cart IDs so we can show "In Cart" badge on listing cards
        $inCart = CartItem::where('user_id', auth()->id())->pluck('course_id')->toArray();

        $subjects    = Cache::remember('catalog.subjects', 300, fn () =>
            Course::published()->distinct()->pluck('subject')->filter()->sort()->values()
        );
        $classLevels = Cache::remember('catalog.class_levels', 300, fn () =>
            Course::published()->distinct()->pluck('class_level')->filter()->sort()->values()
        );

        return view('student.courses.index', compact('courses', 'enrolled', 'inCart', 'subjects', 'classLevels'));
    }

    // ─── Course Detail ────────────────────────────────────────────────────────

    public function show(Course $course)
    {
        abort_if(!$course->isPublished(), 404);

        $course->load([
            'teacher',
            'enrollments',
            'lessons' => fn ($q) => $q->orderBy('order'),
        ]);

        $isEnrolled   = $this->enrollment->isEnrolled(auth()->user(), $course);
        $resumeLesson = $isEnrolled ? $course->lastAccessedLesson(auth()->id()) : null;
        $inCart       = CartItem::where('user_id', auth()->id())
                            ->where('course_id', $course->id)
                            ->exists();

        return view('student.courses.show', compact('course', 'isEnrolled', 'resumeLesson', 'inCart'));
    }

    // ─── Enrollment (free) ────────────────────────────────────────────────────

    public function enroll(Course $course)
    {
        abort_if(!$course->isPublished(), 404);

        if ($this->enrollment->isEnrolled(auth()->user(), $course)) {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        if (!$course->isFree()) {
            return redirect()->route('student.courses.show', $course)
                ->with('error', 'Please complete the payment to enroll in this course.');
        }

        $this->enrollment->enrollFree(auth()->user(), $course);

        return redirect()->route('student.courses.show', $course)
            ->with('success', "You are now enrolled in \"{$course->title}\"!");
    }

    // ─── Direct Purchase (Buy Now, single course) ─────────────────────────────

    public function purchase(Request $request, Course $course)
    {
        abort_if(!$course->isPublished(), 404);
        abort_if($course->isFree(), 400);

        if ($this->enrollment->isEnrolled(auth()->user(), $course)) {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        $request->validate([
            'payment_method' => ['required', 'in:card,upi'],
            // Card fields — required only when paying by card
            'card_number' => ['required_if:payment_method,card', 'nullable', 'digits:16'],
            'card_expiry' => ['required_if:payment_method,card', 'nullable', 'string', 'regex:/^\d{2}\/\d{2}$/'],
            'card_cvv'    => ['required_if:payment_method,card', 'nullable', 'digits_between:3,4'],
            'card_name'   => ['required_if:payment_method,card', 'nullable', 'string', 'max:100'],
            // UPI field — required only when paying by UPI
            'upi_id'      => ['required_if:payment_method,upi', 'nullable', 'string', 'max:100',
                              'regex:/^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/'],
        ]);

        $this->enrollment->enrollPaid(auth()->user(), $course);

        // Remove from cart if it was there
        CartItem::where('user_id', auth()->id())->where('course_id', $course->id)->delete();

        $method = $request->payment_method === 'upi' ? 'UPI' : 'Card';
        return redirect()->route('student.my-courses')
            ->with('success', "Payment via {$method} successful! You are now enrolled in \"{$course->title}\". Start learning now! 🎓");
    }

    // ─── Cart ─────────────────────────────────────────────────────────────────

    public function addToCart(Course $course)
    {
        abort_if(!$course->isPublished(), 404);

        if ($course->isFree()) {
            return back()->with('error', 'Free courses can be enrolled directly — no cart needed.');
        }

        if ($this->enrollment->isEnrolled(auth()->user(), $course)) {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        CartItem::firstOrCreate([
            'user_id'   => auth()->id(),
            'course_id' => $course->id,
        ]);

        return back()->with('success', "\"{$course->title}\" added to your cart!");
    }

    public function removeFromCart(Course $course)
    {
        CartItem::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->delete();

        return back()->with('success', 'Course removed from cart.');
    }

    public function viewCart()
    {
        // Load cart items, filter out any that are no longer published
        $cartItems = CartItem::where('user_id', auth()->id())
            ->with(['course' => fn ($q) => $q->with('teacher')->withCount('lessons')])
            ->latest()
            ->get()
            ->filter(fn ($item) => $item->course && $item->course->isPublished())
            ->values();

        // Remove stale items (unpublished/deleted courses)
        $validIds = $cartItems->pluck('course_id');
        CartItem::where('user_id', auth()->id())
            ->whereNotIn('course_id', $validIds)
            ->delete();

        $total = $cartItems->sum(fn ($item) => (float) $item->course->price);

        return view('student.cart', compact('cartItems', 'total'));
    }

    public function checkout(Request $request)
    {
        $cartItems = CartItem::where('user_id', auth()->id())
            ->with('course')
            ->get()
            ->filter(fn ($item) => $item->course && $item->course->isPublished())
            ->values();

        if ($cartItems->isEmpty()) {
            return redirect()->route('student.cart')->with('error', 'Your cart is empty.');
        }

        $request->validate([
            'payment_method' => ['required', 'in:card,upi'],
            'card_number' => ['required_if:payment_method,card', 'nullable', 'digits:16'],
            'card_expiry' => ['required_if:payment_method,card', 'nullable', 'string', 'regex:/^\d{2}\/\d{2}$/'],
            'card_cvv'    => ['required_if:payment_method,card', 'nullable', 'digits_between:3,4'],
            'card_name'   => ['required_if:payment_method,card', 'nullable', 'string', 'max:100'],
            'upi_id'      => ['required_if:payment_method,upi', 'nullable', 'string', 'max:100',
                              'regex:/^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/'],
        ]);

        $enrolled = 0;
        foreach ($cartItems as $item) {
            if (!$this->enrollment->isEnrolled(auth()->user(), $item->course)) {
                $this->enrollment->enrollPaid(auth()->user(), $item->course);
                $enrolled++;
            }
        }

        // Clear the entire cart
        CartItem::where('user_id', auth()->id())->delete();

        $method = $request->payment_method === 'upi' ? 'UPI' : 'Card';
        $msg = $enrolled === 1
            ? "Payment via {$method} successful! You are now enrolled in 1 course."
            : "Payment via {$method} successful! You are now enrolled in {$enrolled} courses.";

        return redirect()->route('student.my-courses')->with('success', $msg);
    }

    // ─── My Courses ───────────────────────────────────────────────────────────

    public function myCourses()
    {
        $enrollments = auth()->user()
            ->enrollments()
            ->with(['course' => fn ($q) => $q->withCount('lessons')->with('teacher')])
            ->latest()
            ->get();

        return view('student.courses.my-courses', compact('enrollments'));
    }

    // ─── Lesson Player ────────────────────────────────────────────────────────

    public function lesson(Course $course, Lesson $lesson)
    {
        abort_if(!$course->isPublished(), 404);
        abort_if($lesson->course_id !== $course->id, 404);

        if (!$this->enrollment->isEnrolled(auth()->user(), $course)) {
            return redirect()->route('student.courses.show', $course)
                ->with('error', 'You must enroll in this course to access lessons.');
        }

        $lesson->load('contents');
        $lesson->incrementViews();

        $allLessons = $course->lessons()->orderBy('order')->get();
        $currentIdx = $allLessons->search(fn ($l) => $l->id === $lesson->id);
        $prev       = $currentIdx > 0 ? $allLessons[$currentIdx - 1] : null;
        $next       = $currentIdx < $allLessons->count() - 1 ? $allLessons[$currentIdx + 1] : null;

        $isCompleted = ProgressReport::where('lesson_id', $lesson->id)
            ->where('student_id', auth()->id())
            ->where('is_completed', true)
            ->exists();

        $completedIds = ProgressReport::whereIn('lesson_id', $allLessons->pluck('id'))
            ->where('student_id', auth()->id())
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        return view('student.courses.lesson', compact(
            'course', 'lesson', 'allLessons', 'prev', 'next',
            'currentIdx', 'isCompleted', 'completedIds'
        ));
    }

    public function completeLesson(Course $course, Lesson $lesson)
    {
        abort_if($lesson->course_id !== $course->id, 404);

        if (!$this->enrollment->isEnrolled(auth()->user(), $course)) {
            return response()->json(['error' => 'Not enrolled'], 403);
        }

        $report = ProgressReport::firstOrCreate(
            ['lesson_id' => $lesson->id, 'student_id' => auth()->id()],
            ['completion_percentage' => 0, 'views' => 0]
        );

        if (!$report->is_completed) {
            $report->markCompleted();
        }

        $allLessons = $course->lessons()->orderBy('order')->get();
        $currentIdx = $allLessons->search(fn ($l) => $l->id === $lesson->id);
        $next       = $currentIdx < $allLessons->count() - 1 ? $allLessons[$currentIdx + 1] : null;

        return response()->json([
            'completed' => true,
            'next_url'  => $next ? route('student.courses.lesson', [$course, $next]) : null,
        ]);
    }
}
