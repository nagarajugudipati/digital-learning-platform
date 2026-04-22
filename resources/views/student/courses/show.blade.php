@extends('layouts.student')

@section('title', $course->title . ' — Nabha Learning')

@section('student-content')
@php
    use Illuminate\Support\Str;

    /* ── Mock rating (deterministic, seeded by course ID) ── */
    $rating      = $course->mock_rating;
    $reviewCount = $course->mock_review_count;
    $fullStars   = (int) floor($rating);
    $halfStar    = ($rating - $fullStars) >= 0.5;
    $emptyStars  = 5 - $fullStars - ($halfStar ? 1 : 0);

    /* ── Mock reviews ── */
    $mockReviews = collect([
        ['name' => 'Priya Sharma',   'avatar' => 'PS', 'rating' => 5, 'date' => '2 weeks ago',
         'text'  => 'Absolutely fantastic course! The explanations are crystal-clear and the content is well-structured. I could follow along easily even as a beginner.'],
        ['name' => 'Arjun Mehta',    'avatar' => 'AM', 'rating' => 4, 'date' => '1 month ago',
         'text'  => 'Great content overall. The lessons are engaging and the teacher explains concepts very well. Would have loved a few more practice exercises.'],
        ['name' => 'Divya Nair',     'avatar' => 'DN', 'rating' => 5, 'date' => '3 weeks ago',
         'text'  => 'This course exceeded my expectations! The way topics are broken down made it very easy to understand. Highly recommend to anyone.'],
        ['name' => 'Rahul Verma',    'avatar' => 'RV', 'rating' => 4, 'date' => '2 months ago',
         'text'  => 'Solid course with great depth. Took detailed notes on every lesson. The instructor is clearly an expert in this subject.'],
        ['name' => 'Sneha Patel',    'avatar' => 'SP', 'rating' => 5, 'date' => '5 days ago',
         'text'  => 'One of the best learning experiences I\'ve had. Everything is well-explained and the pacing is just right. 10/10!'],
    ])->slice($course->id % 3, 3)->values(); // show 3 reviews, offset by course id
@endphp

