<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is deactivated. Contact admin.']);
            }

            // Teacher approval check
            if ($user->isTeacher() && !$user->isApprovedTeacher()) {
                Auth::logout();
                $message = match($user->status) {
                    'rejected' => 'Your teacher account has been rejected. Please contact admin.',
                    default    => 'Your account is waiting for admin approval. Please check back later.',
                };
                return back()->withErrors(['email' => $message]);
            }

            // Update login streak for students
            if ($user->isStudent()) {
                $user->updateStreak();
            }

            return $this->redirectToDashboard();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'                   => ['required', 'string', 'max:100'],
            'email'                  => ['required', 'email', 'unique:users'],
            'password'               => ['required', 'confirmed', Password::min(6)],
            'role'                   => ['required', 'in:student,teacher'],
            'class_level'            => ['required_if:role,student', 'nullable', 'string'],
            'subject_specialization' => ['required_if:role,teacher', 'nullable', 'string'],
            'phone'                  => ['nullable', 'string', 'max:15'],
        ]);

        $isTeacher = $validated['role'] === 'teacher';

        $user = User::create([
            'name'                   => $validated['name'],
            'email'                  => $validated['email'],
            'password'               => Hash::make($validated['password']),
            'role'                   => $validated['role'],
            'class_level'            => $validated['class_level'] ?? null,
            'subject_specialization' => $validated['subject_specialization'] ?? null,
            'phone'                  => $validated['phone'] ?? null,
            'is_active'              => true,
            'status'                 => $isTeacher ? 'pending' : null,
        ]);

        if ($isTeacher) {
            // Don't log in — redirect to login with message
            return redirect()->route('login')->with(
                'success',
                'Registration submitted! Your teacher account is pending admin approval. You will be notified once approved.'
            );
        }

        Auth::login($user);
        $request->session()->regenerate();
        $user->updateStreak();

        return $this->redirectToDashboard()->with('success', "Welcome to Nabha Digital Learning, {$user->name}!");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    private function redirectToDashboard()
    {
        return match(Auth::user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            default   => redirect()->route('student.dashboard'),
        };
    }
}
