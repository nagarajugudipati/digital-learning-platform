@extends('layouts.student')
@php use Illuminate\Support\Str; @endphp

@section('title', 'My Cart — Nabha Learning')

@section('student-content')
<div class="space-y-6" x-data="{ payModal: false }">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('student.courses') }}" class="hover:text-indigo-600 transition">Courses</a>
        <span class="text-gray-300">›</span>
        <span class="text-gray-800 font-medium">My Cart</span>
    </nav>

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">🛒 My Cart
            @if($cartItems->isNotEmpty())
                <span class="text-base font-normal text-gray-400 ml-2">({{ $cartItems->count() }} {{ Str::plural('course', $cartItems->count()) }})</span>
            @endif
        </h1>
        @if($cartItems->isNotEmpty())
            <a href="{{ route('student.courses') }}" class="text-sm text-indigo-600 hover:underline">+ Add more courses</a>
        @endif
    </div>

    @if($cartItems->isEmpty())
        {{-- Empty state --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
            <div class="text-6xl mb-4">🛒</div>
            <h2 class="text-xl font-bold text-gray-700 mb-2">Your cart is empty</h2>
            <p class="text-gray-400 text-sm mb-6">Browse our catalog and add courses you'd like to learn.</p>
            <a href="{{ route('student.courses') }}"
               class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-bold transition shadow-sm shadow-indigo-200">
                Browse Courses
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ── Cart items list ── --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                    @php $course = $item->course; @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex gap-4">
                        {{-- Thumbnail --}}
                        <a href="{{ route('student.courses.show', $course) }}"
                           class="flex-shrink-0 w-28 h-20 rounded-xl overflow-hidden block">
                            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        </a>

                        <div class="flex-1 min-w-0">
                            <a href="{{ route('student.courses.show', $course) }}"
                               class="font-bold text-gray-800 hover:text-indigo-600 transition text-sm leading-snug block">
                                {{ $course->title }}
                            </a>
                            <p class="text-xs text-gray-400 mt-1">
                                by {{ $course->teacher->name }}
                                &middot; {{ $course->lessons_count }} {{ Str::plural('lesson', $course->lessons_count) }}
                                &middot; {{ $course->class_level }}
                            </p>
                            <div class="flex items-center gap-1 mt-1.5">
                                <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full font-medium">{{ $course->subject }}</span>
                            </div>
                        </div>

                        <div class="flex flex-col items-end justify-between flex-shrink-0">
                            <p class="text-lg font-extrabold text-gray-800">₹{{ number_format($course->price, 2) }}</p>
                            <form method="POST" action="{{ route('student.cart.remove', $course) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs text-red-400 hover:text-red-600 transition font-medium hover:underline">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ── Order summary + checkout ── --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 sticky top-20 space-y-5">
                    <h2 class="font-bold text-gray-800 text-lg">Order Summary</h2>

                    <div class="space-y-2 text-sm">
                        @foreach($cartItems as $item)
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-gray-600 truncate flex-1 text-xs">{{ Str::limit($item->course->title, 32) }}</span>
                                <span class="text-gray-800 font-semibold flex-shrink-0">₹{{ number_format($item->course->price, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 pt-4 flex items-center justify-between">
                        <span class="font-bold text-gray-700">Total</span>
                        <span class="text-2xl font-extrabold text-gray-800">₹{{ number_format($total, 2) }}</span>
                    </div>

                    <button @click="payModal = true"
                            class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold transition shadow-sm shadow-indigo-200">
                        🔒 Checkout ({{ $cartItems->count() }} {{ Str::plural('course', $cartItems->count()) }})
                    </button>

                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-700 flex gap-2">
                        <span class="flex-shrink-0">⚠️</span>
                        <span><strong>Demo only.</strong> No real payment is processed.</span>
                    </div>

                    <p class="text-center text-xs text-gray-400">🔒 Safe & Secure Simulation</p>
                </div>
            </div>

        </div>
    @endif

    {{-- ── Payment Modal ── --}}
    @if($cartItems->isNotEmpty())
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
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $cartItems->count() }} {{ Str::plural('course', $cartItems->count()) }}
                        &middot; Total ₹{{ number_format($total, 2) }}
                    </p>
                </div>
                <button @click="payModal = false" class="text-gray-400 hover:text-gray-600 transition p-1 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Mini course list inside modal --}}
            <div class="mx-6 mt-4 bg-indigo-50 border border-indigo-100 rounded-xl p-3 space-y-1.5 max-h-32 overflow-y-auto">
                @foreach($cartItems as $item)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-indigo-700 truncate flex-1 pr-2">{{ Str::limit($item->course->title, 36) }}</span>
                        <span class="font-bold text-indigo-800 flex-shrink-0">₹{{ number_format($item->course->price, 2) }}</span>
                    </div>
                @endforeach
                <div class="border-t border-indigo-200 pt-1.5 flex items-center justify-between text-xs font-bold">
                    <span class="text-indigo-700">Total</span>
                    <span class="text-indigo-800 text-sm">₹{{ number_format($total, 2) }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('student.cart.checkout') }}"
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
                        <img src="https://api.qrserver.com/v1/create-qr-code/?data=upi://pay?pa=8247592083@axl%26am={{ $total }}%26cu=INR&size=180x180&bgcolor=ffffff&color=4f46e5&margin=8"
                             alt="UPI QR Code"
                             class="w-44 h-44 rounded-xl border border-indigo-100 shadow-sm">
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-0.5">Scan to Pay</p>
                            <p class="text-lg font-extrabold text-indigo-700">₹{{ number_format($total, 2) }}</p>
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
                    <span x-show="!loading" x-text="method === 'upi' ? '📱 Pay via UPI & Enroll All' : '🔒 Pay ₹{{ number_format($total, 2) }} & Enroll All'"></span>
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