<div class="space-y-6" x-data="{ payModal: false }">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('student.courses') }}" class="hover:text-indigo-600 transition">Courses</a>
        <span class="text-gray-300">›</span>
        <span class="text-gray-800 font-medium truncate">{{ Str::limit($course->title, 50) }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Left column ── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Hero banner --}}
            <div class="relative rounded-2xl overflow-hidden shadow-sm" style="height:260px;">
                <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                     class="w-full h-full object-cover scale-100 hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="bg-indigo-600 text-white text-xs font-semibold px-2.5 py-1 rounded-full">{{ $course->subject }}</span>
                        <span class="bg-white/20 text-white text-xs font-medium px-2.5 py-1 rounded-full backdrop-blur-sm">{{ $course->class_level }}</span>
                        @if($course->isFree())
                            <span class="bg-emerald-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">FREE</span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-bold text-white leading-snug">{{ $course->title }}</h1>

                    {{-- Inline rating on hero --}}
                    <div class="flex items-center gap-2 mt-2">
                        <div class="flex items-center gap-0.5">
                            @for($i = 0; $i < $fullStars; $i++)
                                <svg class="w-4 h-4 star-filled" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                            @if($halfStar)
                                <svg class="w-4 h-4 star-half" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" opacity=".4"/><path d="M10 2.927v14.144l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69L10 2.927z"/></svg>
                            @endif
                            @for($i = 0; $i < $emptyStars; $i++)
                                <svg class="w-4 h-4 star-empty" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <span class="text-white font-bold text-sm">{{ $rating }}</span>
                        <span class="text-white/60 text-xs">({{ number_format($reviewCount) }} ratings)</span>
                    </div>
                </div>
            </div>

            {{-- Stats strip --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4">
                <div class="flex flex-wrap gap-6 text-sm">
                    <div class="flex items-center gap-2 text-gray-600">
                        <span class="text-xl">📚</span>
                        <div>
                            <div class="font-bold text-gray-800 text-base">{{ $course->lessons->count() }}</div>
                            <div class="text-xs text-gray-400">Lessons</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <span class="text-xl">⏱</span>
                        <div>
                            <div class="font-bold text-gray-800 text-base">{{ $course->total_duration }}</div>
                            <div class="text-xs text-gray-400">Duration</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <span class="text-xl">👥</span>
                        <div>
                            <div class="font-bold text-gray-800 text-base">{{ $course->enrollments->count() }}</div>
                            <div class="text-xs text-gray-400">Students</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <span class="text-xl">⭐</span>
                        <div>
                            <div class="font-bold text-gray-800 text-base">{{ $rating }}</div>
                            <div class="text-xs text-gray-400">Rating</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <span class="text-xl">🌐</span>
                        <div>
                            <div class="font-bold text-gray-800 text-base">English</div>
                            <div class="text-xs text-gray-400">Language</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- About --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="font-bold text-gray-800 mb-3 text-lg">About this Course</h2>
                <p class="text-gray-600 text-sm leading-relaxed">{{ $course->description }}</p>
            </div>

            {{-- Instructor card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="font-bold text-gray-800 mb-4 text-lg">Your Instructor</h2>
                <div class="flex items-center gap-4">
                    <img src="{{ $course->teacher->avatar_url }}" alt="{{ $course->teacher->name }}"
                         class="w-16 h-16 rounded-full object-cover border-2 border-indigo-100 shadow-sm">
                    <div>
                        <p class="font-bold text-gray-800">{{ $course->teacher->name }}</p>
                        <p class="text-sm text-indigo-600">{{ $course->teacher->subject_specialization ?? 'Teacher' }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $course->teacher->school ?? 'Nabha School' }}</p>
                    </div>
                </div>
            </div>

            {{-- Curriculum --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-gray-800 text-lg">📋 Course Curriculum</h2>
                    <span class="text-xs text-gray-400">{{ $course->lessons->count() }} lessons</span>
                </div>

                @if($course->lessons->isEmpty())
                    <div class="p-8 text-center text-gray-400">
                        <p class="text-sm">No lessons added yet.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($course->lessons as $i => $lesson)
                            <div class="flex items-center gap-4 px-6 py-4 {{ $isEnrolled ? 'hover:bg-indigo-50/50 group transition-colors duration-150' : '' }}">
                                {{-- Number / Play --}}
                                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold transition-colors duration-150
                                            {{ $isEnrolled ? 'bg-indigo-100 text-indigo-700 group-hover:bg-indigo-600 group-hover:text-white' : 'bg-gray-100 text-gray-400' }}">
                                    {{ $isEnrolled ? '▶' : ($i + 1) }}
                                </div>

                                <div class="flex-1 min-w-0">
                                    @if($isEnrolled)
                                        <a href="{{ route('student.courses.lesson', [$course, $lesson]) }}"
                                           class="font-semibold text-sm text-gray-800 group-hover:text-indigo-700 transition block truncate">
                                            {{ $lesson->title }}
                                        </a>
                                    @else
                                        <span class="font-semibold text-sm text-gray-500 block truncate">{{ $lesson->title }}</span>
                                    @endif
                                    <p class="text-xs text-gray-400 truncate mt-0.5">{{ Str::limit($lesson->description, 60) }}</p>
                                </div>

                                <div class="flex items-center gap-3 flex-shrink-0">
                                    @if($lesson->duration_minutes)
                                        <span class="text-xs text-gray-400">{{ $lesson->duration_minutes }}m</span>
                                    @endif
                                    @if($isEnrolled)
                                        <span class="text-indigo-400 text-xs font-medium group-hover:text-indigo-600 transition">Open →</span>
                                    @else
                                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ── Student Reviews ── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-gray-800 text-lg">⭐ Student Reviews</h2>

                    {{-- Summary rating bar --}}
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-extrabold text-gray-800">{{ $rating }}</span>
                        <div>
                            <div class="flex items-center gap-0.5">
                                @for($i = 0; $i < $fullStars; $i++)
                                    <svg class="w-3.5 h-3.5 star-filled" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                                @if($halfStar)
                                    <svg class="w-3.5 h-3.5 star-half" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endif
                                @for($i = 0; $i < $emptyStars; $i++)
                                    <svg class="w-3.5 h-3.5 star-empty" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">{{ number_format($reviewCount) }} reviews</p>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-50">
                    @foreach($mockReviews as $review)
                        <div class="px-6 py-5">
                            <div class="flex items-start gap-4">
                                {{-- Avatar --}}
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    {{ $review['avatar'] }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-sm text-gray-800">{{ $review['name'] }}</span>
                                        <span class="text-xs text-gray-400">{{ $review['date'] }}</span>
                                    </div>
                                    {{-- Stars --}}
                                    <div class="flex items-center gap-0.5 mt-1">
                                        @for($s = 0; $s < $review['rating']; $s++)
                                            <svg class="w-3.5 h-3.5 star-filled" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                        @for($s = $review['rating']; $s < 5; $s++)
                                            <svg class="w-3.5 h-3.5 star-empty" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2 leading-relaxed">{{ $review['text'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ── Right column: enrollment/purchase card ── --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden sticky top-20">

                {{-- Thumbnail --}}
                <div class="relative h-36 overflow-hidden">
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/20"></div>
                </div>

                <div class="p-5 space-y-4">
                    {{-- Price --}}
                    <div class="text-center">
                        @if($course->isFree())
                            <div class="text-3xl font-extrabold text-emerald-600">Free</div>
                        @else
                            <div class="text-3xl font-extrabold text-gray-800">₹{{ number_format($course->price, 2) }}</div>
                            <p class="text-xs text-gray-400 mt-0.5">One-time purchase</p>
                        @endif
                    </div>

                    {{-- CTA --}}
                    @if($isEnrolled)
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 text-center">
                            <p class="text-emerald-700 font-semibold text-sm">✅ You're enrolled!</p>
                        </div>
                        @if($course->lessons->isNotEmpty())
                            @php
                                $target = $resumeLesson ?? $course->lessons->first();
                            @endphp
                            <a href="{{ route('student.courses.lesson', [$course, $target]) }}"
                               class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold transition shadow-sm shadow-indigo-200">
                                {{ $resumeLesson ? '▶ Resume Learning' : 'Start Learning →' }}
                            </a>
                            @if($resumeLesson && $resumeLesson->id !== $course->lessons->first()->id)
                                <p class="text-center text-xs text-gray-400 -mt-2">
                                    Picking up at <em>{{ Str::limit($resumeLesson->title, 30) }}</em>
                                </p>
                            @endif
                        @endif
                    @elseif($course->isFree())
                        <form method="POST" action="{{ route('student.courses.enroll', $course) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold transition shadow-sm shadow-emerald-200">
                                🎓 Enroll for Free
                            </button>
                        </form>
                    @else
                        {{-- Paid, not enrolled --}}
                        @if($inCart)
                            <a href="{{ route('student.cart') }}"
                               class="block w-full text-center bg-amber-500 hover:bg-amber-600 text-white py-3 rounded-xl font-bold transition shadow-sm shadow-amber-200">
                                🛒 Go to Cart
                            </a>
                            <form method="POST" action="{{ route('student.cart.remove', $course) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-full text-sm text-gray-500 hover:text-red-600 py-2 transition">
                                    Remove from cart
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('student.cart.add', $course) }}">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-amber-500 hover:bg-amber-600 text-white py-3 rounded-xl font-bold transition shadow-sm shadow-amber-200">
                                    🛒 Add to Cart
                                </button>
                            </form>
                        @endif
                        <button @click="payModal = true"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold transition shadow-sm shadow-indigo-200">
                            💳 Buy Now — ₹{{ number_format($course->price, 2) }}
                        </button>
                    @endif

                    {{-- Course includes --}}
                    <div class="border-t border-gray-100 pt-4 space-y-2.5 text-sm text-gray-600">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">This course includes</p>
                        <div class="flex items-center gap-2.5"><span>📚</span> {{ $course->lessons->count() }} lessons</div>
                        <div class="flex items-center gap-2.5"><span>⏱</span> {{ $course->total_duration }}</div>
                        <div class="flex items-center gap-2.5"><span>🎓</span> {{ $course->class_level }}</div>
                        <div class="flex items-center gap-2.5"><span>📖</span> {{ $course->subject }}</div>
                        <div class="flex items-center gap-2.5"><span>♾️</span> Lifetime access</div>
                        <div class="flex items-center gap-2.5"><span>⭐</span> {{ $rating }} course rating</div>
                        @if(!$course->isFree())
                            <div class="flex items-center gap-2.5"><span>🏆</span> Certificate of completion</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Payment Modal ── --}}
    @if(!$course->isFree() && !$isEnrolled)
    <div x-show="payModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="payModal = false"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="font-bold text-gray-800">Complete Purchase</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Simulated payment — no real charges</p>
                </div>
                <button @click="payModal = false" class="text-gray-400 hover:text-gray-600 transition p-1 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mx-6 mt-4 bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex items-center justify-between">
                <div>
                    <p class="text-xs text-indigo-400 font-medium">Enrolling in</p>
                    <p class="font-semibold text-indigo-800 text-sm">{{ Str::limit($course->title, 40) }}</p>
                </div>
                <p class="text-xl font-extrabold text-indigo-700">₹{{ number_format($course->price, 2) }}</p>
            </div>

            <form method="POST" action="{{ route('student.courses.purchase', $course) }}"
                  class="px-6 pb-6 pt-4 space-y-4"
                  x-data="paymentForm()"
                  @submit="loading = true">
                @csrf

                {{-- Payment method selector --}}
                <div>
                    <p class="text-xs font-semibold text-gray-600 mb-2">Payment Method</p>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2.5 border rounded-xl px-4 py-3 cursor-pointer transition"
                               :class="method === 'card' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="payment_method" value="card" x-model="method" class="accent-indigo-600">
                            <span class="text-lg">💳</span>
                            <span class="text-sm font-semibold text-gray-700">Card</span>
                        </label>
                        <label class="flex items-center gap-2.5 border rounded-xl px-4 py-3 cursor-pointer transition"
                               :class="method === 'upi' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="payment_method" value="upi" x-model="method" class="accent-indigo-600">
                            <span class="text-lg">📱</span>
                            <span class="text-sm font-semibold text-gray-700">UPI</span>
                        </label>
                    </div>
                </div>

                {{-- Card fields --}}
                <div x-show="method === 'card'" x-transition class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cardholder Name</label>
                        <input type="text" name="card_name" x-model="name"
                               placeholder="Name on card"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Card Number</label>
                        <div class="relative">
                            <input type="text" x-model="cardDisplay"
                                   @input="formatCard($event)"
                                   placeholder="1234 5678 9012 3456" maxlength="19"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 pr-12 tracking-widest transition">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 text-lg">💳</div>
                        </div>
                        <input type="hidden" name="card_number" :value="cardRaw">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Expiry</label>
                            <input type="text" name="card_expiry" @input="formatExpiry($event)"
                                   placeholder="MM/YY" maxlength="5"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 tracking-widest transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">CVV</label>
                            <input type="text" name="card_cvv" placeholder="•••" maxlength="4"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 tracking-widest transition">
                        </div>
                    </div>
                </div>

                {{-- UPI section --}}
                <div x-show="method === 'upi'" x-transition class="space-y-3">
                    <div class="flex flex-col items-center gap-3 py-2">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?data=upi://pay?pa=8247592083@axl%26am={{ $course->price }}%26cu=INR&size=180x180&bgcolor=ffffff&color=4f46e5&margin=8"
                             alt="UPI QR Code"
                             class="w-44 h-44 rounded-xl border border-indigo-100 shadow-sm">
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-0.5">Scan to Pay</p>
                            <p class="text-lg font-extrabold text-indigo-700">₹{{ number_format($course->price, 2) }}</p>
                        </div>
                        <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-5 py-3 text-center">
                            <p class="text-xs text-gray-500 mb-0.5">UPI ID</p>
                            <p class="text-sm font-bold text-indigo-800 tracking-wide select-all">8247592083@axl</p>
                        </div>
                    </div>
                    <input type="hidden" name="upi_id" value="8247592083@axl">
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 text-xs text-blue-700">
                        Scan the QR code or use the UPI ID above to complete payment. This is a simulated demo — no real money is charged.
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-700 flex gap-2">
                    <span class="flex-shrink-0">⚠️</span>
                    <span><strong>Demo only.</strong> No real money is charged.</span>
                </div>

                <button type="submit" :disabled="loading"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed text-white py-3 rounded-xl font-bold transition flex items-center justify-center gap-2 shadow-sm shadow-indigo-200">
                    <svg x-show="loading" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-show="!loading" x-text="method === 'upi' ? '📱 Pay via UPI & Enroll' : '🔒 Pay ₹{{ number_format($course->price, 2) }} & Enroll'"></span>
                    <span x-show="loading" x-cloak>Processing...</span>
                </button>

                <p class="text-center text-xs text-gray-400">🔒 Secured simulation</p>
            </form>
        </div>
    </div>
    @endif

</div>

@if($errors->hasAny(['card_number','card_expiry','card_cvv','card_name','upi_id','payment_method']))
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const root = document.querySelector('[x-data]');
            if (root && root._x_dataStack) root._x_dataStack[0].payModal = true;
        });
    </script>
    @endpush
@endif

@push('scripts')
<script>
function paymentForm() {
    return {
        method: '{{ old("payment_method", "card") }}',
        loading: false,
        name: '',
        cardDisplay: '',
        cardRaw: '',
        formatCard(e) {
            const digits = e.target.value.replace(/\D/g, '').slice(0, 16);
            this.cardRaw     = digits;
            this.cardDisplay = digits.replace(/(.{4})/g, '$1 ').trim();
            e.target.value   = this.cardDisplay;
        },
        formatExpiry(e) {
            let v = e.target.value.replace(/\D/g, '').slice(0, 4);
            if (v.length >= 3) v = v.slice(0, 2) + '/' + v.slice(2);
            e.target.value = v;
        },
    };
}
</script>
@endpush
@endsection
